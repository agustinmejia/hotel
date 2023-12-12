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
                                }, 'reservation_detail.days', 'reservation_detail.reservation'])->get();
                                $floors = $rooms->groupBy('floor_number');
                            @endphp
                            @if ($floors->count())
                                <div class="panel" style="margin: 0px">
                                    <div class="page-content settings container-fluid">
                                        <ul class="nav nav-tabs">
                                            @php
                                                $active = 'active';
                                            @endphp
                                            @foreach($floors as $floor => $item)
                                                <li class="{{ $active }}">
                                                    <a data-toggle="tab" href="#tab-{{ $floor }}">Piso N&deg; {{ $floor }}</a>
                                                </li>
                                                @php
                                                    $active = '';
                                                @endphp
                                            @endforeach
                                        </ul>
                                        <div class="tab-content">
                                            @php
                                                $active = 'active';
                                            @endphp
                                            @foreach($floors as $floor => $rooms)
                                                <div id="tab-{{ $floor }}" class="tab-pane fade in {{ $active }}">
                                                    @foreach ($rooms as $room)
                                                        @php
                                                            $finish_date = null;
                                                            $reservation_date = null;
                                                            if($room->reservation_detail->count()){
                                                                $last_day = $room->reservation_detail->first()->days;
                                                                if($last_day->count()){
                                                                    $finish_date = $last_day->sortByDesc('date')->first()->date;
                                                                }elseif($room->reservation_detail->first()->reservation->status == 'reservacion'){
                                                                    $reservation_date = $room->reservation_detail->first()->reservation->start;
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
                                                            <div class="panel-custom panel-{{ $type }}" @if ($finish_date || $reservation_date) data-toggle="tooltip" data-placement="bottom" title="@if($finish_date) Sale el {{ date('d', strtotime($finish_date)) }} de {{ $months[intval(date('m', strtotime($finish_date)))] }} @else Reservado para el {{ date('d', strtotime($reservation_date)) }} de {{ $months[intval(date('m', strtotime($reservation_date)))] }} @endif" @endif>
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
                                                                        <b>{{ $room->type->name }} {{ $room->type->price == intval($room->type->price) ? intval($room->type->price) : $room->type->price }} <small>Bs.</small></b>
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
    <form action="#" id="enable-form" method="POST">
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
                        <input type="submit" class="btn btn-success" value="Sí, habilitar">
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
