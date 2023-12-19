@extends('voyager::master')

@section('page_title', 'Reporte General')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <h1 class="page-title">
                    <i class="voyager-file-text"></i> Reporte General
                </h1>
            </div>
            <div class="col-md-8 text-right" style="padding-top: 20px">
                <a href="{{ route('report-general.index') }}?type=print" class="btn btn-danger" target="_blank">
                    <i class="fa fa-print"></i> <span>Imprimir</span>
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="4"><h4 class="text-center">Detalle de habitaciones</h4></th>
                                    </tr>
                                    <tr>
                                        <th>Disponibles</th>
                                        <th>Ocupadas</th>
                                        <th>Reservadas</th>
                                        <th>Sucias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            @php
                                                $room_available = App\Models\Room::where('status', 'disponible')->get();
                                            @endphp
                                            <b>{{ $room_available->count() }}</b>
                                            <hr style="margin: 5px 0px">
                                            <small>
                                                @foreach ($room_available as $item)
                                                    {{ $item->code }} &nbsp;
                                                @endforeach
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $room_occupied = App\Models\Room::where('status', 'ocupada')->get();
                                            @endphp
                                            <b>{{ $room_occupied->count() }}</b>
                                            <hr style="margin: 5px 0px">
                                            <small>
                                                @foreach ($room_occupied as $item)
                                                    {{ $item->code }} &nbsp;
                                                @endforeach
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $reservation = App\Models\Reservation::with('details.room')->where('status', 'reservacion')->whereDate('start', date('Y-m-d'))->get();
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
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                               $room_dirty = App\Models\Room::where('status', 'limpieza')->get(); 
                                            @endphp
                                            <b>{{ $room_dirty->count() }}</b>
                                            <hr style="margin: 5px 0px">
                                            <small>
                                                @foreach ($room_dirty as $item)
                                                    {{ $item->code }} &nbsp;
                                                @endforeach
                                            </small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="6"><h4 class="text-center">Ventas</h4></th>
                                    </tr>
                                    <tr>
                                        <th>N&deg;</th>
                                        <th>Usuario</th>
                                        <th>Cliente</th>
                                        <th>Detalle</th>
                                        <th>Estado</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                        $total = 0;
                                    @endphp
                                    @forelse (App\Models\Sale::with(['person', 'reservation_detail.reservation.person', 'details.product', 'user'])->whereDate('date', date('Y-m-d'))->get() as $item)
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
                                                <ul>
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
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-right"><b>TOTAL</b></td>
                                        <td class="text-right"><h4><small>Bs.</small> {{ $total }}</h4></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <br>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="5"><h4 class="text-center">Movimientos de caja</h4></th>
                                    </tr>
                                    <tr>
                                        <th>N&deg;</th>
                                        <th>Usuario</th>
                                        <th>Tipo</th>
                                        <th>Detalle</th>
                                        <th>Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $cont = 1;
                                        $total_revenue = 0;
                                        $total_expenses = 0;
                                        $total_qr = 0;
                                    @endphp
                                    @forelse (App\Models\CashierDetail::with(['cashier.user', 'sale_detail.product', 'service', 'reservation_detail_day.reservation_detail.room', 'penalty.type'])->whereDate('created_at', date('Y-m-d'))->get() as $item)
                                        <tr>
                                            <td>{{ $cont }}</td>
                                            <td>
                                                {{ $item->cashier->user->name }} <br>
                                                <small>{{ date('H:i', strtotime($item->created_at)) }}</small>
                                            </td>
                                            <td>{{ $item->type }}</td>
                                            <td>
                                                @if ($item->sale_detail)
                                                    Venta de <b>{{ $item->sale_detail->quantity == floatVal($item->sale_detail->quantity) ? intval($item->sale_detail->quantity) : $item->sale_detail->quantity }} {{ $item->sale_detail->product->name }}</b>
                                                @elseif ($item->service)
                                                    Uso de <b>{{ $item->service->name }}</b>
                                                @elseif ($item->reservation_detail_day)
                                                    Pago de hospedaje habitación <b>{{ $item->reservation_detail_day->reservation_detail->room->code }}</b>
                                                @elseif ($item->penalty)
                                                    Pago de multa por <b>{{ $item->penalty->type->name }}</b>
                                                    @if ($item->penalty->observations)
                                                        <br> <small>{{ $item->penalty->observations }}</small>
                                                    @endif
                                                @endif
                                                {!! $item->observations ? '<br>'.$item->observations : '' !!}
                                            </td>
                                            <td class="text-right">
                                                @if (!$item->cash)
                                                    <i class="fa fa-qrcode text-primary" title="Pago con QR"></i>
                                                @endif 
                                                {{ floatval($item->amount) == intval($item->amount) ? intval($item->amount) : $item->amount }}
                                            </td>
                                        </tr>
                                        @php
                                            $cont++;
                                            if ($item->type == 'ingreso') {
                                                $total_revenue += $item->amount;
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
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-right"><b>INGRESO TOTAL</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_revenue }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right"><b>EGRESO TOTAL</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_expenses }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_qr }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right"><b>TOTAL EN CAJA</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_revenue - $total_expenses - $total_qr }}</h4></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        tfoot {
            background-color: #f7f7f7
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {

        });
    </script>
@stop
