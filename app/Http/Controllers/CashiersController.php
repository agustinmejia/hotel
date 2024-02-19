<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

// Models
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\CashierDetailAmount;
use App\Models\User;
use App\Models\Room;

class CashiersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->custom_authorize('browse_cashiers');
        return view('cashiers.browse');
    }

    public function list(){
        $this->custom_authorize('browse_cashiers');
        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $status = request('status') ?? null;
        $data = Cashier::with(['user', 'branch_office', 'details'])
                    ->where(function($query) use ($search){
                        if($search){
                            $query->OrwhereHas('user', function($query) use($search){
                                $query->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('branch_office', function($query) use($search){
                                $query->whereRaw("name like '%$search%'");
                            });
                        }
                    })
                    ->whereRaw($status ? "status = '$status'" : 1)
                    ->whereRaw(Auth::user()->role_id == 3 ? "user_id = ".Auth::user()->id : 1)
                    ->where('deleted_at', NULL)->orderBy('id', 'DESC')->paginate($paginate);
        return view('cashiers.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->custom_authorize('add_cashiers');
        return view('cashiers.edit-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        DB::beginTransaction();
        try {
            // Verificar si no tiene una caja abierta
            $cashier = Cashier::where([
                'user_id' => $request->user_id,
                'status' => 'abierta'
            ])->first();
            if($cashier){
                return redirect()->to($request->redirect ?? 'admin/cashiers')->with(['message' => 'Ya tiene una caja abierta, debe cerrarla', 'alert-type' => 'warning']);
            }

            $cashier = Cashier::create([
                'user_id' => $request->user_id,
                'branch_office_id' => $request->branch_office_id,
                'status' => 'abierta'
            ]);

            // Si se registró un monto de apertura
            if ($request->initial_amount) {
                CashierDetail::create([
                    'cashier_id' => $cashier->id,
                    'type' => 'ingreso',
                    'amount' => $request->initial_amount,
                    'observations' => $request->observations ?? 'Apertura de caja'
                ]);
            }

            // Update user
            $user = User::find(Auth::user()->id);
            $user->branch_office_id = $request->branch_office_id;
            $user->update();

            DB::commit();
            return redirect()->to($request->redirect ?? 'admin/cashiers')->with(['message' => 'Caja aperturada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($request->redirect ?? 'admin/cashiers')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->custom_authorize('read_cashiers');
        $cashier = Cashier::with(['details.sale_detail.product', 'details.service', 'details.reservation_detail_day.reservation_detail.room', 'user', 'branch_office'])->where('id', $id)->first();
        return view('cashiers.read', compact('cashier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $cashier = Cashier::with(['details'])->where('id', $id)->first();
            if($cashier->details->count() > 0){
                return redirect()->route('cashiers.index')->with(['message' => 'Ya se hizo movimientos de caja, debe cerrarla', 'alert-type' => 'success']);
            }
            $cashier->status = 'eliminada';
            $cashier->update();
            $cashier->delete();
            return redirect()->route('cashiers.index')->with(['message' => 'Caja eliminada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            return redirect()->route('cashiers.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function close_index($id){
        $cashier = Cashier::with(['details', 'user', 'branch_office'])->where('id', $id)->first();
        return view('cashiers.close', compact('cashier'));
    }

    public function close_store($id, Request $request){
        DB::beginTransaction();
        try {
            // Update cashier
            $cashier = Cashier::find($id);
            $cashier->amount_total = $request->amount_total;
            $cashier->amount_real = $request->amount_real;
            $cashier->amount_surplus = $request->amount_surplus;
            $cashier->amount_missing = $request->amount_missing;
            $cashier->status = 'cerrada';
            $cashier->rooms_available = Room::where('status', 'disponible')->count();
            $cashier->rooms_occupied = Room::where('status', 'ocupada')->count();
            $cashier->rooms_dirty = Room::where('status', 'limpieza')->count();
            $cashier->closed_at = Carbon::now();
            $cashier->update();

            // Registrar los cortes de billete
            for ($i=0; $i < count($request->cash_value); $i++) { 
                if ($request->quantity[$i]) {
                    CashierDetailAmount::create([
                        'cashier_id' => $id,
                        'amount' => $request->cash_value[$i],
                        'quantity' => $request->quantity[$i]
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('cashiers.show', $id)->with(['message' => 'Caja cerrada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cashiers.show', $id)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function print($id){
        $cashier = Cashier::with(['details', 'user', 'branch_office'])->where('id', $id)->first();
        return view('cashiers.print', compact('cashier'));
    }

    public function add_register(Request $request){
        DB::beginTransaction();

        if($request->type == 'egreso'){
            $cashier = Cashier::find($request->id);
            $total_amount = $cashier->details->where('cash', 1)->where('type', 'ingreso')->sum('amount') - $cashier->details->where('cash', 1)->where('type', 'egreso')->sum('amount');
            if($request->amount > $total_amount){
                return redirect()->route('cashiers.index')->with(['message' => 'El monto de egreso sobrepasa el efectivo en caja', 'alert-type' => 'warning']);
            }
        }

        try {
            CashierDetail::create([
                'cashier_id' => $request->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'observations' => $request->observations
            ]);
            DB::commit();
            return redirect()->route('cashiers.index')->with(['message' => Str::ucfirst($request->type).' registrado correctamente', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('cashiers.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
