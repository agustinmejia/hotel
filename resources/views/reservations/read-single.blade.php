@extends('voyager::master')

@section('page_title', 'Registrar Hospedaje')

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];

    // Obtener el id de la habitación seleccionada
    foreach ($reservation->details as $item) {
        if($item->room_id == $room_id){
            $reservation_detail = $item;
            $room = $item->room;      
        }
    }

    $reservation_detail_days = $reservation_detail->days->sortByDesc('date');
    $day_payments_amount = $reservation_detail_days->count() ? $reservation_detail_days[0]->amount : 0;
    $total_payments = $reservation_detail_days->where('status', 'pagado')->sum('amount');
    $total_debts = $reservation_detail_days->where('status', 'pendiente')->sum('amount');
    $total_penalties = $reservation_detail->penalties->where('status', 'pendiente')->sum('amount');

    $reservation_detail_days_payment = $reservation_detail_days->where('status', 'pagado')->sortByDesc('date');
    $last_payment_day = $reservation_detail_days_payment->count() ? $reservation_detail_days_payment[0]->date : null;

    // Verificar deudas de venta de productos
    foreach($reservation_detail->sales as $sale){
        foreach ($sale->details as $detail){
            if ($detail->status == 'pagado') {
                $total_payments += $detail->quantity * $detail->price;
            } else {
                $total_debts += $detail->quantity * $detail->price;
            }
        }
    }

    // Verificar si tiene deudas de otras habitaciones
    $count_pending_hosting = 0;
    foreach($reservation->details as $detail){
        $count_pending_hosting += $detail->days->where('status', 'pendiente')->count();
    }
@endphp

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <br>
            <div class="col-md-6" style="padding-left: 15px">
                <a href="{{ route('reception.index') }}" class="btn btn-warning"><i class="fa fa-arrow-circle-left"></i> Volver</a>
            </div>
            <div class="col-md-6 text-right" style="padding-right: 15px">
                @if ($cashier || $reservation_detail->status == 'ocupada')
                    <div class="btn-group">
                        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">
                            Opciones <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="left: -90px !important">
                            @if($cashier)
                                <li><a href="#" title="Realizar pago de hospedaje" data-toggle="modal" data-target="#add-payment-modal">Pagar hospedaje</a></li>
                            @endif
                            @if ($reservation_detail->status == 'ocupada')
                                @if (Auth::user()->branch_office_id)
                                <li><a href="#" title="Venta de producto" data-toggle="modal" data-target="#add-product-modal">Venta de producto</a></li>
                                @endif
                                <li><a href="#" title="Agregar accesorio" data-toggle="modal" data-target="#add-accessory-modal">Agregar accesorios</a></li>
                                <li><a href="#" title="Agregar huesped a la habitación" data-toggle="modal" data-target="#add-people-modal">Agregar huesped</a></li>
                                <li><a href="#" title="Agregar multa" data-toggle="modal" data-target="#add-penalty-modal">Agregar multa</a></li>
                                <li class="divider"></li>
                                <li><a href="#" title="Cambiar de habitación" data-toggle="modal" data-target="#change-room-modal">Cambiar de habitación</a></li>
                                @if($cashier && !request('disable_close'))
                                    @if ($count_pending_hosting > 1)
                                        <li><a href="{{ route('reservations.show', $reservation->id) }}" style="color: #FA3E19" title="Cerrar hospedaje">Cerrar hospedaje</a></li>
                                    @else
                                    <li><a href="#" style="color: #FA3E19" title="Cerrar hospedaje" data-toggle="modal" data-target="#close-reservation-modal">Cerrar hospedaje</a></li>
                                    @endif
                                @endif
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>

        @include('partials.check-cashier', ['cashier' => $cashier])
        
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 div-details">
                                @php
                                    switch ($room->status) {
                                        case 'disponible':
                                            $type = 'success';
                                            break;
                                        case 'ocupada':
                                            $type = 'primary';
                                            break;
                                        case 'reservada':
                                            $type = 'warning';
                                            break;
                                        case 'fuera de servicio':
                                            $type = 'danger';
                                            break;
                                        default:
                                            $type = 'default';
                                            break;
                                    }
                                @endphp
                                <b>DETALLES DE HABITACIÓN</b> &nbsp; <label class="label label-{{ $type }}">{{ Str::ucfirst($room->status) }}</label>
                                <table style="width: 100%; margin-top: 20px">
                                    <tr style="height: 30px">
                                        <td><b>Huesped(es):</b></td>
                                        <td colspan="5">
                                            {{ $reservation->person->full_name }}
                                            @if ($reservation->aditional_people->count() > 0)
                                                @php
                                                    $cont = 1;
                                                @endphp
                                                @foreach ($reservation->aditional_people as $item)
                                                    {{ $reservation->aditional_people->count() == $cont ? ' y ' : ', ' }} {{ $item->person->full_name }}
                                                    @php
                                                        $cont++;
                                                    @endphp
                                                @endforeach
                                            @endif

                                        </td>
                                    </tr>
                                    <tr style="height: 30px">
                                        <td><b>N&deg; de habitación:</b></td>
                                        <td>{{ $room->code }}</td>
                                        <td><b>Tipo:</b></td>
                                        <td>{{ $room->type->name }}</td>
                                        <td><b>Precio:</b></td>
                                        <td>{{ $room->type->price == intval($room->type->price) ? intval($room->type->price) : $room->type->price }}</td>
                                    </tr>
                                    <tr style="height: 30px">
                                        <td><b>Llegada:</b></td>
                                        <td>{{ date('d', strtotime($reservation_detail->reservation->start)) }}/{{ $months[intval(date('m', strtotime($reservation_detail->reservation->start)))] }}</td>
                                        <td><b>Salida: </b></td>
                                        <td>{{ $reservation_detail->reservation->finish ? date('d', strtotime($reservation_detail->reservation->finish)).'/'.$months[intval(date('m', strtotime($reservation_detail->reservation->finish)))] : 'No definida' }}</td>
                                        <td><b>Pagado hasta:</b></td>
                                        <td>{{ $last_payment_day ? date('d', strtotime($last_payment_day)).'/'.$months[intval(date('m', strtotime($last_payment_day)))] : 'No hay pagos' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 div-details">
                                <b>DETALLES DE PAGOS</b>
                                <table style="width: 100%; margin-top: 20px;">
                                    <tr style="height: 30px">
                                        <th class="text-center" style="width: 20%"><b>Pago diario</b></th>
                                        <th class="text-center" style="width: 20%"><b>Multas</b></th>
                                        <th class="text-center" style="width: 20%"><b>Acumulado</b></th>
                                        <th class="text-center" style="width: 20%"><b>Pagado</b></th>
                                        <th class="text-center" style="width: 20%"><b>Deuda</b></th>
                                    </tr>
                                    <tr style="height: 60px">
                                        <td class="text-center"><h4>{{ $day_payments_amount == intval($day_payments_amount) ? intval($day_payments_amount) : $day_payments_amount }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_penalties == intval($total_penalties) ? intval($total_penalties) : $total_penalties }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_payments + $total_debts + $total_penalties }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_payments }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_debts + $total_penalties }}</h4></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <form class="form-submit" action="{{ route('reservations.product.payment.store') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="8"><h4 class="text-center">Productos</h4></th>
                                            </tr>
                                            <tr>
                                                <th>N&deg;</th>
                                                <th>Fecha</th>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th>Cantidad</th>
                                                <th>Subtotal</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cont = 1;
                                                $payments = 0;
                                                $debts = 0;
                                            @endphp
                                            @forelse ($reservation_detail->sales as $sale)
                                                @foreach ($sale->details as $detail)
                                                    <tr>
                                                        <td>{{ $cont }}</td>
                                                        <td>{{ date('d/m/Y H:i', strtotime($sale->date)) }}</td>
                                                        <td>{{ $detail->product->name }}</td>
                                                        <td class="text-right">{{ floatval($detail->price) == intval($detail->price) ? intval($detail->price):$detail->price }}</td>
                                                        <td class="text-right">{{ floatval($detail->quantity) == intval($detail->quantity) ? intval($detail->quantity):$detail->quantity }}</td>
                                                        <td class="text-right">{{ $detail->quantity * $detail->price }}</td>
                                                        <td><label class="label label-{{ $detail->status == 'pagado' ? 'success':'danger' }}">{{ Str::ucfirst($detail->status) }}</label></td>
                                                        <td class="text-right">
                                                            @if ($detail->status == 'pendiente')
                                                                <input type="checkbox" name="sale_detail_id[]" value="{{ $detail->id }}" class="checkbox-sale_detail_id" data-total="{{ $detail->quantity * $detail->price }}" @if(!$cashier) disabled @endif style="transform: scale(1.5);" title="Pagar" />
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $cont++;
                                                        if ($detail->status == 'pagado') {
                                                            $payments += $detail->quantity * $detail->price;
                                                        } else {
                                                            $debts += $detail->quantity * $detail->price;
                                                        }
                                                    @endphp
                                                @endforeach
                                            @empty
                                                <tr>
                                                    <td colspan="8">No hay registros</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5" class="text-right">TOTAL Bs.</td>
                                                <td class="text-right"><h5>{{ $payments + $debts }}</h5></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right">MONTO PAGADO Bs.</td>
                                                <td class="text-right"><h5>{{ $payments }}</h5></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="text-right">DEUDA Bs.</td>
                                                <td class="text-right"><h5>{{ $debts }}</h5></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr id="tr-total-payment-products" style="display: none">
                                                <td colspan="5" class="text-right">MONTO A PAGAR Bs.</td>
                                                <td class="text-right"><h4 id="label-total-payment-products">0</h4></td>
                                                <td colspan="2" class="text-right"><button type="button" data-toggle="modal" data-target="#confirm-payment-product-modal" class="btn btn-primary" style="margin-top: 0px">Pagar <i class="fa fa-shopping-cart"></i></button></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    {{-- Modal confirm payment products --}}
                                    <div class="modal fade" tabindex="-1" id="confirm-payment-product-modal" role="dialog">
                                        <div class="modal-dialog modal-primary">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title"><i class="voyager-dollar"></i> Desea realizar el pago?</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary btn-submit">Sí, realizar!</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th colspan="4"><h4 class="text-center">Accesorios</h4></th>
                                        </tr>
                                        <tr>
                                            <th>N&deg;</th>
                                            <th>Detalle</th>
                                            <th>Precio</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cont = 1;
                                            $total_accessories = 0;
                                        @endphp
                                        @forelse ($reservation_detail->accessories as $item)
                                            <tr>
                                                <td>{{ $cont }}</td>
                                                <td>{{ $item->accessory->name }}</td>
                                                <td class="text-right">{{ floatval($item->price) == intval($item->price) ? intval($item->price):$item->price }}</td>
                                                <td class="text-center"><input type="checkbox" style="transform: scale(1.5);" title="Habilitado" checked disabled /></td>
                                            </tr>
                                            @php
                                                $cont++;
                                                $total_accessories += $item->price;
                                            @endphp
                                        @empty
                                            <tr>
                                                <td colspan="4">No hay registros</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="text-right">TOTAL Bs.</td>
                                            <td class="text-right"><h5>{{ $total_accessories }}</h5></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <form class="form-submit" action="{{ route('reservations.penalties.payment.store') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th colspan="6"><h4 class="text-center">Multas</h4></th>
                                            </tr>
                                            <tr>
                                                <th>N&deg;</th>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cont = 1;
                                                $payments = 0;
                                                $debts = 0;
                                            @endphp
                                            @forelse ($reservation_detail->penalties as $item)
                                                <tr>
                                                    <td>{{ $cont }}</td>
                                                    <td>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</td>
                                                    <td>
                                                        {{ $item->type->name }}
                                                        @if ($item->observations)
                                                            <br><small>{{ $item->observations }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">{{ floatval($item->amount) == intval($item->amount) ? intval($item->amount):$item->amount }}</td>
                                                    <td><label class="label label-{{ $item->status == 'pagada' ? 'success':'danger' }}">{{ Str::ucfirst($item->status) }}</label></td>
                                                    <td class="text-right">
                                                        @if ($item->status == 'pendiente')
                                                            <input type="checkbox" name="reservation_detail_penalty_id[]" value="{{ $item->id }}" class="checkbox-reservation_detail_penalty_id" data-amount="{{ $item->amount }}" @if(!$cashier) disabled @endif style="transform: scale(1.5);" title="Pagar" />
                                                        @endif
                                                    </td>
                                                </tr>
                                                @php
                                                    $cont++;
                                                    if ($item->status == 'pagada') {
                                                        $payments += $item->amount;
                                                    } else {
                                                        $debts += $item->amount;
                                                    }
                                                @endphp
                                            @empty
                                                <tr>
                                                    <td colspan="6">No hay registros</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right">TOTAL Bs.</td>
                                                <td class="text-right"><h5>{{ $payments + $debts }}</h5></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-right">MONTO PAGADO Bs.</td>
                                                <td class="text-right"><h5>{{ $payments }}</h5></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3" class="text-right">DEUDA Bs.</td>
                                                <td class="text-right"><h5>{{ $debts }}</h5></td>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr id="tr-total-payment-penalties" style="display: none">
                                                <td colspan="3" class="text-right">MONTO A PAGAR Bs.</td>
                                                <td class="text-right"><h4 id="label-total-payment-penalties">0</h4></td>
                                                <td colspan="2" class="text-right"><button type="button" data-toggle="modal" data-target="#confirm-payment-penalties-modal" class="btn btn-primary" style="margin-top: 0px">Pagar <i class="fa fa-shopping-cart"></i></button></td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    {{-- Modal confirm payment penalties --}}
                                    <div class="modal fade" tabindex="-1" id="confirm-payment-penalties-modal" role="dialog">
                                        <div class="modal-dialog modal-primary">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title"><i class="voyager-dollar"></i> Desea realizar el pago?</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary btn-submit">Sí, realizar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add payment modal --}}
    <form action="{{ route('reservations.payment.store') }}" id="form-add-payment" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-payment-modal" role="dialog">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-money"></i> Registrar pago de hospedaje</h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <div class="form-group">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="5" class="text-center">Hospedaje</th>
                                    </tr>
                                    <tr>
                                        <th>N&deg;</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                        $payment = 0;
                                        $debt = 0;
                                    @endphp
                                    @foreach ($reservation_detail->days as $item)
                                        <tr>
                                            <td>{{ $cont }}</td>
                                            <td>{{ $days[date('w', strtotime($item->date))] }}, {{ date('d', strtotime($item->date)) }} de {{ $months[intval(date('m', strtotime($item->date)))] }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td><label class="label label-{{ $item->status == 'pagado' ? 'success' : 'danger' }}" style="color: white !important">{{ Str::ucfirst($item->status) }}</label></td>
                                            <td class="text-right">
                                                <input type="checkbox" name="reservation_detail_day_id[]" value="{{ $item->id }}" data-amount="{{ $item->amount }}" class="checkbox-payment" style="transform: scale(1.5);" title="{{ $item->status == 'pagado' ? 'Pagado' : 'Pagar' }}" @if($item->status == 'pagado') disabled checked @endif /> &nbsp;&nbsp;
                                            </td>
                                        </tr>
                                        @php
                                            $cont++;
                                            if($item->status == 'pagado') {
                                                $payment  += $item->amount;
                                            } else {
                                                $debt += $item->amount;
                                            }
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">SUBTOTAL</td>
                                        <td class="text-right"><h4 style="margin: 0px;">{{ $payment + $debt }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">DEUDA</td>
                                        <td class="text-right"><h4 style="margin: 0px;">{{ $debt }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">MONTO A PAGAR</td>
                                        <td class="text-right"><h4 style="margin: 0px;" id="label-total-payment-rooms">0</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">PAGO POR QR</td>
                                        <td class="text-right"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Pagar <i class="fa fa-money"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Add product modal --}}
    <form action="{{ route('reservations.product.store') }}" id="form-add-product" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-product-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-shopping-basket"></i> Registrar compra</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="select-product">Productos</label>
                            <select class="form-control select2" id="select-product"></select>
                        </div>
                        <div class="form-group">
                            <table class="table table-hover table-products">
                                <thead>
                                    <tr>
                                        <th>N&deg;</th>
                                        <th>Detalle</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Pagado</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="products-details">
                                    <tr id="tr-empty">
                                        <td colspan="7">No hay productos en la cesta</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><b>TOTAL</b></td>
                                        <td class="text-right"><h4 id="label-total">0.00</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><b>MONTO PAGADO</b></td>
                                        <td class="text-right"><h4 id="label-payment">0.00</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right"><b>DEUDA</b></td>
                                        <td class="text-right"><h4 id="label-debt">0.00</h4></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Guardar <i class="fa fa-shopping-basket"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Create cashier modal --}}
    @include('partials.add-cashier-modal', ['redirect' => 'admin/reservations/'.$reservation_detail->reservation_id.'?room_id='.$room->id])

    {{-- Create cashier modal --}}
    <form action="{{ route('reservations.add.people') }}" id="form-add-people" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-people-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-people"></i> Agregar huespedes</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label" for="select-person_id">Cliente/Huesped</label>
                            <select name="person_id[]" class="form-control" id="select-person_id" multiple required></select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Agregar <i class="voyager-check"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- add penalty modal --}}
    <form action="{{ route('reservations.add.penalty') }}" id="form-add-penalty" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-penalty-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-warning"></i> Agregar multa</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label" for="select-penalty_type_id">Tipo de multa</label>
                            <select name="penalty_type_id" class="form-control" id="select-penalty_type_id" required>
                                <option value="" selected disabled>--Seleccionar multa--</option>
                                @foreach (App\Models\PenaltyType::get() as $item)
                                    <option value="{{ $item->id }}" data-item='@json($item)'>{{ $item->name }} ({{ $item->amount == floatval($item->amount) ? intval($item->amount) : $item->amount }} Bs.)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="amount">Monto</label>
                            <input type="number" name="amount" class="form-control" min="1" step="1" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="observations">Observaciones</label>
                            <textarea name="observations" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Agregar <i class="voyager-check"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Change room modal --}}
    <form action="{{ route('reservations.change.room') }}" id="form-change-room" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <div class="modal modal-primary fade" tabindex="-1" id="change-room-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-tags"></i> Cambio de habitación</h4>
                    </div>
                    <div class="modal-body">
                        @php
                            $rooms = App\Models\Room::with(['type'])->where('status', 'disponible')->orderBy('floor_number')->orderBy('code')->get();
                        @endphp
                        <div class="form-group">
                            <label class="control-label" for="room_id">Habitaciones</label>
                            <select name="room_id" class="form-control select2" id="select-room_id" required>
                                <option value="" selected disabled>--Seleccione la habitación--</option>
                                @foreach ($rooms as $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->type->name }} (Bs. {{ $item->type->price == floatval($item->type->price) ? intval($item->type->price) : $item->type->price }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start">Fecha</label>
                            <input type="date" name="start" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Cambiar <i class="fa fa-reset"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Close reservation modal --}}
    <form action="{{ route('reservations.close') }}" id="form-close-reservation" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id[]" value="{{ $reservation_detail->id }}">
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-danger fade" tabindex="-1" id="close-reservation-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-tags"></i> Cierre de hospedaje</h4>
                    </div>
                    <div class="modal-body">
                        @if ($total_debts + $total_penalties > 0)
                        <div class="form-group">
                            <p>Al cerrar el hospedaje se acepta que se han realizado el pago de toda la deuda, desea continuar?</p>
                            <h3 class="text-danger text-right"><span style="font-size: 12px">Deuda Bs. </span>{{ number_format($total_debts + $total_penalties, 2, ',', '.') }}</h3>
                        </div>
                        <div class="form-group text-right">
                            <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;">Pago con Qr</label>
                        </div>
                        @else
                        <div class="form-group">
                            <p>Está a punto de cerar el hospedaje y desalojar la habitación, desea continuar?</p>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-submit">Cerrar <i class="fa fa-tags"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Create person modal --}}
    @include('partials.add-person-modal')
@stop

@section('css')
    <style>
        .div-details b {
            font-weight: bold !important
        }
        .select2{
            width: 100% !important;
        }
        .table-products h4 {
            margin: 0px !important
        }
        .table th {
            text-align: center
        }
        #products-details .form-control {
            padding: 0px 5px !important;
            height: 25px;
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var productSelected = null;
        var user = @json(Auth::user());
        var cashier = @json($cashier);
        $(document).ready(function(){
            customSelect('#select-product', '{{ route("products.search") }}', formatResultProducts, data => { productSelected = data; return data.name}, '#add-product-modal', null);
            customSelect('#select-person_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, '#add-people-modal', 'createPerson()');
            customSelect('#select-city_id', '{{ route("cities.search") }}', formatResultCities, data => data.name, "#person-modal", 'createCity()');
            $('#select-branch_office_id').select2({dropdownParent: '#create-cashier-modal'});
            $('#select-penalty_type_id').select2({
                tags: true,
                dropdownParent: '#add-penalty-modal',
                createTag: function (params) {
                    return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                    }
                },
                templateResult: function (data) {
                    var $result = $("<span></span>");
                    $result.text(data.text);
                    if (data.newOption) {
                        $result.append(" <em>(ENTER para agregar)</em>");
                    }
                    return $result;
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
            })
            .change(function(){
                let item = $('#select-penalty_type_id option:selected').data('item');
                if(item){
                    $('#form-add-penalty input[name="amount"]').val(item.amount == parseInt(item.amount) ? parseInt(item.amount) : item.amount.toFixed(2))
                }

                // Si es un nuevo tipo
                if($('#select-penalty_type_id option:selected').data('select2-tag')){
                    $('#form-add-penalty input[name="amount"]').val('')
                }
            });

            if (user.branch_office_id) {
                $('#select-branch_office_id').val(user.branch_office_id).trigger('change');
            }

            $('#add-product-modal').on('shown.bs.modal', function () {
                $('#products-details .tr-item').remove();
                setNumber();
                $('#label-total').text('0.00');
                $('#label-payment').text('0.00');
                $('#label-debt').text('0.00');
            });

            $('#select-product').change(function(){
                let product = productSelected;
                $('#products-details').append(`
                    <tr id="tr-item-${product.id}" class="tr-item">
                        <td class="td-number"></td>
                        <td>
                            ${product.name}
                            <input type="hidden" name="product_id[]" value="${product.id}" />
                        </td>
                        <td>
                            ${product.price}
                            <input type="hidden" name="price[]" id="input-price-${product.id}" value="${product.price}" />
                        </td>
                        <td style="width: 100px"><input type="number" name="quantity[]" id="input-quantity-${product.id}" class="form-control" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})" value="1" min="1" step="1" max="${product.stock[0].quantity}" required /></td>
                        <td class="text-center"><input type="checkbox" name="pay[]" class="checkbox-pay" id="checkbox-pay-${product.id}" value="${product.id}" data-id="${product.id}" style="transform: scale(1.5);" onclick="getPayments()" ${cashier ? '' : 'disabled title="No has aperturado caja"'} /></td>
                        <td class="text-right"><span id="label-subtotal-${product.id}" class="label-subtotal">${product.price}</span></td>
                        <td><button class="btn btn-link" onclick="removeTr(${product.id})"><i class="voyager-trash text-danger"></i></a></td>
                    </tr>
                `);

                setNumber();
                getSubtotal(product.id);
            });

            $('.checkbox-payment').click(function(){
                var payment_total = 0;
                $('.checkbox-payment').each(function(index) {
                    if($(this).is(':checked') && !$(this).attr('disabled')){
                        payment_total += parseFloat($(this).data('amount'));
                    };
                });
                $('#label-total-payment-rooms').text(payment_total);
            });

            $('.checkbox-sale_detail_id').click(function(){
                var amount = 0;
                $('.checkbox-sale_detail_id').each(function(index) {
                    if($(this).is(':checked')){
                        amount += parseFloat($(this).data('total'));
                    };
                });
                $('#label-total-payment-products').text(amount);
                if(amount > 0){
                    $('#tr-total-payment-products').fadeIn();
                }else{
                    $('#tr-total-payment-products').fadeOut();
                }
            });

            $('.checkbox-reservation_detail_penalty_id').click(function(){
                var amount = 0;
                $('.checkbox-reservation_detail_penalty_id').each(function(index) {
                    if($(this).is(':checked')){
                        amount += parseFloat($(this).data('amount'));
                    };
                });
                $('#label-total-payment-penalties').text(amount);
                if(amount > 0){
                    $('#tr-total-payment-penalties').fadeIn();
                }else{
                    $('#tr-total-payment-penalties').fadeOut();
                }
            });
        });

        function setNumber(){
            var length = 0;
            $(".td-number").each(function(index) {
                $(this).text(index +1);
                length++;
            });

            if(length > 0){
                $('#tr-empty').css('display', 'none');
            }else{
                $('#tr-empty').fadeIn('fast');
            }
        }

        function getSubtotal(id){
            let price = $(`#input-price-${id}`).val() ? parseFloat($(`#input-price-${id}`).val()):0;
            let quantity = $(`#input-quantity-${id}`).val() ? parseFloat($(`#input-quantity-${id}`).val()):0;
            $(`#label-subtotal-${id}`).text((price * quantity).toFixed(2));
            getTotal();
            getPayments();
        }

        function getTotal(){
            let total = 0;
            $(".label-subtotal").each(function(index) {
                total += parseFloat($(this).text());
            });
            $('#label-total').text(total.toFixed(2));
        }

        function getPayments(){
            let payment_total = 0;
            $(".checkbox-pay").each(function(index) {
                let id = $(this).data('id');
                if($(`#checkbox-pay-${id}`).is(':checked')){
                    payment_total += parseFloat($(`#label-subtotal-${id}`).text());
                };
            });
            $('#label-payment').text(payment_total.toFixed(2))

            let total = parseFloat($(`#label-total`).text());
            $('#label-debt').text((total - payment_total).toFixed(2))
        }

        function removeTr(id){
            $(`#tr-item-${id}`).remove();
            setNumber();
            getTotal();
        }

        function createPerson(){
            $('#select-person_id').select2('close');
            $('#add-people-modal').modal('hide');
            $('#person-modal').modal('show');
        }

        function createCity(){
            $('#select-city_id').select2('destroy');
            $('#select-city_id').fadeOut('fast', function(){
                $('#input-city_name').fadeIn('fast');
                $('#input-city_name').prop('required', true);
            });
        }
    </script>
@stop
