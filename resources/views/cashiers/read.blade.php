@extends('voyager::master')

@section('page_title', 'Ver Caja')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-folder"></i> Viendo Caja
        <a href="{{ route('cashiers.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a>
        &nbsp;
        @if ($cashier->status == 'abierta')
            <button class="btn btn-danger" disabled>
                <span class="voyager-lock"></span>&nbsp;
                Cerrar
            </button>
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
                                        $total = 0;
                                        $total_qr = 0;
                                    @endphp
                                    @forelse ($cashier->details as $item)
                                        <tr>
                                            <td>{{ $cont }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>
                                                {{ $item->observations }}
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
                                            $total += $item->amount;
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
                                        <td colspan="3" class="text-right"><b>TOTAL</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total_qr }}</h4></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right"><b>TOTAL EN CAJA</b></td>
                                        <td class="text-right"><h4><small>Bs.</small>{{ $total - $total_qr }}</h4></td>
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
