@extends('voyager::master')

@section('page_title', 'Ver Caja')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-logbook"></i> Viendo Caja
        <a href="{{ route('cashiers.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a>
        {{-- &nbsp; --}}
        @if ($cashier->status == 'abierta')
            <a href="{{ route('cashiers.close.index', $cashier->id) }}" class="btn btn-danger">
                <span class="voyager-lock"></span>&nbsp;
                Cerrar
            </a>
        @else
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Imprimir <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="{{ route('cashiers.print', $cashier->id) }}" title="Cierre" target="_blank">Cierre</a></li>
                    <li><a href="{{ route('cashiers.print', $cashier->id) }}?detailed=1" title="Cierre" target="_blank">Cierre completo</a></li>
                </ul>
            </div>
        @endif
    </h1>
@stop

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
@endphp

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Usuario</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->user->name }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Sucursal</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->branch_office->name }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-4">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Fecha</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $days[intval(date('N', strtotime($cashier->created_at)))] }}, {{ date('d', strtotime($cashier->created_at)) }} de {{ $months[intval(date('m', strtotime($cashier->created_at)))] }} {{ date('H:i', strtotime($cashier->created_at)) }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>N&deg;</th>
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
                                    @forelse ($cashier->details as $item)
                                        <tr>
                                            <td>{{ $cont }}</td>
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
                                                    <label class="label label-warning">Pago Qr</label>
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
                                            <td colspan="4">No hay datos registardos</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>INGRESO TOTAL</b></td>
                                        <td class="text-right td-total"><h4>{{ $total_revenue }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                                        <td class="text-right td-total"><h4>{{ $total_qr }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>EGRESO TOTAL</b></td>
                                        <td class="text-right td-total"><h4>{{ $total_expenses }}</h4></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>TOTAL EN CAJA</b></td>
                                        <td class="text-right td-total"><h4>{{ $total_revenue - $total_expenses - $total_qr }}</h4></td>
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
        .td-total{
            background-color: rgba(27, 38, 49, 0.2)
        }
    </style>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            
        });
    </script>
@stop
