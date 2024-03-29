@extends('voyager::master')

@section('page_title', 'Registrar Hospedaje')

@section('page_header')
    <h1 class="page-title">
        <i class="fa fa-tag"></i>
        Registrar Hospedaje
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <form role="form" class="form-submit" action="{{ route('reservations.store') }}" method="POST">
            @if($room)
                
                @include('partials.check-cashier', ['cashier' => $cashier])

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12 div-details">
                            <table style="width: 100%; margin-top: 20px">
                                <tr style="height: 30px">
                                    <td style="width: 16%"><b>N&deg; de habitación : </b></td>
                                    <td style="width: 16%">{{ $room->code }}</td>
                                    <td style="width: 16%"><b>Tipo : </b></td>
                                    <td style="width: 16%">{{ $room->type->name }}</td>
                                    <td style="width: 16%"><b>Precio : </b></td>
                                    <td style="width: 16%">
                                        <span id="label-room-price">{{ $room->type->price == intval($room->type->price) ? intval($room->type->price) : $room->type->price }} &nbsp; <i class="voyager-edit" id="btn-edit-price" style="cursor: pointer" title="Editar precio"></i></span>
                                        <input type="hidden" name="room_price" class="form-control" id="input-price" onchange="getSubtotal()" onkeyup="getSubtotal()" value="{{ $room->type->price == intval($room->type->price) ? intval($room->type->price) : $room->type->price }}" step="1" required>
                                    </td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Descripción : </b></td>
                                    <td colspan="3">{{ $room->type->description }}</td>
                                    <td><b>Estado : </b></td>
                                    <td>{{ $room->status }}</td>
                                </tr>
                            </table>
                            <br>
                        </div>
                        <input type="hidden" name="room_id[]" value="{{ $room->id }}">
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        @csrf
                        <input type="hidden" name="status" value="en curso">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    @if(!$room)
                                        @php
                                            $rooms = App\Models\Room::with(['type'])->where('status', 'disponible')->orderBy('floor_number')->orderBy('code')->get();
                                        @endphp
                                        <div class="form-group col-md-12">
                                            <label class="control-label" for="room_id">Habitaciones</label>
                                            <select name="room_id[]" class="form-control select2" id="select-room_id" multiple required>
                                                @foreach ($rooms as $item)
                                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->type->name }} (Bs. {{ $item->type->price == floatval($item->type->price) ? intval($item->type->price) : $item->type->price }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endisset
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="select-person_id">Cliente/Huesped</label>
                                        <select name="person_id[]" class="form-control" id="select-person_id" multiple @if(setting('system.required_guest')) required @endif></select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label" for="start">Ingreso</label>
                                        <input type="date" name="start" id="input-start" value="{{ date('Y-m-d') }}" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label" for="finish">Salida</label>
                                        <input type="date" name="finish" id="input-finish" class="form-control">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="reason">Motivo de estadía</label>
                                        <select name="reason" class="form-control" id="select-reason">
                                            <option value="trabajo">trabajo</option>
                                            <option value="paseo">paseo</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="observation">Observaciones</label>
                                        <textarea name="observation" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Accesorios</label>
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>Descripción</th>
                                                    <th>Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (App\Models\RoomAccessory::where('status', 1)->get() as $item)
                                                    <tr id="tr-{{ $item->id }}" class="tr-accessories">
                                                        <td style="width: 50px" class="text-center">
                                                            <input type="checkbox" name="accessory_id[]" class="check-accessory" value="{{ $item->id }}" data-id="{{ $item->id }}" data-price="{{ intval($item->price) == floatval($item->price) ? intval($item->price) : $item->price }}" style="transform: scale(1.5);">
                                                        </td>
                                                        <td>
                                                            {{ $item->name }} (<b><small>Bs.</small> {{ intval($item->price) == floatval($item->price) ? intval($item->price) : $item->price }}</b>) {{ $item->description ? '<br><small>'.$item->description.'</small>' : '' }}
                                                        </td>
                                                        <td style="width: 150px">
                                                            <div class="input-group">
                                                                <input type="number" name="price[]" onchange="getSubtotal()" onkeyup="getSubtotal()" class="form-control" step="0.1" disabled>
                                                                <span class="input-group-addon">Bs.</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @foreach (App\Models\FoodType::where('status', 1)->get() as $item)
                                                    <tr id="tr-food-type-{{ $item->id }}" class="tr-food-type">
                                                        <td style="width: 50px" class="text-center">
                                                            <input type="checkbox" name="food_type_id[]" class="check-food_type_id" value="{{ $item->id }}" style="transform: scale(1.5);" checked>
                                                        </td>
                                                        <td>
                                                            {{ $item->name }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr style="height: 50px">
                                                    <td colspan="2" class="text-right">MONTO DIARIO</td>
                                                    <td>
                                                        @php
                                                            $room_price = $room ? $room->type->price : 0;
                                                        @endphp
                                                        <h3 class="text-right" id="label-subtotal">{{ $room_price == intval($room_price) ? intval($room_price) : $room_price }}</h3>
                                                        <input type="hidden" name="subtotal" id="input-subtotal" value="{{ $room_price == intval($room_price) ? intval($room_price) : $room_price }}">
                                                    </td>
                                                </tr>
                                                <tr style="height: 50px">
                                                    <td colspan="2" class="text-right">TOTAL</td>
                                                    <td>
                                                        <h3 class="text-right" id="label-total"></h3>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        @if (!$room)
                                            <small>Si selecciona un accesorio se le asignará a cada habitación</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <a href="{{ route('reception.index') }}" class="btn btn-default">Cancelar</a>
                            <button type="submit" class="btn btn-primary save btn-submit" @if(!$cashier) disabled title="Debe aperturar caja" @endif>Guardar <i class="voyager-check"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Create person modal --}}
    @include('partials.add-person-modal')

    {{-- Create cashier modal --}}
    @include('partials.add-cashier-modal', ['redirect' => $room ? 'admin/reservations/create?room_id='.$room->id : null])
@stop

@section('css')
    <style>
        .div-details b {
            font-weight: bold !important
        }
        .select2-selection--single {
            width: 100% !important
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var subtotal = $('#input-price').val();
        $(document).ready(function(){

            customSelect('#select-person_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, null, 'createPerson()');
            customSelect('#select-city_id', '{{ route("cities.search") }}', formatResultCities, data => data.name, "#person-modal", 'createCity()');

            $('#select-reason').select2({
                tags: true,
                createTag: function (params) {
                    return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                    }
                },
                templateResult: function (data) {
                    var $result = $("<span></span>");
                    $result.text(data.text);
                    if (data.newOption) {
                        $result.append(" <em>(ENTER para agregar)</em>");
                    }
                    return $result;
                },
                escapeMarkup: function(markup) {
                    return markup;
                },
            });

            $('#btn-edit-price').click(function(){
                $('#label-room-price').fadeOut('fast', function(){
                    $('#input-price').prop('type', 'number');
                });
            });

            $('.check-accessory').change(function(){
                let id = $(this).data('id');
                let price = $(this).data('price');
                if($(this).is(':checked')){
                    $(`#tr-${id} input[name="price[]"]`).val(price);
                    $(`#tr-${id} input[name="price[]"]`).prop('disabled', false)
                }else{
                    $(`#tr-${id} input[name="price[]"]`).val('');
                    $(`#tr-${id} input[name="price[]"]`).prop('disabled', true);
                }
                getSubtotal();
            });

            $('#input-start').change(function(){
                getTotal();
            });

            $('#input-finish').change(function(){
                getTotal();
            });
        });

        function getSubtotal(){
            let totalAccessories = 0;
            $('.tr-accessories input[name="price[]"]').each(function(){
                totalAccessories += $(this).val() ? parseFloat($(this).val()) : 0;
            });
            let price = $('#input-price').val() ? parseFloat($('#input-price').val()) : 0;
            subtotal = price + parseFloat(totalAccessories);
            $('#label-subtotal').text(subtotal);
            $('#input-subtotal').val(subtotal);
            getTotal();
        }

        function getTotal(){
            if (!$('#input-start').val() && $('#input-finish').val()) {
                $('#label-total').text('');
                return 0;
            }

            let start = new Date($('#input-start').val()).getTime();
            let finish    = new Date($('#input-finish').val()).getTime();
            if (start <= finish) {
                let diff = finish - start;
                let days = diff/(1000*60*60*24) +1;
                console.log(days)

                $('#label-total').text(days * subtotal);
            } else {
                $('#label-total').text('');
            }
        }

            function createPerson(){
                $('#select-person_id').select2('close');
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
