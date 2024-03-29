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

    $total_debts = 0;

    $day_payments_amount = $reservation_detail->days->count() ? $reservation_detail->days->sortByDesc('date')->first()->amount : 0;
    $last_day_payments = $reservation_detail->days->count() ? $reservation_detail->days->sortByDesc('date')->first()->date : null;
    $total_payments = $reservation_detail->days->where('status', 'pagado')->sum('amount');
    $total_days_debts = $reservation_detail->days->whereIn('status', ['pendiente', 'deuda'])->sum('amount');
    $total_penalties_debts = $reservation_detail->penalties->whereIn('status', ['pendiente', 'deuda'])->sum('amount');

    $reservation_detail_days_payment = $reservation_detail->days->where('status', 'pagado');
    $last_payment_day = $reservation_detail_days_payment->count() ? $reservation_detail_days_payment->sortByDesc('date')->first()->date : null;

    $total_debts += $total_days_debts;

    // Verificar deudas de venta de productos
    $total_sales_debts = 0;
    foreach($reservation_detail->sales as $sale){
        foreach ($sale->details as $detail){
            if ($detail->status == 'pagado') {
                $total_payments += $detail->quantity * $detail->price;
            } else {
                $total_sales_debts += $detail->quantity * $detail->price;
            }
        }
    }

    $total_debts += $total_sales_debts;
    
    foreach ($reservation_detail->days->where('status', 'pendiente') as $item) {
        $total_debts -= $item->payments->sum('amount');
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
                @if ($reservation_detail->status == 'ocupada')
                    <div class="btn-group">
                        <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">
                            Opciones <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" style="left: -90px !important">
                            {{-- @if($casshier) --}}
                            <li><a href="#" title="Realizar pago de hospedaje" data-toggle="modal" data-target="#add-payment-host-modal">Pagar hospedaje</a></li>
                            {{-- @endif --}}
                            @if ($reservation_detail->status == 'ocupada')
                                @if (Auth::user()->branch_office_id)
                                <li><a href="#" title="Venta de producto" data-toggle="modal" data-target="#add-product-sale-modal">Venta de producto</a></li>
                                @endif
                                <li><a href="#" title="Agregar accesorio" data-toggle="modal" data-target="#add-service-modal">Agregar accesorio</a></li>
                                <li><a href="#" title="Agregar huesped a la habitación" data-toggle="modal" data-target="#add-people-modal">Agregar huesped</a></li>
                                <li><a href="#" title="Agregar multa" data-toggle="modal" data-target="#add-penalty-modal">Agregar multa</a></li>
                                @if($cashier)
                                <li class="divider" style="margin: 10px 0px"></li>
                                <li><a href="#" title="Pago parcial" data-toggle="modal" data-target="#add-partial-payment-modal">Pago parcial</a></li>
                                <li><a href="#" title="Pago total" data-toggle="modal" data-target="#add-total-payment-modal">Pago total</a></li>
                                @endif
                                <li class="divider" style="margin: 10px 0px"></li>
                                <li><a href="#" title="Cambiar de habitación" data-toggle="modal" data-target="#change-room-modal">Cambiar de habitación</a></li>
                                @if($cashier && !request('disable_close'))
                                    @if ($reservation->details->count() > 1)
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
                                            @php
                                                $cont = 1;
                                            @endphp
                                            @if ($reservation->aditional_people->count() > 0)
                                                @foreach ($reservation->aditional_people as $item)
                                                    {{ $reservation->aditional_people->count() == $cont ? ' y ' : ', ' }} {{ $item->person->full_name }}
                                                    @php
                                                        $cont++;
                                                    @endphp
                                                @endforeach
                                            @endif
                                             <i class="voyager-info-circled" data-toggle="tooltip" data-placement="top" title="{{ $cont }} personas"></i>
                                        </td>
                                    </tr>
                                    <tr style="height: 30px">
                                        <td><b>N&deg; de habitación:</b></td>
                                        <td>{{ $room->code }}</td>
                                        <td><b>Tipo:</b></td>
                                        <td>{{ $room->type->name }}</td>
                                        <td><b>Precio:</b></td>
                                        <td>{{ $reservation_detail->price == intval($reservation_detail->price) ? intval($reservation_detail->price) : $reservation_detail->price }}</td>
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
                                        <td class="text-center">
                                            <h4>
                                                <span style="cursor:default" title="Pago calculado hasta {{ $last_day_payments ? date('d/m/Y', strtotime($last_day_payments)) : '' }}">{{ $day_payments_amount == intval($day_payments_amount) ? intval($day_payments_amount) : $day_payments_amount }}</span>
                                                @if ($reservation_detail->status == 'ocupada')
                                                    <a href="#" id="btn-edit-daily-payment" data-toggle="modal" data-target="#edit_daily_payment-modal" data-price="{{ $day_payments_amount }}"><i class="voyager-edit"></i></a>
                                                @endif
                                            </h4>
                                        </td>
                                        <td class="text-center"><h4>{{ $total_penalties_debts == intval($total_penalties_debts) ? intval($total_penalties_debts) : $total_penalties_debts }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_payments + $total_debts + $total_penalties_debts }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_payments }}</h4></td>
                                        <td class="text-center"><h4>{{ $total_debts + $total_penalties_debts }}</h4></td>
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
                                                        <td class="text-center"><label class="label label-{{ $detail->status == 'pagado' ? 'success':'danger' }}">{{ Str::ucfirst($detail->status) }}</label></td>
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
                                        @foreach ($reservation_detail->accessories as $item)
                                            <tr>
                                                <td>{{ $cont }}</td>
                                                <td>{{ $item->accessory->name }}</td>
                                                <td class="text-right">{{ floatval($item->price) == intval($item->price) ? intval($item->price):$item->price }}</td>
                                                <td class="text-center">
                                                    @if ($reservation_detail->status == 'ocupada')
                                                    <button type="button" class="btn btn-link btn-remove-service" data-id="{{ $item->id }}" data-type="accessory"><i class="voyager-trash text-danger"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $cont++;
                                                $total_accessories += $item->price;
                                            @endphp
                                        @endforeach

                                        @foreach ($reservation_detail->food as $item)
                                            <tr>
                                                <td>{{ $cont }}</td>
                                                <td>{{ $item->type->name }}</td>
                                                <td class="text-right"></td>
                                                <td class="text-center">
                                                    @if ($reservation_detail->status == 'ocupada')
                                                        <button type="button" class="btn btn-link btn-remove-service" data-id="{{ $item->id }}" data-type="food_type"><i class="voyager-trash text-danger"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($reservation_detail->accessories->count() == 0 && $reservation_detail->food->count() == 0)
                                            <tr>
                                                <td colspan="4">No hay registros</td>
                                            </tr>
                                        @endif
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
    <form action="{{ route('reservations.payment.store') }}" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-payment-host-modal" role="dialog">
            <div class="modal-dialog modal-l">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-money"></i> Registrar pago de hospedaje</h4>
                    </div>
                    <div class="modal-body" style="max-height: 500px; overflow-y: auto">
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" name="payment_type" class="radio-payment-type" value="1" checked>Normal</label>
                            <label class="radio-inline"><input type="radio" name="payment_type" class="radio-payment-type" value="2">Pago adelantado</label>
                        </div>
                        <div class="form-group" id="div-payment-normal">
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
                                            <td class="text-right" @if($item->payments->sum('amount') > 0 && $item->status == 'pendiente') title="Adelanto de {{ $item->payments->sum('amount') }}" style="cursor: pointer" @endif>
                                                @php
                                                    if($item->status == 'pendiente'){
                                                        $amount = $item->amount - $item->payments->sum('amount');
                                                    }else{
                                                        $amount = $item->amount;
                                                    }
                                                @endphp
                                                {{ floatval($amount) == intval($amount) ? intval($amount) : $amount }}
                                            </td>
                                            <td class="text-center"><label class="label label-{{ $item->status == 'pagado' ? 'success' : 'danger' }}" style="color: white !important">{{ Str::ucfirst($item->status) }}</label></td>
                                            <td>
                                                <input type="checkbox" name="reservation_detail_day_id[]" value="{{ $item->id }}" data-amount="{{ $amount }}" class="checkbox-payment" style="transform: scale(1.5);" title="{{ $item->status == 'pagado' ? 'Pagado' : 'Pagar' }}" @if($item->status == 'pagado') disabled checked @endif /> &nbsp;&nbsp;
                                            </td>
                                            <td>
                                                @if ($item->status == 'pendiente')
                                                    <a href="#" class="btn-update-price-day" data-toggle="modal" data-target="#edit-day-price-modal" data-item='@json($item)'><i class="voyager-edit"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        @php
                                            $cont++;
                                            if($item->status == 'pagado') {
                                                $payment  += $amount;
                                            } else {
                                                $debt += $amount;
                                            }
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">SUBTOTAL</td>
                                        <td class="text-right"><h4 style="margin: 0px;">{{ $payment + $debt }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">DEUDA</td>
                                        <td class="text-right"><h4 style="margin: 0px;">{{ $debt }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">MONTO A PAGAR</td>
                                        <td class="text-right"><h4 style="margin: 0px;" id="label-total-payment-rooms">0</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right" style="vertical-align: middle;">PAGO POR QR</td>
                                        <td class="text-right"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div id="div-payment-prepayment" style="display: none">
                            <div class="form-group" >
                                <label for="date_payment">Pagado hasta</label>
                                <input type="hidden" name="initial_date">
                                <input type="hidden" name="amount" value="{{ $day_payments_amount }}">
                                <input type="date" name="date_payment" class="form-control">
                                <div class="text-right">
                                    <h3 id="label-prepayment-total">0 Bs.</h3>
                                    <span id="label-prepayment-days"></span>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <label class="checkbox-inline"><input type="checkbox" name="payment_qr_alt" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit" @if(!$cashier) disabled title="Debe aperturar caja" @endif>Pagar <i class="fa fa-money"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Add product modal --}}
    <form action="{{ route('reservations.product.store') }}" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-product-sale-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-shopping-basket"></i> Registrar venta</h4>
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
                            <select name="room_id" class="form-control" id="select-room_id" required>
                                <option value="" selected disabled>--Seleccione la habitación--</option>
                                @foreach ($rooms as $item)
                                    <option value="{{ $item->id }}" data-item='@json($item)'>{{ $item->code }} - {{ $item->type->name }} (Bs. {{ $item->type->price == floatval($item->type->price) ? intval($item->type->price) : $item->type->price }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="price">Precio</label>
                            <input type="number" name="price" class="form-control" required>
                            <small>Precio actual de hospedaje {{ $room->type->price == intval($room->type->price) ? intval($room->type->price) : $room->type->price }} Bs.</small>
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
                        @if ($total_debts + $total_penalties_debts > 0)
                        <div class="form-group">
                            <p>Al cerrar el hospedaje se acepta que se han realizado el pago de toda la deuda, desea continuar?</p>
                            <h3 class="text-danger text-right"><span style="font-size: 12px">Deuda Bs. </span>{{ number_format($total_debts + $total_penalties_debts, 2, ',', '.') }}</h3>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-inline"><input type="checkbox" name="payment_qr" id="checkbox-payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-inline"><input type="checkbox" name="not_payment" id="checkbox-not_payment" value="1" title="En caso de que el huesped se haya ido sin pagar" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Retiro sin pagar <i class="voyager-warning text-danger"></i></label>
                        </div>
                        <div class="form-group div-not_payment">
                            <select name="type" id="select-type" class="form-control form-control-not_payment">
                                <option value="">Seleccione tipo de deuda</option>
                                <option value="1">Abandonó sin pagar</option>
                                <option value="2">Paga luego</option>
                            </select>
                        </div>
                        <div class="form-group div-not_payment">
                            <textarea name="observations" class="form-control form-control-not_payment" rows="3" placeholder="Deuda pendiente"></textarea>
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

    {{-- Add partial payment modal --}}
    <form action="{{ route('reservations.add.payment') }}" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-partial-payment-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-money"></i> Agregar pago parcial</h4>
                    </div>
                    <div class="modal-body">
                        @php
                            $total_amount = $reservation_detail->days->where('status', 'pendiente')->sum('amount');
                            foreach ($reservation_detail->days->where('status', 'pendiente') as $item) {
                                $total_amount -= $item->payments->sum('amount');
                            }
                        @endphp
                        <div class="form-group">
                            <div class="panel panel-bordered" style="border-left: 5px solid #62A8EA">
                                <div class="panel-body" style="padding: 15px 20px">
                                    <p>La deuda total asciende a un monto de <b>{{ $total_amount }} <small>Bs.</small></b> </p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="amount">Monto</label>
                            <input type="number" name="amount" class="form-control" step="0.5" min="0.5" max="{{ $total_amount }}" required>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
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

    {{-- Edit price day modal --}}
    <form action="{{ route('reservations.update.amount_day') }}" class="form-submit" id="form-edit-day-price" method="POST">
        @csrf
        <input type="hidden" name="id">
        <div class="modal modal-primary fade" tabindex="-1" id="edit-day-price-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-edit"></i> Editar costo de hospedaje</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="amount">Precio</label>
                            <input type="number" name="amount" class="form-control" step="1" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Editar <i class="fa fa-edit"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Total payment modal --}}
    <form action="{{ route('reservations.total.payment') }}" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-primary fade" tabindex="-1" id="add-total-payment-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-money"></i> Agregar pago parcial</h4>
                    </div>
                    <div class="modal-body">
                        <br>
                        <div class="form-group">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Deudas</th>
                                        <th class="text-right">Monto</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Gastos de hospedaje</td>
                                        <td class="text-right">
                                            {{ $total_days_debts }}
                                            <input type="hidden" name="hosting_debts" value="{{ $total_days_debts }}">
                                        </td>
                                        <td class="text-right"><input type="checkbox" name="type_payment[]" class="checkbox-type_payment" value="hosting" data-amount="{{ $total_days_debts }}" style="transform: scale(1.5);" @if($total_days_debts <= 0) disabled @endif></td>
                                    </tr>
                                    <tr>
                                        <td>Gastos de consumo</td>
                                        <td class="text-right">
                                            {{ $total_sales_debts }}
                                            <input type="hidden" name="sales_debts" value="{{ $total_sales_debts }}">
                                        </td>
                                        <td class="text-right"><input type="checkbox" name="type_payment[]" class="checkbox-type_payment" value="sales" data-amount="{{ $total_sales_debts }}" style="transform: scale(1.5);" @if($total_sales_debts <= 0) disabled @endif></td>
                                    </tr>
                                    <tr>
                                        <td>Multas</td>
                                        <td class="text-right">
                                            {{ $total_penalties_debts }}
                                            <input type="hidden" name="penalties_debts" value="{{ $total_penalties_debts }}">
                                        </td>
                                        <td class="text-right"><input type="checkbox" name="type_payment[]" class="checkbox-type_payment" value="penalties" data-amount="{{ $total_penalties_debts }}" style="transform: scale(1.5);" @if($total_penalties_debts <= 0) disabled @endif></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td><b>TOTAL</b></td>
                                        <td class="text-right"><b id="label-total-payment">0</b></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
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

    {{-- Create person modal --}}
    @include('partials.add-person-modal')

    {{-- Total payment modal --}}
    <form action="{{ route('reservations.remove.service') }}" id="form-remove-service" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="id">
        <input type="hidden" name="type">
        <div class="modal modal-danger fade" tabindex="-1" id="remove-service-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-trash"></i> Anular el siguiente servicio</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-submit">Sí, anular <i class="fa fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Edita daily payment modal --}}
    <form action="{{ route('reservations.details.update.daily-payment') }}" class="form-submit" id="form-edit_daily_payment" method="POST">
        @csrf
        <input type="hidden" name="reservation_detail_id" value="{{ $reservation_detail->id }}">
        <div class="modal fade" tabindex="-1" id="edit_daily_payment-modal" role="dialog">
            <div class="modal-dialog modal-primary">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-edit"></i> Editar precio diario</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="price">Precio diario</label>
                            <input type="number" name="price" class="form-control" step="1" min="0" required>
                            <input type="hidden" name="old_price" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-primary btn-submit" value="Sí, editar">
                    </div>
                </div>
            </div>
        </div>
    </form>
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
        .div-not_payment{
            display: none
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var productSelected = null;
        var user = @json(Auth::user());
        var cashier = @json($cashier);
        var dayPaymentsAmount = parseFloat('{{ $day_payments_amount }}');
        var lastPaymentDay = "{{ $last_payment_day ?? date('Y-m-d', strtotime($reservation_detail->reservation->start.' -1 days')) }}";

        // Variable para pago total
        var totalPayment = 0;
        $(document).ready(function(){
            customSelect('#select-product', '{{ route("products.search") }}', formatResultProducts, data => { productSelected = data; return data.name}, '#add-product-sale-modal', null);
            customSelect('#select-person_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, '#add-people-modal', 'createPerson()');
            customSelect('#select-city_id', '{{ route("cities.search") }}', formatResultCities, data => data.name, "#person-modal", 'createCity()');
            $('#select-branch_office_id').select2({dropdownParent: '#create-cashier-modal'});
            $('#select-room_id').select2({dropdownParent: $('#change-room-modal')});
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
            $('#select-type').select2({dropdownParent: $('#close-reservation-modal')})

            if (user.branch_office_id) {
                $('#select-branch_office_id').val(user.branch_office_id).trigger('change');
            }

            $('#add-product-sale-modal').on('shown.bs.modal', function () {
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

            $('.btn-update-price-day').click(function(){
                $('#add-payment-host-modal').modal('hide');
                let item = $(this).data('item');
                $('#form-edit-day-price input[name="id"]').val(item.id);
                $('#form-edit-day-price input[name="amount"]').val(parseInt(item.amount));
            });

            $('#btn-edit-daily-payment').click(function(){
                $('#form-edit_daily_payment input[name="price"]').val(parseInt($(this).data('price')));
                $('#form-edit_daily_payment input[name="old_price"]').val(parseInt($(this).data('price')));
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

            $('.checkbox-type_payment').click(function(){
                if($(this).is(':checked')){
                    totalPayment += parseFloat($(this).data('amount'));
                }else{
                    totalPayment -= parseFloat($(this).data('amount'));
                }

                $('#label-total-payment').text(totalPayment);
            });

            $('.radio-payment-type').change(function(){
                let type = $(this).val();
                if(type == 1){
                    $('#div-payment-prepayment').fadeOut('fast', function(){
                        $('#div-payment-normal').fadeIn('fast');
                        $('#add-payment-host-modal input[name="date_payment"]').prop('required', false);
                    });
                }else{
                    $('#div-payment-normal').fadeOut('fast', function(){
                        $('#div-payment-prepayment').fadeIn('fast');
                        $('#add-payment-host-modal input[name="date_payment"]').prop('required', true);
                    });
                }
                $('#add-payment-host-modal input[name="initial_date"]').val(moment(lastPaymentDay).add(1, 'days').format('YYYY-MM-DD'));
                $('#add-payment-host-modal input[name="date_payment"]').val('');
                $('#add-payment-host-modal input[name="date_payment"]').prop('min', moment(lastPaymentDay).add(1, 'days').format('YYYY-MM-DD'));
                $('#label-prepayment-total').text(' 0 Bs.');
                $('#label-prepayment-days').empty();
            });

            $('#add-payment-host-modal input[name="date_payment"]').change(function(){
                let date = moment($(this).val());
                let lastPayment = moment(lastPaymentDay);
                $('#label-prepayment-total').text(`${date.diff(lastPayment, 'days') * dayPaymentsAmount} Bs.`);
                $('#label-prepayment-days').text(`${date.diff(lastPayment, 'days')} ${date.diff(lastPayment, 'days') > 1 ? 'días' : 'día'}`);
                
            });

            $('#select-room_id').change(function(){
                let item = $('#select-room_id option:selected').data('item');
                $('#form-change-room input[name="price"]').val(parseInt(item.type.price));
            });

            $('.btn-remove-service').click(function(e){
                e.preventDefault();
                let id = $(this).data('id');
                let type = $(this).data('type');
                $('#remove-service-modal').modal('show');
                $('#form-remove-service input[name="id"]').val(id);
                $('#form-remove-service input[name="type"]').val(type);
            });

            $('#checkbox-not_payment').click(function(){
                if($(this).is(':checked')){
                    $('#checkbox-payment_qr').prop('checked', false);
                    $('#checkbox-payment_qr').prop('disabled', true);
                    $('.div-not_payment').fadeIn('fast');
                    $('.form-control-not_payment').prop('required', true);
                }else{
                    $('#checkbox-payment_qr').prop('disabled', false);
                    $('.div-not_payment').fadeOut('fast');
                    $('.form-control-not_payment').prop('required', false);
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
