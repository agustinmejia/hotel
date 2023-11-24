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
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12 div-details">
                            <table style="width: 100%; margin-top: 20px">
                                <tr style="height: 30px">
                                    <td><b>N&deg; de habitación : </b></td>
                                    <td>{{ $room->code }}</td>
                                    <td><b>Tipo : </b></td>
                                    <td>{{ $room->type->name }}</td>
                                    <td><b>Precio : </b></td>
                                    <td>
                                        {{ $room->type->price == intval($room->type->price) ? intval($room->type->price) : $room->type->price }}
                                        <input type="hidden" name="room_price" value="{{ $room->type->price }}">
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
                                        <select name="person_id[]" class="form-control" id="select-person_id" multiple required></select>
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
                                        <label class="control-label" for="reason">Motivo de viaje</label>
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
                                                @forelse (App\Models\RoomAccessory::where('status', 1)->get() as $item)
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
                                                        </th>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3"><h4 class="text-center">No hay accesorios disponibles</h4></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot>
                                                <tr style="height: 50px">
                                                    <td colspan="2" class="text-right">MONTO DIARIO</td>
                                                    <td>
                                                        <h3 class="text-right" id="label-subtotal">{{ $room ? $room->type->price : 0 }}</h3>
                                                        <input type="hidden" name="subtotal" id="input-subtotal" value="{{ $room ? $room->type->price : 0 }}">
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
                            <button type="submit" class="btn btn-primary save btn-submit">Guardar <i class="voyager-check"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Create person modal --}}
    @include('partials.add-person-modal')
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
        var price = parseFloat("{{ $room ? $room->type->price : 0 }}");
        var subtotal = price;
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
