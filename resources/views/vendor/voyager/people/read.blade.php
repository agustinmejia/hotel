@extends('voyager::master')

@section('page_title', 'Ver Persona')

@php
    $cashier = App\Models\Cashier::where('status', 'abierta')->where('user_id', Auth::user()->id)->first();
    $person = App\Models\Person::with(['reservations.details.room', 'reservations.details.days', 'city.state.country', 'defaulters'])->where('id', $dataTypeContent->id)->first();
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $now = \Carbon\Carbon::now();
    $birthday = new \Carbon\Carbon($person->birthday);
    $age = $birthday->diffInYears($now);
@endphp

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-people"></i> Viendo Persona
        <a href="{{ route('voyager.people.index') }}" class="btn btn-warning">
            <span class="glyphicon glyphicon-list"></span>&nbsp;
            Volver a la lista
        </a>
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
                                <h3 class="panel-title">Nombre completo</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person->full_name }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">CI/NIT</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person->dni }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">N&deg; de celular</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person->phone }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Fecha de nacimiento</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>
                                    @if ($person->birthday)
                                        {{ date('d', strtotime($person->birthday)).'/'.$months[intval(date('m', strtotime($person->birthday)))].'/'.date('Y', strtotime($person->birthday)) }} - {{ $age }} años        
                                    @else
                                        No definida
                                    @endif
                                </p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Género</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person->gender }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Ocupación</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person->job ?? 'No definido' }}</p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Ciudad</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>
                                    {{ $person->city->name }}, 
                                    @if ($person->city->state)
                                        {{ $person->city->state->name }}
                                        @if ($person->city->state->country)
                                            - {{ $person->city->state->country->name }}            
                                        @endif
                                    @endif
                                </p>
                            </div>
                            <hr style="margin:0;">
                        </div>
                        <div class="col-md-6">
                            <div class="panel-heading" style="border-bottom:0;">
                                <h3 class="panel-title">Dirección</h3>
                            </div>
                            <div class="panel-body" style="padding-top:0;">
                                <p>{{ $person->address ?? 'No definida' }}</p>
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
                                    <th colspan="5"><h4>Detalles de hospedaje</h4></th>
                                </tr>
                                <tr>
                                    <th>N&deg;</th>
                                    <th>Fecha</th>
                                    <th>Habitación</th>
                                    <th>Cantidad de Días</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cont = 1;
                                @endphp
                                @forelse ($person->reservations->sortByDesc('start') as $item)
                                    @php
                                        $detail_reservation = $item->details[0];
                                    @endphp
                                    <tr>
                                        <td>{{ $cont }}</td>
                                        <td>{{ date('d', strtotime($item->start)).'/'.$months[intval(date('m', strtotime($item->start)))].'/'.date('Y', strtotime($item->start)) }}</td>
                                        <td>{{ $detail_reservation->room->code }}</td>
                                        <td>{{ $detail_reservation->days->count() }}</td>
                                        <td>
                                            @php
                                                switch ($item->status) {
                                                    case 'en curso':
                                                        $label = 'primary';
                                                        $status = $item->status;
                                                        break;
                                                    case 'finalizado':
                                                        $label = 'danger';
                                                        $status = $item->status;
                                                        break;
                                                    case 'reservacion':
                                                        $label = 'warning';
                                                        $status = 'reservación';
                                                        break;
                                                    default:
                                                        $label = 'default';
                                                        $status = $item->status ?? 'no definido';
                                                        break;
                                                }
                                            @endphp
                                            <label class="label label-{{ $label }}">{{ ucfirst($status) }}</label>
                                        </td>
                                    </tr>
                                    @php
                                        $cont++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="5"><h5>No hay registros</h5></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <table class="table table-bordered table-hover" id="dataTable">
                            <thead>
                                <tr>
                                    <th colspan="7"><h4>Deudas registradas</h4></th>
                                </tr>
                                <tr>
                                    <th>N&deg;</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Detalle</th>
                                    <th>Estado</th>
                                    <th class="text-right">Monto</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $cont = 1;
                                @endphp
                                @forelse ($person->defaulters as $item)
                                    <tr>
                                        <td>{{ $cont }}</td>
                                        <td>{{ date('d', strtotime($item->created_at)).'/'.$months[intval(date('m', strtotime($item->created_at)))].'/'.date('Y H:i', strtotime($item->created_at)) }}</td>
                                        <td>{{ $item->type == 1 ? 'Abandonó sin pagar' : 'Paga luego' }}</td>
                                        <td>{{ $item->observations }}</td>
                                        <td>
                                            @php
                                                switch ($item->status) {
                                                    case 'pendiente':
                                                        $label = 'danger';
                                                        $status = $item->status;
                                                        break;
                                                    case 'pagada':
                                                        $label = 'success';
                                                        $status = $item->status;
                                                        break;
                                                    default:
                                                        $label = 'default';
                                                        $status = $item->status ?? 'no definido';
                                                        break;
                                                }
                                            @endphp
                                            <label class="label label-{{ $label }}">{{ ucfirst($status) }}</label>
                                        </td>
                                        <td class="text-right">{{ $item->amount }}</td>
                                        <td class="no-sort no-click bread-actions text-right">
                                            @if ($item->status == 'pendiente')
                                                <button class="btn btn-sm btn-success btn-payment-defaulter" data-id="{{ $item->id }}" data-toggle="modal" data-target="#add_payment_defaulters-modal" @if(!$cashier) disabled title="Debe abrir caja primero" @else title="Pagar deuda" @endif>
                                                    <i class="voyager-dollar"></i> <span class="hidden-xs hidden-sm">Pagar</span>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $cont++;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="5"><h5>No hay registros</h5></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edita daily payment modal --}}
    <form action="{{ route('people.defaulters.payment.store') }}" class="form-submit" id="form-add_payment_defaulters" method="POST">
        @csrf
        <input type="hidden" name="id">
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
            $('.btn-payment-defaulter').click(function(){
                let id = $(this).data('id');
                $('#form-add_payment_defaulters input[name="id"]').val(id);
            });
        });
    </script>
@stop
