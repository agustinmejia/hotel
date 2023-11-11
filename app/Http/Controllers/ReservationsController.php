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
use App\Models\ProductBranchOffice;
use App\Models\ReservationPerson;

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
        return view('reservations.browse');
    }

    public function list(){
        $this->custom_authorize('browse_reservations');
        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $data = Reservation::with(['details', 'user', 'person', 'aditional_people'])
                    ->where(function($query) use ($search){
                        if($search){
                            $query->OrwhereHas('user', function($query) use($search){
                                $query->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('person', function($query) use($search){
                                $query->whereRaw("(full_name like '%$search%' or dni like '%$search%' or phone like '%$search%')");
                            });
                        }
                    })
                    ->where('deleted_at', NULL)->orderBy('id', 'DESC')->paginate($paginate);
        return view('reservations.list', compact('data'));
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
                'person_id' => $request->person_id[0],
                'start' => $request->start,
                'finish' => $request->finish,
                'reason'  => $request->reason,
                'observation' => $request->observation,
                'status' => $request->status
            ]);

            // En caso de que sea más de una persona
            if (count($request->person_id) > 1) {
                for ($i=1; $i < count($request->person_id); $i++) { 
                    ReservationPerson::create([
                        'reservation_id' => $reservation->id,
                        'person_id' => $request->person_id[$i]
                    ]);
                }
            }

            // Lista de habitaciones
            for ($i=0; $i < count($request->room_id); $i++) { 
                $room = Room::find($request->room_id[$i]);

                // No Se cambia el estado de la habitación ni se hace el registro de días de hospedaje
                // en caso de que sea una reserva

                if ($request->status == 'en curso') {
                    $room->status = 'ocupada';
                    $room->update();
                }

                $detail = ReservationDetail::create([
                    'reservation_id' => $reservation->id,
                    'room_id' => $room->id,
                    'price' => $room->type->price,
                    'status' => $request->status == 'en curso' ? 'ocupada' : 'reservada'
                ]);
                
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
    
                if ($request->status == 'en curso') {
                    // Calendario de ocupación
                    $start = $request->start;
                    $finish = $request->finish ?? date('Y-m-d');
        
                    while ($start <= $finish) {
                        ReservationDetailDay::create([
                            'reservation_detail_id' => $detail->id,
                            'date' => $start,
                            'amount' => $room->type->price + $total_accessories
                        ]);
                        $start = date('Y-m-d', strtotime($start.' +1 days'));
                    }
                }
            }

            DB::commit();
            return redirect()->route('reception.index')->with(['message' => 'Hospedaje registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('reception.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
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
        $room_id = request('room_id');
        $reservation = Reservation::with(['details.accessories.accessory', 'details.days', 'details.sales.details.product', 'details.room.type'])
                        ->where('id', $id)->first();
        $cashier = Cashier::where('user_id', Auth::user()->id)->where('status', 'abierta')->first();
        if ($room_id) {
            return view('reservations.read-single', compact('reservation', 'cashier', 'room_id'));
        } else {
            return view('reservations.read', compact('reservation', 'cashier'));
        }
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
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
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

                // Descontar del stock
                $cashier = Cashier::find($request->cashier_id);
                $product = ProductBranchOffice::where('product_id', $request->product_id[$i])
                            ->where('branch_office_id', $cashier->branch_office_id)
                            ->where('quantity', '>=', $request->quantity[$i])
                            ->first();
                $product->quantity -= $request->quantity[$i];
                $product->update();
            }

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Venta registrada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function payment_store(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {

            if (!$request->reservation_detail_day_id) {
                return redirect()->to($redirect)->with(['message' => 'No se ha seleccionado ningún día de hospedaje', 'alert-type' => 'error']);
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
            return redirect()->to($redirect)->with(['message' => 'Pago registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function product_payment_store(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            if (!$request->cashier_id) {
                return redirect()->to($redirect)->with(['message' => 'No has aperturado caja', 'alert-type' => 'warning']);
            }
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
            return redirect()->to($redirect)->with(['message' => 'Pago registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function change(Request $request){
        DB::beginTransaction();
        try {
            $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
            $room = Room::find($request->room_id);
            $reservation_detail_old = ReservationDetail::find($request->reservation_detail_id);

            $reservation_detail = ReservationDetail::create([
                'reservation_id' => $reservation_detail_old->reservation_id,
                'room_id' => $room->id,
                'price' => $room->type->price
            ]);

            $room->status = 'ocupada';
            $room->update();

            foreach ($reservation_detail_old->days as $item) {
                if($item->date >= $request->start){
                    ReservationDetailDay::create([
                        'reservation_detail_id' => $reservation_detail->id,
                        'date' => $item->date,
                        'amount' => $item->amount,
                        'status' => $item->status
                    ]);
                    ReservationDetailDay::where('id', $item->id)->delete();
                }
            }

            $reservation_detail_old->status = 'finalizada';
            $reservation_detail_old->room->status = 'disponible';
            $reservation_detail_old->room->update();
            $reservation_detail_old->update();

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Cambio de habitación registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function close(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            $reservation_details = $request->reservation_detail_id;
            for ($i=0; $i < count($reservation_details); $i++) { 
                // Actualizar estado de reserva de habitación
                $reservation_detail = ReservationDetail::with(['reservation', 'room', 'days', 'sales.details'])->where('id', $reservation_details[$i])->first();
                $reservation_detail->status = 'finalizada';

                // Cambiar datos de habitación
                $reservation_detail->room->status = 'limpieza';

                // Cambiar datos de reservación
                $reservation = Reservation::find($reservation_detail->reservation_id);
                if($reservation->details->where('status', 'ocupada')->count() == count($reservation_details)){
                    $reservation_detail->reservation->finish = date('Y-m-d');
                    $reservation_detail->reservation->status = 'finalizado';
                }

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
            }

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Hospedaje finalizado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
