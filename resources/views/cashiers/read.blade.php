@extends('voyager::master')

@section('page_title', 'Ver Caja')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-logbook"></i> Viendo Caja
        <a href="{{ route('cashiers.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a>
        &nbsp;
        @if ($cashier->status == 'abierta')
            <a href="{{ route('cashiers.close.index', $cashier->id) }}" class="btn btn-danger">
                <span class="voyager-lock"></span>&nbsp;
                Cerrar
            </a>
        @else
            <a href="{{ route('cashiers.print', $cashier->id) }}" class="btn btn-default" target="_blank">
                <span class="fa fa-print"></span>&nbsp;
                Imprimir
            </a>
        @endif
    </h1>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Usuario</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->user->name }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Sucursal</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $cashier->branch_office->name }}</p>
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
                                        <th>Acciones</th>
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
                                                    Venta de <b>{{ $item->sale_detail->quantity == floatVal($item->sale_detail->quantity) ? intval($item->sale_detail->quantity) : $item->sale_detail->quantity }} {{ $item->sale_detail->product->name }}</b>
                                                @elseif ($item->service)
                                                    Uso de <b>{{ $item->service->name }}</b>
                                                @elseif ($item->reservation_detail_day)
                                                    Pago de hospedaje habitación <b>{{ $item->reservation_detail_day->reservation_detail->room->code }}</b>
                                                @endif
                                                {!! $item->observations ? '<br>'.$item->observations : '' !!}
                                            </td>
                                            <td class="text-right">
                                                @if (!$item->cash)
                                                    <i class="fa fa-qrcode text-primary" title="Pago con QR"></i>
                                                @endif 
                                                {{ floatval($item->amount) == intval($item->amount) ? intval($item->amount) : $item->amount }}
                                            </td>
                                            <td></td>
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
                                        <td colspan="3" class="text-right"><b>INGRESO TOTAL</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_revenue }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>EGRESO TOTAL</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_expenses }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_qr }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>TOTAL EN CAJA</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_revenue - $total_expenses - $total_qr }}</h4></td>
                                        <td></td>
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

@section('javascript')
    <script>
        $(document).ready(function () {
            
        });
    </script>
@stop
