@extends('partials.template-print', ['page_title' => 'Cierre de caja'])

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
@endphp

@section('header')
    <h2 style="margin: 0px">CIERRE DE CAJA</h2>
    <small>
        {{ $days[date('N', strtotime($cashier->closed_at))] }}, {{ date('d', strtotime($cashier->closed_at)) }} de {{ $months[intval(date('m', strtotime($cashier->closed_at)))] }} del {{ date('Y', strtotime($cashier->closed_at)) }} <br>
        {{ $cashier->user->name }}
    </small>
@endsection

@section('content')
    <br>
    <div>
        <table width="100%">
            <tr>
                <td>Usuario</td>
                <td><b>{{ $cashier->user->name }}</b></td>
                <td>Sucursal</td>
                <td><b>{{ $cashier->branch_office->name }}</b></td>
            </tr>
        </table>
    </div>
    <br>
    <div class="details">
        <table width="100%" border="1" cellpadding="5">
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
                    </tr>
                    @php
                        $cont++;
                        $total += $item->amount;
                        if(!$item->cash){
                            if ($item->type == 'ingreso') {
                                $total_qr += $item->amount;
                            } else {
                                $total_qr -= $item->amount;
                            }
                            
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
                    <td colspan="3" class="text-right"><b>TOTAL</b></td>
                    <td class="text-right"><h4 style="margin: 0px"><small>Bs.</small>{{ $total }}</h4></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                    <td class="text-right"><h4 style="margin: 0px"><small>Bs.</small>{{ $total_qr }}</h4></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><b>TOTAL EN CAJA</b></td>
                    <td class="text-right"><h4 style="margin: 0px"><small>Bs.</small>{{ $total - $total_qr }}</h4></td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@section('css')
    <style>
        .details table {
            border-collapse: collapse;
            font-size: 12px
        }
        .text-right {
            text-align: right
        }
    </style>
@endsection