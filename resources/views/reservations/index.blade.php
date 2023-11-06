@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <form id="form-reservation" action="#" method="post">
                        <div class="panel-body">
                            @php
                                $rooms = App\Models\Room::with(['type'])->get();
                                $floors = $rooms->groupBy('floor_number');
                            @endphp
                            @if ($floors->count())
                                <div class="panel">
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
                                                            <div class="panel-custom panel-{{ $type }}">
                                                                @if ($room->status == 'disponible')
                                                                    <div class="panel-checkbox">
                                                                        <i class="fa fa-check-circle text-white label-check" id="label-check-{{ $room->id }}"></i>
                                                                        <input type="checkbox" name="room_id[]" class="checkbox-select" id="checkbox-select-{{ $room->id }}" value="{{ $room->id }}" style="transform: scale(1.2);" readonly>
                                                                    </div>
                                                                @endif
                                                                <div class="panel-body">
                                                                    <div class="panel-number" data-id="{{ $room->id }}">
                                                                        <div>
                                                                            N&deg; <br>
                                                                            <span style="font-size: 35px">{{ $room->code }}</span> <br>
                                                                            <span>{{ $room->type->name }}</span>
                                                                        </div>
                                                                        <div class="text-right">
                                                                            <br>
                                                                            <i class="icon {{ $icon }}"></i>
                                                                        </div>
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
                                                                                    $route = route('reservations.show', $room->id);
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
                                <button type="button" class="btn btn-primary">Reservar</button>
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-success" value="Sí, habilitar">
                    </div>
                </div>
            </div>
        </div>
    </form>
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
            padding: 5px 10px;
            cursor: pointer;
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
    <script>
        $(document).ready(function(){
            $('.btn-enable').click(function(){
                let id = $(this).data('id');
                $('#enable-form').attr('action', '{{ url("admin/rooms") }}/'+id+'/update/status');
            });
        });
        // $('.panel-custom .panel-number').on('mousedown', function() {
        //     let id = $(this).data('id');
        //     $('#checkbox-select-'+id).trigger('click');

        //     if($('#checkbox-select-'+id).is(':checked')){
        //         $('#label-check-'+id).fadeIn('fast');
        //     }else{
        //         $('#label-check-'+id).fadeOut('fast');
        //     }

        //     let checked = false;
        //     $('.checkbox-select').each(function(index) {
        //         if($(this).is(':checked')){
        //             checked = true;
        //         };
        //     });
        //     if(checked){
        //         $('.div-actions').fadeIn('fast');
        //     }else{
        //         $('.div-actions').fadeOut('fast');
        //     }
        // });

        $('#form-reservation').on('reset', function(){
            $('.label-check').fadeOut('fast');
            $('.div-actions').fadeOut('fast');
        });
    </script>
@stop
