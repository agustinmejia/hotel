@extends('voyager::master')

@section('page_title', 'Ver Persona')

@php
    $person = App\Models\Person::with(['reservations.details.room', 'reservations.details.days', 'city.state.country'])->where('id', $dataTypeContent->id)->first();
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
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function () {
            
        });
    </script>
@stop
