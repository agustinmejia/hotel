<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\User;

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
        // dd($request->all());
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
            //throw $th;
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
        $cashier = Cashier::with(['details', 'user', 'branch_office'])->where('id', $id)->first();
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
        //
    }
}
