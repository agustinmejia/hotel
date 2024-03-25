<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Person;
use App\Models\City;
use App\Models\PersonDefaulter;
use App\Models\PersonDefaulterPayment;
use App\Models\CashierDetail;
use App\Models\ReservationDetail;

class PeopleController extends Controller
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
        //
    }

    public function list(){
        $this->custom_authorize('browse_people');
        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $data = Person::with(['reservations' => function($q){
                        $q->where('status', 'en curso');
                    }, 'city.state.country', 'defaulters'])
                    ->where(function($query) use ($search){
                        if($search){
                            $query->OrwhereHas('city', function($q) use($search){
                                $q->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('city.state', function($q) use($search){
                                $q->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('city.state.country', function($q) use($search){
                                $q->whereRaw("name like '%$search%'");
                            })
                            ->OrWhereRaw("(full_name like '%$search%' or dni like '%$search%' or phone like '%$search%')");
                        }
                    })
                    ->where('deleted_at', NULL)->orderBy('id', 'DESC')->paginate($paginate);
        return view('vendor.voyager.people.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $city_id = 1;
            if($request->city_id){
                $city_id = $request->city_id;
            }elseif($request->city_name){
                $city_id = City::create([
                    'state_id' => 1,
                    'name' => $request->city_name
                ])->id;
            }

            Person::create([
                'full_name' => $request->full_name,
                'dni' => $request->dni,
                'phone' => $request->phone,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'city_id' => $city_id,
                'job' => $request->job,
                'gender' => $request->gender,
                'photo' => $request->photo
            ]);

            return response()->json(['success' => 1]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 1]);
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
        //
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

    public function search() {
        $q = request('q');
        $data = [];
        if ($q) {
            $data = Person::with(['defaulters' => function($q){
                $q->where('status', 'pendiente');
            }])->whereRaw('(dni like "%'.$q.'%" or full_name like "%'.$q.'%" or phone like "%'.$q.'%")')->get();
        }
        return response()->json($data);
    }

    public function defaulters_payment_store(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            if(!$request->cashier_id){
                return redirect()->to($redirect)->with(['message' => 'Debe abrir caja primero', 'alert-type' => 'error']);
            }
            $person_defaulter = PersonDefaulter::find($request->id);
            $person_defaulter->status = 'pagada';
            $person_defaulter->update();

            // Agregar pago de deuda
            PersonDefaulterPayment::create([
                'person_defaulter_id' => $person_defaulter->id,
                'user_id' => Auth::user()->id,
                'amount' => $person_defaulter->amount
            ]);
    
            // Registro en caja
            CashierDetail::create([
                'cashier_id' => $request->cashier_id,
                'type' => 'ingreso',
                'amount' => $person_defaulter->amount,
                'cash' => $request->payment_qr ? 0 : 1,
                'observations' => 'Pago de deuda atrasada de '.$person_defaulter->person->full_name
            ]);

            // =====================================================================
            // Actualizar datos del hospedaje (dias, ventas, multas)
            $reservation_detail = ReservationDetail::with(['reservation', 'room', 'days.payments', 'sales.details'])->where('id', $person_defaulter->reservation_detail_id)->first();

            // Pago de ventas pendientes
            $reservation_detail->sales->each(function ($sale) use($request) {
                $sale->update(['status' => 'pagada']);
                $sale->details->each(function ($detail) use($request, &$amount_debt_total) {
                    if ($detail->status == 'deuda') {
                        $detail->update(['status' => 'pagado']);
                    }
                });
            });

            // Pago de registros de hospedaje
            $reservation_detail->days->each(function ($day) use($request, &$amount_debt_total) {
                if ($day->status == 'deuda') {
                    $day->update(['status' => 'pagado']);
                }
            });

            // Pago de multas
            $reservation_detail->penalties->each(function ($penalty) use($request, &$amount_debt_total) {
                if ($penalty->status == 'deuda') {
                    $penalty->update(['status' => 'pagada']);
                }
            });

            $reservation_detail->update();
            // =====================================================================

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Pago registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
