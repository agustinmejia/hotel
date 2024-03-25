@extends('voyager::master')

@section('page_title', 'Ver Deudor')

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
@endphp

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-dollar"></i> Viendo Deudor
        <a href="{{ route('person-defaulters.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a>
        @if ($person_defaulter->status == 'pendiente')
            <a href="#" class="btn btn-success" data-toggle="modal" data-target="#add_payment_defaulters-modal" @if(!$cashier) disabled title="Debe abrir caja primero" @else title="Pagar deuda" @endif style="margin-left: -15px">
                <span class="voyager-dollar"></span>&nbsp; Pagar
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
                                <h3 class="panel-title">Persona</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person_defaulter->person ? $person_defaulter->person->full_name : 'No definido' }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Tipo</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person_defaulter->type == 1 ? 'Abandonó sin pagar' : 'Paga luego' }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Fecha</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ date('d', strtotime($person_defaulter->created_at)).'/'.$months[intval(date('m', strtotime($person_defaulter->created_at)))].'/'.date('Y H:i', strtotime($person_defaulter->created_at)) }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Monto</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person_defaulter->amount }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Detalle</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person_defaulter->observations }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Estado</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                @php
                                    switch ($person_defaulter->status) {
                                        case 'pendiente':
                                            $label = 'danger';
                                            $status = $person_defaulter->status;
                                            break;
                                        case 'pagada':
                                            $label = 'success';
                                            $status = $person_defaulter->status;
                                            break;
                                        default:
                                            $label = 'default';
                                            $status = $person_defaulter->status ?? 'no definido';
                                            break;
                                    }
                                @endphp
                                <p><label class="label label-{{ $label }}">{{ ucfirst($status) }}</label></p>
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
                    <div class="panel-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th colspan="5"><h4>Detalles de deuda</h4></th>
                                </tr>
                                <tr>
                                    <th>Días de hospedajes</th>
                                    <th>Ventas</th>
                                    <th>Multas</th>
                                </tr>
                            </thead>
                            @php
                                $hosting_total = 0;
                                $sales_total = 0;
                                $penalties_total = 0;
                            @endphp
                            <tbody>
                                <tr>
                                    <td>
                                        <ul>
                                            @forelse ($person_defaulter->reservation_detail->days->where('status', 'deuda') as $item)
                                                <li>{{ $days[date('w', strtotime($item->date))] }}, {{ date('d', strtotime($item->date)).'/'.$months[intval(date('m', strtotime($item->date)))].'/'.date('Y', strtotime($item->date)) }} <br> <small>Bs.</small> <b>{{ $item->amount - $item->payments->sum('amount') }}</b></li>
                                                @php
                                                    $hosting_total += $item->amount - $item->payments->sum('amount');
                                                @endphp
                                            @empty
                                                <li>No hay deuda</li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                            @forelse ($person_defaulter->reservation_detail->sales->where('status', 'deuda') as $item)
                                                @foreach ($item->details as $detail)
                                                    <li>{{ $detail->product->name }} a <small>Bs.</small> <b>{{ $detail->price * $detail->quantity }}</b></li>
                                                    @php
                                                        $sales_total += $detail->price * $detail->quantity;
                                                    @endphp
                                                @endforeach
                                            @empty
                                                <li>No hay deuda</li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                            @forelse ($person_defaulter->reservation_detail->penalties->where('status', 'deuda') as $item)
                                                <li>{{ $item->observations }} <small>Bs.</small> <b>{{ $item->amount == intval($item->amount) ? intval($item->amount) : $item->amount }}</b></li>
                                                @php
                                                    $penalties_total += $item->amount;
                                                @endphp
                                            @empty
                                                <li>No hay deuda</li>
                                            @endforelse
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-right"><h4><small>Bs.</small> {{ $hosting_total }}</h4></td>
                                    <td class="text-right"><h4><small>Bs.</small> {{ $sales_total }}</h4></td>
                                    <td class="text-right"><h4><small>Bs.</small> {{ $penalties_total }}</h4></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edita daily payment modal --}}
    <form action="{{ route('people.defaulters.payment.store') }}" class="form-submit" id="form-add_payment_defaulters" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $person_defaulter->id }}">
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal fade" tabindex="-1" id="add_payment_defaulters-modal" role="dialog">
            <div class="modal-dialog modal-success">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-dollar"></i> Desea registrar la deuda como pagada?</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;"> &nbsp; Pago con QR/Transferencia</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-success btn-submit" value="Sí, pagar">
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('javascript')
    <script>
        $(document).ready(function () {

        });
    </script>
@stop
