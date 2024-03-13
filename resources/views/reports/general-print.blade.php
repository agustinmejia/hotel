@extends('partials.template-print', ['page_title' => 'Reporte General'])

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
@endphp

@section('header')
    <h2 style="margin: 0px">REPORTE GENERAL</h2>
    <small>
        {{ $days[date('N', strtotime($date))] }}, {{ date('d', strtotime($date)) }} de {{ $months[intval(date('m', strtotime($date)))] }} del {{ date('Y', strtotime($date)) }} <br>
        {{ Auth::user()->name }}
    </small>
@endsection

@section('content')
    <div class="content">
        @if (date('Y-m-d') == $date)
            <table width="100%" border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th colspan="4"><b class="text-center">Detalle de habitaciones</b></th>
                    </tr>
                    <tr>
                        <th style="width: 25%">Disponibles</th>
                        <th style="width: 25%">Ocupadas</th>
                        <th style="width: 25%">Reservadas</th>
                        <th style="width: 25%">Sucias</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="vertical-align: top;">
                            @php
                                $room_available = App\Models\Room::where('status', 'disponible')->get();
                            @endphp
                            <b>{{ $room_available->count() }}</b>
                            <hr style="margin: 5px 0px">
                            <small>
                                @foreach ($room_available as $item)
                                    {{ $item->code }} &nbsp;
                                @endforeach
                                @if ($room_available->count() == 0)
                                    &nbsp;
                                @endif
                            </small>
                        </td>
                        <td style="vertical-align: top;">
                            @php
                                $room_occupied = App\Models\Room::where('status', 'ocupada')->get();
                            @endphp
                            <b>{{ $room_occupied->count() }}</b>
                            <hr style="margin: 5px 0px">
                            <small>
                                @foreach ($room_occupied as $item)
                                    {{ $item->code }} &nbsp;
                                @endforeach
                                @if ($room_occupied->count() == 0)
                                    &nbsp;
                                @endif
                            </small>
                        </td>
                        <td style="vertical-align: top;">
                            @php
                                $reservation = App\Models\Reservation::with('details.room')->where('status', 'reservacion')->whereDate('start', date('Y-m-d', strtotime($date)))->get();
                                $reservations = 0;
                                foreach ($reservation as $item) {
                                    $reservations += $item->details->count();
                                }
                            @endphp
                            <b>{{ $reservations }}</b>
                            <hr style="margin: 5px 0px">
                            <small>
                                @foreach ($reservation as $item)
                                    @foreach ($item->details as $detail)
                                        {{ $detail->room->code }} &nbsp;
                                    @endforeach
                                @endforeach
                                @if ($reservations == 0)
                                    &nbsp;
                                @endif
                            </small>
                        </td>
                        <td style="vertical-align: top;">
                            @php
                            $room_dirty = App\Models\Room::where('status', 'limpieza')->get(); 
                            @endphp
                            <b>{{ $room_dirty->count() }}</b>
                            <hr style="margin: 5px 0px">
                            <small>
                                @foreach ($room_dirty as $item)
                                    {{ $item->code }} &nbsp;
                                @endforeach
                                @if ($room_dirty->count() == 0)
                                    &nbsp;
                                @endif
                            </small>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif

        <table width="100%" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th colspan="4"><b class="text-center">Movimientos de caja</b></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_sales = 0;
                    $total_hosting = 0;
                @endphp
                @forelse ($cashiers as $cashier)
                    <thead>
                        <tr>
                            <th colspan="4"><b>{{ $cashier->user->name }} | {{ date('H:i', strtotime($cashier->created_at)) }} - {{ $cashier->closed_at ? date('H:i', strtotime($cashier->closed_at)) : 'Pendiente' }}</b></th>
                        </tr>
                        <tr>
                            <th style="width: 30px">N&deg;</th>
                            <th>Tipo</th>
                            <th>Detalle</th>
                            <th width="70px">Monto (Bs.)</th>
                        </tr>
                    </thead>
                    @php
                        $cont = 1;
                        $total_revenue = 0;
                        $total_expenses = 0;
                        $total_qr = 0;
                    @endphp
                    @forelse ($cashier->details as $item)
                        <tr>
                            <td>{{ $cont }}</td>
                            <td>{{ $item->type }}</td>
                            <td>
                                @if ($item->sale_detail)
                                    Venta de <b>{{ $item->sale_detail->quantity == floatVal($item->sale_detail->quantity) ? intval($item->sale_detail->quantity) : $item->sale_detail->quantity }} {{ $item->sale_detail->product->name }}</b>
                                @elseif ($item->service)
                                    Uso de <b>{{ $item->service->name }}</b>
                                @elseif ($item->reservation_detail_day)
                                    Pago de hospedaje habitación <b>{{ $item->reservation_detail_day->reservation_detail->room->code }}</b> | {{ $item->reservation_detail_day->reservation_detail->reservation->person->full_name }}<br>
                                    <small class="text-muted">del {{ $days[intval(date('N', strtotime($item->reservation_detail_day->date)))] }}, {{ date('d', strtotime($item->reservation_detail_day->date)) }} de {{ $months[intval(date('m', strtotime($item->reservation_detail_day->date)))] }}</small>
                                @elseif ($item->penalty)
                                    Pago de multa por <b>{{ $item->penalty->type->name }}</b> habitación <b>{{ $item->penalty->reservation_detail->room->code }}</b>
                                    @if ($item->penalty->observations)
                                        <br> <small>{{ $item->penalty->observations }}</small>
                                    @endif
                                @elseif ($item->resort_register)
                                    {{ $item->resort_register->quantity }} entradas <b>{{ $item->resort_register->type }}</b>
                                @endif
                                {!! $item->observations ? '<br>'.$item->observations : '' !!}
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

                                if ($item->sale_detail_id ) {
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
                        <td colspan="3" class="text-right"><b>INGRESO TOTAL</b></td>
                        <td class="text-right td-total"><h4>{{ $total_revenue }}</h4></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><b>EGRESO TOTAL</b></td>
                        <td class="text-right td-total"><h4>{{ $total_expenses }}</h4></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                        <td class="text-right td-total"><h4>{{ $total_qr }}</h4></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><b>TOTAL EN CAJA</b></td>
                        <td class="text-right td-total"><h4>{{ $total_revenue - $total_expenses - $total_qr }}</h4></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No hay datos registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <br>

        <table width="100%" border="1" cellpadding="5">
            <thead>
                <tr>
                    <th colspan="6"><b class="text-center">Ingresos</b></th>
                </tr>
                <tr>
                    <th style="width: 30px">N&deg;</th>
                    <th>Detalle</th>
                    <th width="70px">Monto (Bs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Pago de hospedajes</td>
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
                    <th colspan="6"><b class="text-center">Detalle de ventas</b></th>
                </tr>
                <tr>
                    <th style="width: 30px">N&deg;</th>
                    <th>Usuario</th>
                    <th>Cliente</th>
                    <th>Detalle</th>
                    <th>Estado</th>
                    <th width="70px">Total (Bs.)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cont = 1;
                    $total = 0;
                @endphp
                @forelse ($sales as $item)
                    <tr>
                        <td>{{ $cont }}</td>
                        <td>
                            {{ $item->user->name }} <br>
                            <small>{{ date('H:i', strtotime($item->created_at)) }}</small>
                        </td>
                        <td>
                            @if ($item->person)
                                {{ $item->person->full_name }}
                            @else
                                {{ $item->reservation_detail->reservation->person->full_name }} <br> <b>Habitación {{ $item->reservation_detail->room->code }}</b>
                            @endif
                        </td>
                        <td>
                            <ul style="padding-left: 15px">
                                @php
                                    $subtotal = 0;
                                @endphp
                                @foreach ($item->details as $item)
                                <li>{{ $item->quantity == floatval($item->quantity) ? intval($item->quantity) : $item->quantity }} {{ $item->product->name }}</li>
                                @php
                                    $subtotal += $item->quantity * $item->price;
                                @endphp
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td class="text-right">{{ $subtotal }}</td>
                    </tr>
                    @php
                        $cont++;
                        $total += $subtotal;
                    @endphp
                @empty
                    <tr>
                        <td colspan="6">No hay datos registrados</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="5" class="text-right"><b>TOTAL</b></td>
                    <td class="text-right td-total"><h4> {{ $total }}</h4></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('css')
    <style>
        thead th {
            background-color: #ECF0F1
        }
        .content table {
            border-collapse: collapse;
            font-size: 11px
        }
        .text-right {
            text-align: right
        }
        th h4, td h4 {
            margin: 5px 0px
        }
        .td-total{
            background-color: rgba(27, 38, 49, 0.2)
        }
    </style>
@endsection