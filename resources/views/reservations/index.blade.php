@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <form id="form-reservation" class="form-submit" action="{{ route('reservations.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="status" value="reservacion">
                        <div class="panel-body">
                            @php
                                $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                                $rooms = App\Models\Room::with(['type', 'reservation_detail' => function($q){
                                    $q->whereRaw('(status = "ocupada" or status = "reservada")')->orderBy('status');
                                }, 'reservation_detail.days', 'reservation_detail.reservation.aditional_people'])->get();
                                $groups = $rooms->groupBy(setting('system.group_by'));

                                switch (setting('system.group_by')) {
                                    case 'floor_number':
                                        $label_group_by = 'Piso N&deg;';
                                        break;
                                    default:
                                        $label_group_by = '';
                                        break;
                                }
                            @endphp
                            @if ($groups->count())
                                <div class="panel" style="margin: 0px">
                                    <div class="page-content settings container-fluid">
                                        <ul class="nav nav-tabs">
                                            @php
                                                $active = 'active';
                                            @endphp
                                            @foreach($groups as $group => $item)
                                                <li class="{{ $active }}">
                                                    <a data-toggle="tab" href="#tab-{{ $group }}">{!! $label_group_by !!} {{ Str::ucfirst($group) }}</a>
                                                </li>
                                                @php
                                                    $active = '';
                                                @endphp
                                            @endforeach
                                            @if (setting('services.status'))
                                            <li>
                                                <a data-toggle="tab" href="#tab-services">Servicios</a>
                                            </li>
                                            @endif
                                        </ul>
                                        <div class="tab-content">
                                            @php
                                                $active = 'active';
                                            @endphp
                                            @foreach($groups as $group => $rooms)
                                                <div id="tab-{{ $group }}" class="tab-pane fade in {{ $active }}">
                                                    @foreach ($rooms as $room)
                                                        @php
                                                            $finish_date = null;
                                                            $reservation_date = null;
                                                            $person_quantity = 0;
                                                            if($room->reservation_detail->count()){
                                                                $reservation = $room->reservation_detail->first()->reservation;
                                                                // Si está ocupada se obtiene el número de personas
                                                                if($room->status == 'ocupada'){
                                                                    $person_quantity = $reservation->aditional_people->count() +1;
                                                                }
                                                                // Calcular la fecha de salida
                                                                if(date('Y-m-d') < $reservation->finish){
                                                                    if($room->status == 'ocupada'){
                                                                        $finish_date = $reservation->finish;
                                                                        $person_quantity = $reservation->aditional_people->count() +1;
                                                                    }elseif($room->status == 'reservada'){
                                                                        $reservation_date = $reservation->start;
                                                                    }
                                                                }
                                                            }
                                                            switch ($room->status) {
                                                                case 'disponible':
                                                                    $type = 'success';
                                                                    $icon = 'fa fa-key';
                                                                    break;
                                                                case 'ocupada':
                                                                    $type = 'primary';
                                                                    $icon = 'fa fa-bed';
                                                                    break;
                                                                case 'reservada':
                                                                    $type = 'warning';
                                                                    $icon = 'fa fa-edit';
                                                                    break;
                                                                case 'fuera de servicio':
                                                                    $type = 'danger';
                                                                    $icon = 'fa fa-ban';
                                                                    break;
                                                                case 'limpieza':
                                                                    $type = 'dark';
                                                                    $icon = 'fa fa-clock-o';
                                                                    break;
                                                                default:
                                                                    $type = 'default';
                                                                    $icon = 'voyager-warning';
                                                                    break;
                                                            }
                                                        @endphp
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="panel-custom panel-{{ $type }}" @if ($finish_date || $reservation_date || $person_quantity) data-toggle="tooltip" data-placement="bottom" title="@if($finish_date) Sale el {{ date('d', strtotime($finish_date)) }} de {{ $months[intval(date('m', strtotime($finish_date)))] }} | @elseif($reservation_date) Reservado para el {{ date('d', strtotime($reservation_date)) }} de {{ $months[intval(date('m', strtotime($reservation_date)))] }} | @endif {{ $person_quantity }} {{ $person_quantity > 1 ? 'personas' : 'persona' }}" @endif>
                                                                <div class="panel-checkbox">
                                                                    <i class="fa fa-check-circle text-white label-check" id="label-check-{{ $room->id }}"></i>
                                                                    <input type="checkbox" name="room_id[]" class="checkbox-select" id="checkbox-select-{{ $room->id }}" value="{{ $room->id }}" style="transform: scale(1.2);" readonly>
                                                                </div>
                                                                <div class="panel-body">
                                                                    <div class="panel-number" data-id="{{ $room->id }}">
                                                                        <div>
                                                                            N&deg; <br>
                                                                            <span style="font-size: 35px">{{ $room->code }}</span> <br>
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <br>
                                                                            <i class="icon {{ $icon }}"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div style="margin: 5px">
                                                                        @php
                                                                            $room_price = $room->type->price;
                                                                            if($room->status == 'ocupada'){
                                                                                if($room->reservation_detail->count()){
                                                                                    $room_price = $room->reservation_detail[0]->price;
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <b>{{ $room->type->name }} <br> {{ $room_price == intval($room_price) ? intval($room_price) : $room_price }} <small>Bs.</small></b>
                                                                    </div>
                                                                    <hr style="margin: 0px">
                                                                    <div class="text-center" style="padding-top: 5px">
                                                                        @php
                                                                            switch ($room->status) {
                                                                                case 'disponible':
                                                                                    $route = route('reservations.create').'?room_id='.$room->id;
                                                                                    $modal = '';
                                                                                    break;
                                                                                case 'ocupada':
                                                                                    $route = route('reservations.show', $room->reservation_detail[0]->reservation_id).'?room_id='.$room->id;
                                                                                    $modal = '';
                                                                                    break;
                                                                                default:
                                                                                    $route = '';
                                                                                    $modal = '#enable-modal';
                                                                                    break;
                                                                            }
                                                                        @endphp
                                                                        <a href="{{ $route ? $route : '#' }}" style="padding: 10px 5px" @if(!$route && !$modal) style="cursor: not-allowed;" @endif @if($modal) data-toggle="modal" data-target="{{ $modal }}" data-id="{{ $room->id }}" class="btn-enable" @endif ><b>{{ Str::upper($room->status) }} <i class="fa fa-arrow-circle-right"></i></b></a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @php
                                                    $active = '';
                                                @endphp
                                            @endforeach
                                            @if (setting('services.status'))
                                                <div id="tab-services" class="tab-pane fade in">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Servicio</th>
                                                                        <th>Precio</th>
                                                                        <th>Cantidad</th>
                                                                        <th>Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><h4>Entrada a priscina adultos</h4></td>
                                                                        <td><h4>{{ setting('services.pool_price_adults') }}</h4></td>
                                                                        <td style="padding: 0px">
                                                                            {{-- <input type="number" name="pool_price_adults" class="form-control" step="1" min="0" style="width: 100px; font-size: 15px"> --}}
                                                                            <div class="number-input">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" class="minus">-</button>
                                                                                <input class="quantity" name="pool_price_adults" min="0" name="quantity" value="0" type="number">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right"><h4>0</h4></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><h4>Entrada a priscina niños</h4></td>
                                                                        <td><h4>{{ setting('services.pool_price_children') }}</h4></td>
                                                                        <td style="padding: 0px">
                                                                            {{-- <input type="number" name="pool_price_children" class="form-control" step="1" min="0" style="width: 100px; font-size: 15px"> --}}
                                                                            <div class="number-input">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" class="minus">-</button>
                                                                                <input class="quantity" name="pool_price_children" min="0" name="quantity" value="0" type="number">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right"><h4>0</h4></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><h4>Sauna</h4></td>
                                                                        <td><h4>{{ setting('services.sauna_price') }}</h4></td>
                                                                        <td style="padding: 0px">
                                                                            {{-- <input type="number" name="sauna_price" class="form-control" step="1" min="0" style="width: 100px; font-size: 15px"> --}}
                                                                            <div class="number-input">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" class="minus">-</button>
                                                                                <input class="quantity" name="sauna_price" min="0" name="quantity" value="0" type="number">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right"><h4>0</h4></td>
                                                                    </tr>
                                                                    <tr style="background-color: #ebebeb">
                                                                        <td colspan="3"><h4>TOTAL</h4></td>
                                                                        <td class="text-right"><h4>0</h4></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div class="text-right">
                                                                <button type="reset" class="btn btn-default">Limpiar</button>
                                                                <button type="submit" class="btn btn-primary btn-submit"><i class="voyager-edit"></i> Registrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <h1 class="text-center">No hay habitaciones registradas</h1>
                            @endif
                            <div class="col-md-12 text-right div-actions" style="display: none">
                                <button type="reset" class="btn btn-default">Cancelar</button>
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#reservation-modal">Reservar <i class="fa fa-tag"></i></button>
                            </div>
                        </div>

                        <div class="modal modal-success fade" tabindex="-1" id="reservation-modal" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><i class="fa fa-tag"></i> Registrar reserva</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label class="control-label" for="select-person_id">Cliente/Huesped</label>
                                            <select name="person_id[]" class="form-control" id="select-person_id" required></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="date">Fecha</label>
                                            <input type="date" name="start" class="form-control" value="{{ date('Y-m-d', strtotime(date('Y-m-d').' +1 days')) }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="observation">Observaciones</label>
                                            <textarea name="observation" class="form-control" rows="3" placeholder="Escribir observaciones..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-success btn-submit">Sí, reservar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- enable modal --}}
    <form action="#" id="enable-form" class="form-submit" method="POST">
        @csrf
        <input type="hidden" name="id">
        <div class="modal fade" tabindex="-1" id="enable-modal" role="dialog">
            <div class="modal-dialog modal-success">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-check"></i> Desea habilitar la siguiente habitación?</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="employe_id">Limpieza realizada por</label>
                            <select name="employe_id" id="select-employe_id" class="form-control">
                                <option value="">--Seleccionar empleado(a)--</option>
                                @foreach (App\Models\Employe::where('status', 1)->get() as $item)
                                    <option value="{{ $item->id }}">{{ $item->full_name }} - {{ $item->job->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-success btn-submit" value="Sí, habilitar">
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Create person modal --}}
    @include('partials.add-person-modal')
@stop

@section('css')
    <style>
        .panel-custom{
            position: relative;
            border-radius: 10px 10px 0px 0px;
            padding: 10px 0px;
            margin-bottom: 10px
        }
        .panel-custom:hover {
            filter: brightness(90%);
        }
        .panel-custom a {
            color: white
        }
        .panel-body {
            padding: 0px;
        }
        .panel-primary {
            background-color: #337AB7 !important;
            color: white
        }
        .panel-success {
            background-color: #1CC88A !important;
            color: white
        }
        .panel-warning {
            background-color: #e2a816 !important;
            color: white
        }
        .panel-danger {
            background-color: #DC392D !important;
            color: white
        }
        .panel-dark {
            background-color: #526069 !important;
            color: white
        }
        .panel-number {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 5px;
        }
        .panel-number .icon {
            font-size: 40px
        }
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover{
            background:#fff !important;
            color:#62a8ea !important;
            border-bottom:1px solid #fff !important;
            top:-1px !important;
        }
        .panel-checkbox{
            position: absolute;
            right: 10px;
            top: 10px;
        }
        .panel-checkbox .label-check{
            display: none;
            font-size: 20px
        }
        .panel-checkbox input{
            display: none;
        }

        input[type="number"] {
            -webkit-appearance: textfield;
            -moz-appearance: textfield;
            appearance: textfield;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
        }

        .number-input {
            border: 2px solid #ddd;
            display: inline-flex;
        }

        .number-input,
        .number-input * {
            box-sizing: border-box;
            text-align: center;
        }

        .number-input input {
            width: 50px;
            font-size: 20px;
            font-weight: 500
        }

        .number-input button {
            outline:none;
            -webkit-appearance: none;
            background-color: transparent;
            border: none;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            cursor: pointer;
            margin: 0;
            position: relative;
        }

        .number-input button {
            font-weight: 900;
            font-size: 20px;
            background-color: rgba(28,200,138, 1);
            color: white
        }
        .number-input button:hover {
            background: rgb(25, 179, 122)
        }
        .number-input button:active {
            background-color: rgba(28,200,138, 0.9)
        }
        table th {
            font-size: 12px
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var EnableClick = false;
        $(document).ready(function(){

            customSelect('#select-person_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, '#reservation-modal', 'createPerson()');
            customSelect('#select-city_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, "#person-modal", 'createCity()');

            $('#select-employe_id').select2({
                dropdownParent: $('#enable-modal')
            });

            $('.btn-enable').click(function(){
                let id = $(this).data('id');
                $('#enable-form').attr('action', '{{ url("admin/rooms") }}/'+id+'/update/status');
            });

            $('.panel-custom .panel-number').on('dblclick', function() {
                let id = $(this).data('id');
                EnableClick = true;
                changeDivActions(id);
            });

            $('.panel-custom .panel-number').on('click', function() {
                let id = $(this).data('id');
                if (EnableClick) {
                    changeDivActions(id);   
                }
            });

            $('#form-reservation').on('reset', function(){
                $('.label-check').fadeOut('fast');
                $('.div-actions').fadeOut('fast');
            });
        });

        function changeDivActions(id){
            $('#checkbox-select-'+id).trigger('click');

            if($('#checkbox-select-'+id).is(':checked')){
                $('#label-check-'+id).fadeIn('fast');
            }else{
                $('#label-check-'+id).fadeOut('fast');
            }
            let checked = false;
            $('.checkbox-select').each(function(index) {
                if($(this).is(':checked')){
                    checked = true;
                };
            });

            if(checked){
                $('.div-actions').fadeIn('fast');
                EnableClick = true;
            }else{
                $('.div-actions').fadeOut('fast');
                EnableClick = false;
            }
        }

        function createPerson(){
            $('#select-person_id').select2('close');
            $('#reservation-modal').modal('hide');
            $('#person-modal').modal('show');
        }

        function createCity(){
            $('#select-city_id').select2('destroy');
            $('#select-city_id').fadeOut('fast', function(){
                $('#input-city_name').fadeIn('fast');
                $('#input-city_name').prop('required', true);
            });
        }
    </script>
@stop
