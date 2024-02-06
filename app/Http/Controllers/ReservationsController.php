<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

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
use App\Models\PenaltyType;
use App\Models\ReservationDetailPenalty;
use App\Models\ReservationDetailDayPay;
use App\Models\ReservationDetailFoodType;
use App\Models\Person;

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
        $cashier = Cashier::where('user_id', Auth::user()->id)->where('status', 'abierta')->first();
        return view('reservations.edit-add', compact('room', 'cashier'));
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
                        'person_id' => $request->person_id[$i],
                        'room_id' => $request->status == 'en curso' ? $request->room_id[0] : null
                    ]);
                }
            }

            // Lista de habitaciones
            for ($i=0; $i < count($request->room_id); $i++) { 
                $room = Room::find($request->room_id[$i]);

                // No se cambia el estado de la habitación ni se hace el registro de días de hospedaje
                // en caso de que sea una reserva

                if ($request->status == 'en curso') {
                    $room->status = 'ocupada';
                    $room->update();
                }

                $room_price = $request->room_price ?? $room->type->price;
                $detail = ReservationDetail::create([
                    'reservation_id' => $reservation->id,
                    'room_id' => $room->id,
                    'price' => $room_price,
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

                // Lista de refrigerio
                if ($request->food_type_id) {
                    for ($i=0; $i < count($request->food_type_id); $i++) { 
                        ReservationDetailFoodType::create([
                            'reservation_detail_id' => $detail->id,
                            'food_type_id' => $request->food_type_id[$i]
                        ]);
                    }
                }

                // Detalle del mensaje que se le va a enviar al huesped
                $message_detail = '';
    
                if ($request->status == 'en curso') {
                    // Calendario de ocupación
                    $start = $request->start;
                    $finish = $request->finish ?? date('Y-m-d');
        
                    while ($start <= $finish) {
                        ReservationDetailDay::create([
                            'reservation_detail_id' => $detail->id,
                            'date' => $start,
                            'amount' => $room_price + $total_accessories
                        ]);
                        $start = date('Y-m-d', strtotime($start.' +1 days'));
                    }

                    $message_detail = 'piso '.$room->floor_number.' con el número *'.$room->code.'* y un precio diario de '.($room_price + $total_accessories).' bs.';
                }
            }

            try {
                if($message_detail){
                    $person = Person::find($request->person_id[0]);
                    // Enviar notificación al nuevo huesped
                    if (setting('system.whatsapp-server') && setting('system.whatsapp-session') && $person->phone) {
                        Http::post(setting('system.whatsapp-server').'/send?id='.setting('system.whatsapp-session'), [
                            'phone' => '591'.$person->phone,
                            'text' => "Bienvenido al *Hotel Tarope*, su habitación se encuentra en el $message_detail, Gracias por su preferencia!"
                        ]);
                    }

                    // Notificar al administrador
                    if (setting('system.phone-admin')) {
                        Http::post(setting('system.whatsapp-server').'/send?id='.setting('system.whatsapp-session'), [
                            'phone' => '591'.setting('system.phone-admin'),
                            'text' => "Nuevo alquiler de habitación en el $message_detail, cantidad de huespedes *".count($request->person_id).'*. Registrado por '.Auth::user()->name
                        ]);
                    }
                }
            } catch (\Throwable $th) {
                //throw $th;
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
        $reservation = Reservation::with(['details.accessories.accessory', 'details.food.type', 'details.days.payments', 'details.penalties', 'details.sales.details.product', 'details.room.type', 'aditional_people.person'])
                        ->where('id', $id)->first();
        $cashier = Cashier::where('user_id', Auth::user()->id)->where('status', 'abierta')->first();
        if ($room_id) {
            update_hosting();
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
        $this->custom_authorize('delete_reservations');
        $reservation = Reservation::with(['details.days.payments', 'details.sales', 'details.penalties', 'details.room'])->where('id', $id)->first();
        $pending = true;
        foreach ($reservation->details as $item) {
            // Si existen días de hospedaje pagados
            if($item->days->where('status', 'pagado')->count()){
                $pending = false;
            }

            // Si se han hecho pagos parciales de hospedaje
            foreach ($item->days as $day) {
                if($day->payments->count()){
                    $pending = false;
                }
            }

            // Si hay ventas realizadas
            if($item->sales->count()){
                $pending = false;
            }

            // Si hay multas
            if($item->penalties->count()){
                $pending = false;
            }
        }
        if(!$pending){
            return redirect()->to($_SERVER['HTTP_REFERER'])->with(['message' => 'El Hospedaje no se puede eliminar', 'alert-type' => 'error']);
        }

        // Eliminar todo el detalle de la reservación
        $reservation->details->each(function ($detail){
            $detail->room->status = 'disponible';
            $detail->room->update();
            $detail->delete();
        });
        // Eliminar la reservación
        $reservation->delete();
        
        return redirect()->to($_SERVER['HTTP_REFERER'])->with(['message' => 'Hospedaje eliminado', 'alert-type' => 'success']);
        
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
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function payment_store(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {

            if (!$request->reservation_detail_day_id && !$request->date_payment) {
                return redirect()->to($redirect)->with(['message' => 'No se ha seleccionado ningún día de hospedaje', 'alert-type' => 'error']);
            }

            // Si el pago se va a realizar seleccionando daudas de la lista
            if ($request->reservation_detail_day_id) {
                for ($i=0; $i < count($request->reservation_detail_day_id); $i++) { 
                    $day_reservation = ReservationDetailDay::find($request->reservation_detail_day_id[$i]);
                    $day_reservation->status = 'pagado';
                    $day_reservation->update();

                    CashierDetail::create([
                        'cashier_id' => $request->cashier_id,
                        'reservation_detail_day_id' => $day_reservation->id,
                        'type' => 'ingreso',
                        'amount' => $day_reservation->amount - $day_reservation->payments->sum('amount'),
                        'cash' => $request->payment_qr ? 0 : 1
                    ]);
                }
            }

            // Si se va a pagar hasta una fecha seleccionada
            if ($request->initial_date && $request->date_payment) {
                // dd($request->all());
                $start = $request->initial_date;
                $finish = $request->date_payment;
                while ($start <= $finish) {
                    $reservation_detail_day = ReservationDetailDay::firstOrNew([
                        'reservation_detail_id' => $request->reservation_detail_id,
                        'date' => $start,
                    ]);
                    $reservation_detail_day->status = 'pagado';
                    // Si no existe le agregamos el precio
                    if (!$reservation_detail_day->exists) {
                        $reservation_detail_day->amount = $request->amount;
                        $amount = $request->amount;
                        $reservation_detail_day->save();
                    }else{
                        $amount = $reservation_detail_day->amount - $reservation_detail_day->payments->sum('amount');
                        $reservation_detail_day->update();
                    }

                    CashierDetail::create([
                        'cashier_id' => $request->cashier_id,
                        'reservation_detail_day_id' => $reservation_detail_day->id,
                        'type' => 'ingreso',
                        'amount' => $amount,
                        'cash' => $request->payment_qr_alt ? 0 : 1
                    ]);

                    $start = date('Y-m-d', strtotime($start.' +1 days'));
                }
            }
                
            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Pago registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            dd($th);
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

    public function penalties_payment_store(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            if (!$request->cashier_id) {
                return redirect()->to($redirect)->with(['message' => 'No has aperturado caja', 'alert-type' => 'warning']);
            }
            for ($i=0; $i < count($request->reservation_detail_penalty_id); $i++) { 
                $reservation_detail_penalty = ReservationDetailPenalty::find($request->reservation_detail_penalty_id[$i]);
                $reservation_detail_penalty->status = 'pagada';
                $reservation_detail_penalty->update();

                CashierDetail::create([
                    'cashier_id' => $request->cashier_id,
                    'reservation_detail_penalty_id' => $reservation_detail_penalty->id,
                    'type' => 'ingreso',
                    'amount' => $reservation_detail_penalty->amount,
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

    public function change_room(Request $request){
        DB::beginTransaction();
        try {
            $room = Room::find($request->room_id);
            $reservation_detail_old = ReservationDetail::find($request->reservation_detail_id);

            $reservation_detail = ReservationDetail::create([
                'reservation_id' => $reservation_detail_old->reservation_id,
                'room_id' => $room->id,
                'price' => $request->price
            ]);

            $room->status = 'ocupada';
            $room->update();

            foreach ($reservation_detail_old->days as $item) {
                if($item->date >= $request->start){
                    ReservationDetailDay::create([
                        'reservation_detail_id' => $reservation_detail->id,
                        'date' => $item->date,
                        'amount' => $request->price,
                        'status' => $item->status
                    ]);
                    ReservationDetailDay::where('id', $item->id)->delete();
                }
            }

            $reservation_detail_old->status = 'finalizada';
            $reservation_detail_old->room->status = 'limpieza';
            $reservation_detail_old->room->update();
            $reservation_detail_old->update();

            DB::commit();

            // Redireccionar a la nueva habitación
            $route = route('reservations.show', $reservation_detail_old->reservation_id).'?room_id='.$room->id;
            return redirect()->to($route)->with(['message' => 'Cambio de habitación registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($_SERVER['HTTP_REFERER'])->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function add_people(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            $reservation_detail = ReservationDetail::find($request->reservation_detail_id);
            foreach ($request->person_id as $person_id) {
                // Verificar que la persona no sea la que tiene asignada la reserva
                if ($reservation_detail->reservation->person_id != $person_id) {
                    if($reservation_detail->reservation->aditional_people->whereIn('person_id', [$person_id])->count() == 0){
                        ReservationPerson::create([
                            'reservation_id' => $reservation_detail->reservation_id,
                            'person_id' => $person_id,
                            'room_id' => $reservation_detail->room_id
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Huesped(es) agregados', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function add_penalty(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            ReservationDetailPenalty::create([
                'reservation_detail_id' => $request->reservation_detail_id,
                'penalty_type_id' => is_numeric($request->penalty_type_id) ? $request->penalty_type_id : PenaltyType::firstOrCreate(['name' => ucfirst(strtolower($request->penalty_type_id)), 'amount' => $request->amount])->id,
                'user_id' => Auth::user()->id,
                'amount' => $request->amount,
                'observations' => $request->observations
            ]);

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Multa agregada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function add_payment(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            $details_days = ReservationDetailDay::where('reservation_detail_id', $request->reservation_detail_id)->where('status', 'pendiente')->get();
            $amount_pay = $request->amount;
            $reservation_detail_day_id = null;
            // Recorrer la lista de días pendientes para cambiar el estado
            foreach ($details_days as $item) {
                $amount_day = $item->amount - $item->payments->sum('amount');
                // Si el monto es mayor a la deuda cambiamos el estado y disminuimos el monto, sino termina el ciclo
                if($amount_pay >= $amount_day){
                    $item->status = 'pagado';
                    $item->update();
                    $amount_pay -= $amount_day;

                    // Guardar detalle de caja
                    CashierDetail::create([
                        'cashier_id' => $request->cashier_id,
                        'reservation_detail_day_id' => $item->id,
                        'type' => 'ingreso',
                        'amount' => $amount_day,
                        'cash' => $request->payment_qr ? 0 : 1
                    ]);
                }else{
                    $reservation_detail_day_id = $item->id;
                    break;
                }
            }

            // Si existe un excedente lo ponemos a cuenta del siguiente día
            if($reservation_detail_day_id && $amount_pay > 0){
                ReservationDetailDayPay::create([
                    'user_id' => Auth::user()->id,
                    'reservation_detail_day_id' => $reservation_detail_day_id,
                    'amount' => $amount_pay
                ]);

                // Guardar detalle de caja
                CashierDetail::create([
                    'cashier_id' => $request->cashier_id,
                    'reservation_detail_day_id' => $reservation_detail_day_id,
                    'type' => 'ingreso',
                    'amount' => $amount_pay,
                    'cash' => $request->payment_qr ? 0 : 1,
                    'observations' => 'Pago parcial de hospedaje'
                ]);
            }

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Hospedaje finalizado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function total_payment(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {

            if(!$request->type_payment){
                return redirect()->to($redirect)->with(['message' => 'Debe seleccionar el pago', 'alert-type' => 'error']);
            }

            $reservation_detail = ReservationDetail::with(['days.payments', 'days' => function($q){
                $q->where('status', 'pendiente');
            }, 'sales.details' => function($q){
                $q->where('status', 'pendiente');
            }, 'penalties' => function($q){
                $q->where('status', 'pendiente');
            }])
            ->where('id', $request->reservation_detail_id)->first();
            foreach ($request->type_payment as $value) {
                switch ($value) {
                    case 'hosting':
                        foreach ($reservation_detail->days as $day) {
                            $reservation_detail_day = ReservationDetailDay::find($day->id);
                            $reservation_detail_day->status = 'pagado';
                            $reservation_detail_day->update();
                            
                            CashierDetail::create([
                                'cashier_id' => $request->cashier_id,
                                'reservation_detail_day_id' => $day->id,
                                'type' => 'ingreso',
                                'amount' => $day->amount - $day->payments->sum('amount'),
                                'cash' => $request->payment_qr ? 0 : 1
                            ]);
                        }
                        break;
                    case 'sales':
                        foreach ($reservation_detail->sales as $sale) {
                            foreach ($sale->details as $detail) {
                                $sale_detail = SaleDetail::find($detail->id);
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
                        }
                        break;
                    case 'penalties':
                        foreach ($reservation_detail->penalties as $penalty) {
                            $reservation_detail_penalty = ReservationDetailPenalty::find($penalty->id);
                            $reservation_detail_penalty->status = 'pagada';
                            $reservation_detail_penalty->update();

                            CashierDetail::create([
                                'cashier_id' => $request->cashier_id,
                                'reservation_detail_penalty_id' => $reservation_detail_penalty->id,
                                'type' => 'ingreso',
                                'amount' => $reservation_detail_penalty->amount,
                                'cash' => $request->payment_qr ? 0 : 1
                            ]);
                        }
                        break;
                }
            }

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Pagos realizados', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function update_amount_day(Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        DB::beginTransaction();
        try {
            $reservation_detail_day = ReservationDetailDay::with(['reservation_detail.room'])->where('id', $request->id)->first();
            $previus_price = $reservation_detail_day->amount;
            $reservation_detail_day->amount = $request->amount;
            $reservation_detail_day->update();
            // Notificar al administrador
            if (setting('system.phone-admin')) {
                Http::post(setting('system.whatsapp-server').'/send?id='.setting('system.whatsapp-session'), [
                    'phone' => '591'.setting('system.phone-admin'),
                    'text' => "Cambio de precio de hospedaje de la habitación ".$reservation_detail_day->reservation_detail->room->code." de ".intval($previus_price)." a ".$request->amount." Bs. en el día ".date('d/m/Y', strtotime($reservation_detail_day->date)).". Registrado por ".Auth::user()->name
                ]);
            }
            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Pagos realizados', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function remove_service(Request $request){
        try {
            $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
            switch ($request->type) {
                case 'food_type':
                    ReservationDetailFoodType::where('id', $request->id)->delete();
                    break;
                case 'accessory':
                    ReservationDetailAccessory::where('id', $request->id)->delete();
                    break;
            }
            return redirect()->to($redirect)->with(['message' => 'Servicio anulado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
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
                $reservation_detail = ReservationDetail::with(['reservation', 'room', 'days.payments', 'sales.details'])->where('id', $reservation_details[$i])->first();
                $reservation_detail->status = 'finalizada';
                $reservation_detail->unoccupied_at = Carbon::now();

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
                        if ($detail->status == 'pendiente') {
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
                    if ($day->status == 'pendiente') {
                        $day->update(['status' => 'pagado']);
                        CashierDetail::create([
                            'cashier_id' => $request->cashier_id,
                            'reservation_detail_day_id' => $day->id,
                            'type' => 'ingreso',
                            'amount' => $day->amount - $day->payments->sum('amount'),
                            'cash' => $request->payment_qr ? 0 : 1
                        ]);
                    }
                });

                // Pago de multas
                $reservation_detail->penalties->each(function ($penalty) use($request) {
                    if ($penalty->status == 'pendiente') {
                        $penalty->update(['status' => 'pagada']);
                        CashierDetail::create([
                            'cashier_id' => $request->cashier_id,
                            'reservation_detail_penalty_id' => $penalty->id,
                            'type' => 'ingreso',
                            'amount' => $penalty->amount,
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
