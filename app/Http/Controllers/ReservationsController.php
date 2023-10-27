<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Room;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use App\Models\ReservationDetailAccessory;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\ReservationDetailDay;
use App\Models\Cashier;
use App\Models\CashierDetail;

class ReservationsController extends Controller
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
        $this->custom_authorize('browse_reservations');
        return view('reservations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->custom_authorize('add_reservations');
        $room_id = request('room_id');
        $room = $room_id ? Room::find($room_id) : null;
        return view('reservations.edit-add', compact('room'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $reservation = Reservation::create([
                'user_id' => Auth::user()->id,
                'person_id' => $request->person_id,
                'start' => $request->start,
                'finish' => $request->finish,
                'observation' => $request->observation,
                'status' => 'en curso'
            ]);

            // Lista de habitaciones
            $detail = ReservationDetail::create([
                'reservation_id' => $reservation->id,
                'room_id' => $request->room_id,
                'price' => $request->room_price
            ]);
            Room::where('id', $request->room_id)->update(['status' => 'ocupada']);
            // Lista de accesorios
            $total_accessories = 0;
            if ($request->accessory_id) {
                for ($i=0; $i < count($request->accessory_id); $i++) { 
                    ReservationDetailAccessory::create([
                        'reservation_detail_id' => $detail->id,
                        'room_accessory_id' => $request->accessory_id[$i],
                        'price' => $request->price[$i]
                    ]);
                    $total_accessories += $request->price[$i];
                }
            }

            // Calendario de ocupación
            $start = $request->start;
            $finish = $request->finish ?? date('Y-m-d');

            while ($start <= $finish) {
                ReservationDetailDay::create([
                    'reservation_detail_id' => $detail->id,
                    'date' => $start,
                    'amount' => $request->room_price + $total_accessories
                ]);
                $start = date('Y-m-d', strtotime($start.' +1 days'));
            }


            // Falta guardar el adelanto si lo diera (parámetro recibido "initial_amount")

            DB::commit();
            return redirect()->route('reservations.index')->with(['message' => 'Hospedaje registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            // throw $th;
            return redirect()->route('reservations.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
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
        $this->custom_authorize('read_reservations');
        $room = Room::with(['reservation_detail' => function($q){
                        $q->where('status', 'ocupada');
                    }, 'reservation_detail.reservation', 'reservation_detail.accessories.accessory', 'reservation_detail.days', 'reservation_detail.reservation', 'reservation_detail.sales.details.product'])
                    ->where('id', $id)->first();
        $cashier = Cashier::where('user_id', Auth::user()->id)->where('status', 'abierta')->first();
        return view('reservations.read', compact('room', 'cashier'));
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

    public function product_store(Request $request){
        DB::beginTransaction();
        $reservation_detail = ReservationDetail::find($request->reservation_detail_id);
        $request->pay = $request->pay ?? [];
        try {
            $sale = Sale::create([
                'user_id' => Auth::user()->id,
                'reservation_detail_id' => $reservation_detail->id,
                'date' => date('Y-m-d H:i:s'),
                'status' => count($request->product_id) == count($request->pay) ? 'pagada' : 'pendiente'
            ]);

            for ($i=0; $i < count($request->product_id); $i++) { 
                $sale_detail = SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $request->product_id[$i],
                    'price' => $request->price[$i],
                    'quantity' => $request->quantity[$i],
                    // Si se hizo check en paga el producto debe salir como pagado
                    'status' => in_array($request->product_id[$i], $request->pay) ? 'pagado' : 'pendiente'
                ]);

                // Si el producto se hizo check como pagado
                if(in_array($request->product_id[$i], $request->pay)){
                    CashierDetail::create([
                        'cashier_id' => $request->cashier_id,
                        'sale_detail_id' => $sale_detail->id,
                        'type' => 'ingreso',
                        'amount' => $request->price[$i] * $request->quantity[$i]
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('reservations.show', $reservation_detail->room_id)->with(['message' => 'Venta registrada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return redirect()->route('reservations.show', $reservation_detail->room_id)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function payment_store(Request $request){
        DB::beginTransaction();
        try {

            if (!$request->reservation_detail_day_id) {
                return redirect()->route('reservations.show', $request->room_id)->with(['message' => 'No se ha seleccionado ningún día de hospedaje', 'alert-type' => 'error']);
            }

            for ($i=0; $i < count($request->reservation_detail_day_id); $i++) { 
                $day_reservation = ReservationDetailDay::find($request->reservation_detail_day_id[$i]);
                $day_reservation->status = 'pagado';
                $day_reservation->update();

                CashierDetail::create([
                    'cashier_id' => $request->cashier_id,
                    'reservation_detail_day_id' => $day_reservation->id,
                    'type' => 'ingreso',
                    'amount' => $day_reservation->amount,
                    'cash' => $request->payment_qr ? 0 : 1
                ]);
            }
            DB::commit();
            return redirect()->route('reservations.show', $request->room_id)->with(['message' => 'Pago registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('reservations.show', $request->room_id)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function product_payment_store(Request $request){
        DB::beginTransaction();
        try {
            for ($i=0; $i < count($request->sale_detail_id); $i++) { 
                $sale_detail = SaleDetail::find($request->sale_detail_id[$i]);
                $sale_detail->status = 'pagado';
                $sale_detail->update();

                CashierDetail::create([
                    'cashier_id' => $request->cashier_id,
                    'sale_detail_id' => $sale_detail->id,
                    'type' => 'ingreso',
                    'amount' => $sale_detail->price * $sale_detail->quantity,
                    'cash' => $request->payment_qr ? 0 : 1
                ]);
            }
            DB::commit();
            return redirect()->route('reservations.show', $request->room_id)->with(['message' => 'Pago registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('reservations.show', $request->room_id)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function close(Request $request){
        DB::beginTransaction();
        try {
            // Actualizar estado de reserva de habitación
            $reservation_detail = ReservationDetail::with(['reservation', 'room', 'days', 'sales.details'])->where('id', $request->reservation_detail_id)->first();
            $reservation_detail->status = 'finalizada';
            $reservation_detail->room->status = 'limpieza';
            $reservation_detail->reservation->finish = date('Y-m-d');

            // Pago de ventas pendientes
            $reservation_detail->sales->each(function ($sale) use($request) {
                $sale->update(['status' => 'pagada']);

                $sale->details->each(function ($detail) use($request) {
                    if ($detail->status = 'pendiente') {
                        $detail->update(['status' => 'pagado']);
                        CashierDetail::create([
                            'cashier_id' => $request->cashier_id,
                            'sale_detail_id' => $detail->id,
                            'type' => 'ingreso',
                            'amount' => $detail->price * $detail->quantity,
                            'cash' => $request->payment_qr ? 0 : 1
                        ]);
                    }
                });
            });

            // Pago de registros de hospedaje
            $reservation_detail->days->each(function ($day) use($request) {
                if ($day->status = 'pendiente') {
                    $day->update(['status' => 'pagado']);
                    CashierDetail::create([
                        'cashier_id' => $request->cashier_id,
                        'reservation_detail_day_id' => $day->id,
                        'type' => 'ingreso',
                        'amount' => $day->amount,
                        'cash' => $request->payment_qr ? 0 : 1
                    ]);
                }
            });

            $reservation_detail->room->update();
            $reservation_detail->reservation->update();
            $reservation_detail->update();

            DB::commit();
            return redirect()->route('reservations.index')->with(['message' => 'Hospedaje finalizado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            // throw $th;
            return redirect()->route('reservations.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
