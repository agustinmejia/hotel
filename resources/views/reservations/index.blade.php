@extends('voyager::master')

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    @php
                        $cashier = App\Models\Cashier::with(['branch_office'])->where('user_id', Auth::user()->id)->where('status', 'abierta')->first();
                        $cashier_id = null;
                        $resort_branch_office_active = false;
                        $pool_adults_price = 0;
                        $pool_children_price = 0;
                        $sauna_price = 0;
                        if($cashier){
                            $cashier_id = $cashier->id;
                            $pool_adults_price = $cashier->branch_office->pool_adults_price;
                            $pool_children_price = $cashier->branch_office->pool_children_price;
                            $sauna_price = $cashier->branch_office->sauna_price;
                            $resort_branch_office_active = $cashier->branch_office->resort ? true : false;
                        }
                    @endphp
                    <form id="form-reservation" class="form-submit" action="{{ route('reservations.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="status" value="reservacion">
                        <input type="hidden" name="cashier_id" value="{{ $cashier_id }}">
                        <div class="panel-body">
                            @php
                                $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                                $rooms = App\Models\Room::with(['type', 'reservation_detail' => function($q){
                                    $q->whereRaw('(status = "ocupada" or status = "reservada")')->orderBy('status');
                                }, 'reservation_detail.days', 'reservation_detail.reservation.aditional_people'])
                                ->where('branch_office_id', Auth::user()->branch_office_id)->get();
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
                                            @if ($resort_branch_office_active)
                                            <li>
                                                <a data-toggle="tab" href="#tab-services" id="nav-tab-services"><b>Servicios</b></a>
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
                                            @if ($resort_branch_office_active)
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
                                                                        <td><span>Entrada a priscina adultos</span></td>
                                                                        <td>
                                                                            <h4>{{ $pool_adults_price == intval($pool_adults_price) ? intval($pool_adults_price) : $pool_adults_price }}</h4>
                                                                            <input type="hidden" name="pool_adults_price" value="{{ $pool_adults_price }}">
                                                                        </td>
                                                                        <td style="padding: 0px">
                                                                            <div class="number-input">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" class="minus">-</button>
                                                                                <input class="quantity" name="pool_adults_quantity" min="0" value="0" type="number">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right"><h4 id="label-pool_total_adults">0</h4></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span>Entrada a priscina niños</span></td>
                                                                        <td>
                                                                            <h4>{{ $pool_children_price == intval($pool_children_price) ? intval($pool_children_price) : $pool_children_price }}</h4>
                                                                            <input type="hidden" name="pool_children_price" value="{{ $pool_children_price }}">
                                                                        </td>
                                                                        <td style="padding: 0px">
                                                                            <div class="number-input">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" class="minus">-</button>
                                                                                <input class="quantity" name="pool_children_quantity" min="0" value="0" type="number">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right"><h4 id="label-pool_total_children">0</h4></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><span>Sauna</span></td>
                                                                        <td>
                                                                            <h4>{{ $sauna_price == intval($sauna_price) ? intval($sauna_price) : $sauna_price }}</h4>
                                                                            <input type="hidden" name="sauna_price" value="{{ $sauna_price }}">
                                                                        </td>
                                                                        <td style="padding: 0px">
                                                                            <div class="number-input">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" class="minus">-</button>
                                                                                <input class="quantity" name="sauna_quantity" min="0" value="0" type="number">
                                                                                <button type="button" onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus">+</button>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-right"><h4 id="label-sauna_total">0</h4></td>
                                                                    </tr>
                                                                    <tr style="background-color: #ebebeb">
                                                                        <td colspan="3"><h4>TOTAL</h4></td>
                                                                        <td class="text-right"><h4 id="label-total_sservices">0</h4></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            <div class="text-right">
                                                                {{-- <button type="reset" class="btn btn-default">Limpiar</button> --}}
                                                                <button type="button" class="btn btn-primary btn-submit-alt" @if(!$cashier) disabled title="No ha aperturado caja" @endif><i class="voyager-edit"></i> Registrar</button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <canvas id="myChart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-12" style="padding: 20px">
                                    <h1 class="text-center"><i class="fa fa-ban"></i> No hay habitaciones registradas</h1>
                                </div>

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
        table td span{
            font-size: 1.2rem
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var pool_adults_price = "{{ $pool_adults_price }}";
        var pool_children_price = "{{ $pool_children_price }}";
        var sauna_price = "{{ $sauna_price }}";
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

            $('.quantity').keyup(function(){
                calculateService();
            });

            $('.minus').click(function(){
                calculateService();
            });

            $('.plus').click(function(){
                calculateService();
            });

            $('.btn-submit-alt').click(() => {
                $(this).attr('disabled', 'disabled');
                $.post("{{ route('services.store') }}", $('#form-reservation').serialize(), res => {
                    $('.btn-submit-alt').removeAttr('disabled');
                    $('input[name="pool_adults_quantity"]').val(0);
                    $('input[name="pool_children_quantity"]').val(0);
                    $('input[name="sauna_quantity"]').val(0);
                    calculateService();
                    renderChart();
                    if(res.success){
                        toastr.success('Entradas registradas', 'Bien hecho!');
                    }else{
                        toastr.error('Entradas no registradas', 'Error');
                    }
                });
            })

            $('#nav-tab-services').click(function(){
                setTimeout(() => {
                    renderChart();
                }, 250);
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

        function calculateService(){
            let pool_adults_quantity = $('input[name="pool_adults_quantity"]').val();
            let pool_children_quantity = $('input[name="pool_children_quantity"]').val();
            let sauna_quantity = $('input[name="sauna_quantity"]').val();
            $('#label-pool_total_adults').text(pool_adults_price * pool_adults_quantity);
            $('#label-pool_total_children').text(pool_children_price * pool_children_quantity);
            $('#label-sauna_total').text(sauna_price * sauna_quantity);
            $('#label-total_sservices').text((pool_adults_price * pool_adults_quantity) + (pool_children_price * pool_children_quantity) + (sauna_price * sauna_quantity));
        }
    </script>

    <script>
        var myChart;
        var pool_total_adults = 0;
        var pool_total_children = 0;
        var total_sauna = 0;
        var backgroundColor = [
            'rgba(255, 159, 64, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(201, 203, 207, 0.5)'
        ];
        var borderColor = [
            'rgb(255, 159, 64)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
        ]
        const ctx = document.getElementById('myChart');
    
        function renderChart(){
            $.get("{{ route('services.list') }}", (res) => {
                pool_total_adults = res.pool_total_adults;
                pool_total_children = res.pool_total_children;
                total_sauna = res.total_sauna;
                if (myChart) {
                    // Actualiza los datos del gráfico con animación
                    myChart.data = {
                        labels: ['Piscina menores', 'Piscina adultos', 'Sauna'],
                        datasets: [{
                            label: 'Cant. personas',
                            data: [pool_total_adults, pool_total_children, total_sauna],
                            backgroundColor,
                            borderColor,
                            borderWidth: 1
                        }]
                    };
                    myChart.update({
                        duration: 500,
                        easing: 'easeInOutQuart',
                    });
                }else{
                    myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Piscina menores', 'Piscina adultos', 'Sauna'],
                            datasets: [{
                                label: 'Cant. personas',
                                data: [pool_total_adults, pool_total_children, total_sauna],
                                backgroundColor,
                                borderColor,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            // animation : false
                        }
                    });
                }
            });
        }
    </script>
@stop
