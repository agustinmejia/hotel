@extends('voyager::master')

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    @php
                        $rooms = App\Models\Room::with(['type'])->get();
                        $floors = $rooms->groupBy('floor_number');
                    @endphp
                    <div class="row">

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
                                                        default:
                                                            $type = 'default';
                                                            $icon = 'voyager-warning';
                                                            break;
                                                    }
                                                @endphp
                                                <div class="col-md-2 col-sm-4">
                                                    <div class="panel-custom panel-{{ $type }}">
                                                        <div class="panel-body">
                                                            <div class="panel-number">
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
                                                                            break;
                                                                        case 'ocupada':
                                                                            $route = route('reservations.show', 1);
                                                                            break;
                                                                        default:
                                                                            $route = '';
                                                                            break;
                                                                    }
                                                                @endphp
                                                                <a href="{{ $route ? $route : '#' }}" @if(!$route) style="cursor: not-allowed;" @endif><b>{{ Str::upper($room->status) }} <i class="fa fa-arrow-circle-right"></i></b></a>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .panel-custom{
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
        .panel-number {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 5px 10px
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
    </style>
@stop

@section('javascript')
    <script>
        
    </script>
@stop
