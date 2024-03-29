@extends('partials.template-print', ['page_title' => 'Cierre de caja'])

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
@endphp

@section('header')
    <h2 style="margin: 0px">CIERRE DE CAJA</h2>
    <small>
        {{ $days[date('N', strtotime($cashier->closed_at))] }}, {{ date('d', strtotime($cashier->closed_at)) }} de {{ $months[intval(date('m', strtotime($cashier->closed_at)))] }} del {{ date('Y', strtotime($cashier->closed_at)) }} <br>
        {{ $cashier->user->name }}
    </small>
@endsection

@section('content')
    <div class="content">
        <br>
        <div>
            <table width="100%">
                <tr>
                    <td>Usuario</td>
                    <td><b>{{ $cashier->user->name }}</b></td>
                    <td>Sucursal</td>
                    <td><b>{{ $cashier->branch_office->name }}</b></td>
                    <td>Fecha de cierre</td>
                    <td><b>{{ date('d/m/Y H:i', strtotime($cashier->closed_at)) }}</b></td>
                </tr>
                <tr>
                    <td>Hab. disponibles</td>
                    <td><b>{{ $cashier->rooms_available ? $cashier->rooms_available : 'Ninguna' }}</b></td>
                    <td>Hab. ocupadas</td>
                    <td><b>{{ $cashier->rooms_occupied ? $cashier->rooms_occupied : 'Ninguna' }}</b></td>
                    <td>Hab. sucias</td>
                    <td><b>{{ $cashier->rooms_dirty ? $cashier->rooms_dirty : 'Ninguna' }}</b></td>
                </tr>
            </table>
        </div>
        <br>
        <br>
        <div class="details">
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="5">Movimientos</th>
                    </tr>
                    <tr>
                        <th width="30px">N&deg;</th>
                        <th width="50px">Horas</th>
                        <th>Tipo</th>
                        <th>Detalle</th>
                        <th width="100px">Monto (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cont = 1;
                        $total_revenue = 0;
                        $total_expenses = 0;
                        $total_qr = 0;
                        $total_sales = 0;
                        $total_hosting = 0;
                    @endphp
                    @forelse ($cashier->details as $item)
                        <tr>
                            <td>{{ $cont }}</td>
                            <td>{{ date('H:i', strtotime($item->created_at)) }}</td>
                            <td>{{ $item->type }}</td>
                            <td>
                                @if ($item->sale_detail)
                                    Venta de <b>{{ $item->sale_detail->quantity == floatVal($item->sale_detail->quantity) ? intval($item->sale_detail->quantity) : $item->sale_detail->quantity }} {{ $item->sale_detail->product->name }}</b> <br>
                                @elseif ($item->service)
                                    Uso de <b>{{ $item->service->name }}</b> <br>
                                @elseif ($item->reservation_detail_day)
                                    Pago de hospedaje habitación <b>{{ $item->reservation_detail_day->reservation_detail->room->code }}</b> | {{ $item->reservation_detail_day->reservation_detail->reservation->person->full_name }}<br>
                                    <small class="text-muted">del {{ $days[intval(date('N', strtotime($item->reservation_detail_day->date)))] }}, {{ date('d', strtotime($item->reservation_detail_day->date)) }} de {{ $months[intval(date('m', strtotime($item->reservation_detail_day->date)))] }}</small>
                                @elseif ($item->penalty)
                                    Multa de <b>{{ $item->penalty->type->name }}</b> {{ $item->penalty->observations ? '('.$item->penalty->observations.')' : '' }} <br>
                                @elseif ($item->resort_register)
                                    {{ $item->resort_register->quantity }} entradas <b>{{ $item->resort_register->type }}</b>
                                @endif
                                {!! $item->observations ? $item->observations : '' !!}
                            </td>
                            <td class="text-right">
                                @if (!$item->cash)
                                    <small><b>(Pago Qr)</b></small>
                                @endif 
                                {{ floatval($item->amount) == intval($item->amount) ? intval($item->amount) : $item->amount }}
                            </td>
                        </tr>
                        @php
                            $cont++;
                            if ($item->type == 'ingreso') {
                                $total_revenue += $item->amount;

                                if ($item->sale_detail_id) {
                                    $total_sales += $item->amount;
                                } else {
                                    $total_hosting += $item->amount;
                                }
                            } else {
                                $total_expenses += $item->amount;
                            }
                            if(!$item->cash){
                                $total_qr += $item->amount;
                            }
                        @endphp
                    @empty
                        <tr>
                            <td colspan="5">No hay datos registardos</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="4" class="text-right"><b>INGRESO TOTAL</b></td>
                        <td class="text-right td-total"><h4>{{ $total_revenue }}</h4></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                        <td class="text-right td-total"><h4>{{ $total_qr }}</h4></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><b>EGRESO TOTAL</b></td>
                        <td class="text-right td-total"><h4>{{ $total_expenses }}</h4></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><b>TOTAL EN CAJA</b></td>
                        <td class="text-right td-total"><h4>{{ $total_revenue - $total_expenses - $total_qr }}</h4></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="4">Ingresos</th>
                    </tr>
                    <tr>
                        <th>N&deg;</th>
                        <th>Detalle</th>
                        <th width="100px">Monto (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Pago de hospedajes/multas</td>
                        <td class="text-right td-total"><b>{{ $total_hosting }}</b></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Ventas</td>
                        <td class="text-right td-total"><b>{{ $total_sales }}</b></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="5">Llegada de huespedes</th>
                    </tr>
                    <tr>
                        <th width="30px">N&deg;</th>
                        <th width="50px">Hora</th>
                        <th>Habitaciones</th>
                        <th width="100px">Días de<br>hospedaje</th>
                        <th width="100px">Cantidad de<br>personas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cont = 1;
                        $arrivals = App\Models\Reservation::with(['details.room', 'details.accessories.accessory', 'aditional_people'])->where('user_id', $cashier->user_id)->where('created_at', '>=', $cashier->created_at)->whereRaw($cashier->closed_at ? 'created_at <= "'.$cashier->closed_at.'"' : 1)->get();
                        $people_quantity_total = 0;
                    @endphp
                    @forelse ($arrivals as $item)
                        @php
                            $people_quantity = $item->aditional_people->whereIn('room_id', $item->details->pluck('room_id'))->count() + 1;
                            $people_quantity_total += $people_quantity;
                        @endphp
                        <tr>
                            <td>{{ $cont }}</td>
                            <td>{{ date('H:i', strtotime($item->created_at)) }}</td>
                            <td>
                                @foreach ($item->details as $detail)
                                    <b>{{ $detail->room->code }}</b> piso {{ $detail->room->floor_number }} 
                                    @if ($detail->accessories->count())
                                        | 
                                        @foreach ($detail->accessories as $accessory_item)
                                            {{ $accessory_item->accessory->name }} &nbsp;
                                        @endforeach
                                    @endif
                                    <br>
                                @endforeach
                            </td>
                            <td class="text-right">
                                @if ($item->start && $item->finish)
                                    @php
                                        $start = new \DateTime($item->start);
                                        $finish = new \DateTime($item->finish);
                                    @endphp
                                    {{ $start->diff($finish)->format('%d') +1 }}
                                @else
                                    No definido
                                @endif
                            </td>
                            <td class="text-right">{{ $people_quantity }}</td>
                        </tr>
                        @php
                            $cont++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="5">No hay datos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><b>TOTAL</b></td>
                        <td class="text-right td-total"><b>{{ $people_quantity_total }}</b></td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="5">Salida de huespedes</th>
                    </tr>
                    <tr>
                        <th width="30px">N&deg;</th>
                        <th width="50px">Hora</th>
                        <th>Habitaciones</th>
                        <th width="100px">Días de<br>hospedaje</th>
                        <th width="100px">Cantidad de<br>personas</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cont = 1;
                        // TODO: Filtrar por la sucursal del usuario cuando trabaje con varias sucursales 
                        $departures = App\Models\ReservationDetail::with(['reservation.aditional_people', 'room', 'days'])->where('unoccupied_at', '>=', $cashier->created_at)->whereRaw($cashier->closed_at ? 'unoccupied_at <= "'.$cashier->closed_at.'"' : 1)->get();
                        $people_quantity_total = 0;
                    @endphp
                    @forelse ($departures as $item)
                        @php
                            $people_quantity = $item->reservation->aditional_people->whereIn('room_id', $item->room_id)->count() + 1;
                            $people_quantity_total += $people_quantity;
                        @endphp
                        @if ($item->days->count())
                            <tr>
                                <td>{{ $cont }}</td>
                                <td>{{ date('H:i', strtotime($item->unoccupied_at)) }}</td>
                                <td>
                                    <b>{{ $item->room->code }}</b> piso {{ $item->room->floor_number }}
                                </td>
                                <td class="text-right">
                                    @if ($item->days->first()->date && $item->days->sortByDesc('date')->first()->date)
                                        @php
                                            $start = new \DateTime($item->days->first()->date);
                                            $finish = new \DateTime($item->days->sortByDesc('date')->first()->date);
                                        @endphp
                                        {{ $start->diff($finish)->format('%d') +1 }}
                                    @else
                                        No definido
                                    @endif
                                </td>
                                <td class="text-right">{{ $people_quantity }}</td>
                            </tr>
                        @endif
                        @php
                            $cont++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="5">No hay datos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><b>TOTAL</b></td>
                        <td class="text-right td-total"><b>{{ $people_quantity_total }}</b></td>
                    </tr>
                </tfoot>
            </table>
            @if (request('detailed') == '1')
            <br>
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="4">Accesorios</th>
                    </tr>
                    <tr>
                        <th width="30px">N&deg;</th>
                        <th>Accesorios</th>
                        <th>Habitaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cont = 1;
                        $services = App\Models\RoomAccessory::with(['reservation_accessories.reservation_detail.room', 'reservation_accessories.reservation_detail' => function($q){
                                            $q->where('status', 'ocupada');
                                        }])
                                        ->whereHas('reservation_accessories.reservation_detail', function($q){
                                            $q->where('status', 'ocupada');
                                        })->get();
                    @endphp
                    @forelse ($services as $item)
                        <tr>
                            <td>{{ $cont }}</td>
                            <td>{{ $item->name }}</td>
                            <td>
                                @foreach ($item->reservation_accessories->groupBy('reservation_detail.room.floor_number') as $key => $reservation_accessories)
                                    @if ($key)
                                        <b>Piso {{ $key }}</b> <br>
                                        @foreach ($reservation_accessories as $reservation_accessory)
                                            {{ $reservation_accessory->reservation_detail->room->code }} &nbsp;
                                        @endforeach
                                        <br>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        @php
                            $cont++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="3">No hay datos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <br>
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="6">Deudas</th>
                    </tr>
                    <tr>
                        <th width="30px">N&deg;</th>
                        <th>Habitaciones</th>
                        <th>Detalles</th>
                        <th width="80px">Hospedaje (Bs.)</th>
                        <th width="80px">Ventas (Bs.)</th>
                        <th width="80px">Multas (Bs.)</th>
                        <th width="80px">Total (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cont = 1;
                        $total = 0;
                        $reservations = App\Models\ReservationDetail::with(['reservation.person', 'reservation.aditional_people', 'days' => function($q){
                                                $q->where('status', 'pendiente');
                                            }, 'days.payments', 'sales.details' => function($q){
                                                $q->where('status', 'pendiente');
                                            }, 'penalties' => function($q){
                                                $q->where('status', 'pendiente');
                                            }, 'room'])->where('status', 'ocupada')->get();
                    @endphp
                    @forelse ($reservations->sortBy('floor_name') as $item)
                        @php
                            $last_day = $item->days->sortByDesc('date')->first();
                            $current_price = $last_day ? $last_day->amount : 0;
                            $debt_hosting_amount = $item->days->sum('amount');
                            $debt_penalties_amount = $item->penalties->sum('amount');
                            $debt_sales_amount = 0;
                            $advance_amount = 0;
                            foreach($item->days as $day){
                                $advance_amount += $day->payments->sum('amount');
                            }
                            foreach($item->sales as $sale){
                                foreach($sale->details as $detail){
                                    $debt_sales_amount += $detail->price * $detail->quantity;
                                }
                            }
                        @endphp
                        @if ($debt_hosting_amount - $advance_amount + $debt_sales_amount > 0)
                            <tr>
                                <td>{{ $cont }}</td>
                                <td>
                                    {{ $item->room->code }} piso {{ $item->room->floor_number }} <br>
                                    <small class="text-muted">{{ $current_price == intval($current_price) ? intval($current_price) : $current_price }} Bs.</small>
                                </td>
                                <td>
                                    {{ $item->reservation->person->full_name }} @if($item->reservation->aditional_people->count()) (+{{ $item->reservation->aditional_people->count() }}) @endif <br>
                                    <small>
                                        Huesped desde el {{ date('d', strtotime($item->reservation->start)) }}/{{ $months[intval(date('m', strtotime($item->reservation->start)))] }}
                                        @if($item->reservation->finish)
                                        hasta {{ date('d', strtotime($item->reservation->finish)) }}/{{ $months[intval(date('m', strtotime($item->reservation->finish)))] }}
                                        @endif
                                    </small>
                                </td>
                                <td style="text-align: right">
                                    {{ $debt_hosting_amount - $advance_amount }} <br>
                                    <small class="text-muted">{{ $item->days->count() }} Días</small>
                                </td>
                                <td style="text-align: right">{{ $debt_sales_amount }}</td>
                                <td style="text-align: right">{{ $debt_penalties_amount }}</td>
                                <td style="text-align: right">{{ $debt_hosting_amount - $advance_amount + $debt_sales_amount + $debt_penalties_amount}}</td>
                            </tr>
                        @endif
                        @php
                            $total += $debt_hosting_amount - $advance_amount + $debt_sales_amount + $debt_penalties_amount;
                            $cont++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="7">No hay datos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="text-right"><b>TOTAL</b></td>
                        <td class="text-right td-total"><b>{{ $total }}</b></td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        thead th {
            background-color: #ECF0F1;
            font-size: 9px
        }
        .details table {
            border-collapse: collapse;
            font-size: 11px
        }
        .text-right {
            text-align: right
        }
        .text-muted{
            color: #3d3d3d
        }
        .td-total{
            background-color: rgba(27, 38, 49, 0.2)
        }
        .td-total h4{
            margin: 0px
        }
    </style>
@endsection