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
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <form role="form" class="form-submit" action="{{ route('reservations.store') }}" method="POST">
                        @csrf
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    @isset($room)
                                        <div class="col-md-12 div-details">
                                            <table style="width: 100%; margin-top: 20px">
                                                <tr style="height: 30px">
                                                    <td><b>N&deg; de habitación : </b></td>
                                                    <td>{{ $room->code }}</td>
                                                    <td><b>Tipo : </b></td>
                                                    <td>{{ $room->type->name }}</td>
                                                    <td><b>Precio : </b></td>
                                                    <td>
                                                        {{ $room->type->price }}
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
                                        <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    @endisset
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="person_id">Cliente/Huesped</label>
                                        <select name="person_id" class="form-control" id="select-person_id" required></select>
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
                                                        <h3 class="text-right" id="label-subtotal">{{ $room->type->price }}</h3>
                                                        <input type="hidden" name="subtotal" id="input-subtotal" value="{{ $room->type->price }}">
                                                    </td>
                                                </tr>
                                                <tr style="height: 50px">
                                                    <td colspan="2" class="text-right">TOTAL</td>
                                                    <td>
                                                        <h3 class="text-right" id="label-total"></h3>
                                                    </td>
                                                </tr>
                                                {{-- <tr style="height: 50px">
                                                    <td colspan="2" class="text-right">ADELANTO</td>
                                                    <td>
                                                        <input type="text" name="initial_amount" class="form-control" value="0">
                                                    </td>
                                                </tr> --}}
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary save btn-submit">Guardar <i class="voyager-check"></i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Create type items modal --}}
    <form action="{{ route('people.store').'?ajax=1' }}" id="form-person" class="form-submit" method="POST">
        @csrf
        <div class="modal modal-primary fade" tabindex="-1" id="person-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-tag"></i> Registrar huesped</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="full_name">Nombre completo</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="dni">CI/NIT</label>
                            <input type="text" name="dni" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">N&deg; de celular</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="birthday">Fecha de nac.</label>
                            <input type="date" name="birthday" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="origin">Procedencia</label>
                            <input type="text" name="origin" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="job">Ocupación</label>
                            <input type="text" name="job" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="street">Dirección</label>
                            <textarea name="street" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-dark btn-submit">Guardar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        .div-details b {
            font-weight: bold !important
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var price = parseFloat("{{ $room->type->price }}");
        var subtotal = price;
        $(document).ready(function(){

            customSelect('#select-person_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, null, 'createPerson()');

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

            $('#form-person').submit(function(e){
                e.preventDefault();
                $.post($(this).attr('action'), $(this).serialize(), function(res){
                    if (res.success) {
                        toastr.success('Huesped registrado', 'Bien hecho');
                        $('.form-submit .btn-submit').removeAttr('disabled');
                        $(this).trigger('reset');
                        $('#person-modal').modal('hide');
                    } else {
                        toastr.error('Ocurrió un error', 'Error');
                    }
                });
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
    </script>
@stop
