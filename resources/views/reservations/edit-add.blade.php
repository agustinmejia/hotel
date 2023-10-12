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
                                        <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    @endisset
                                    <div class="form-group col-md-12">
                                        <label class="control-label" for="person_id">Cliente/Huesped</label>
                                        <select name="person_id" class="form-control" id="select-person_id" required></select>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label" for="start">Ingreso</label>
                                        <input type="date" name="start" value="{{ date('Y-m-d') }}" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="control-label" for="finish">Salida</label>
                                        <input type="date" name="finish" class="form-control">
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
                                                            {{ $item->name }} {{ $item->description ? '<br><small>'.$item->description.'</small>' : '' }}
                                                        </td>
                                                        <td style="width: 150px">
                                                            <div class="input-group">
                                                                <input type="number" name="price[]" onchange="getTotal()" onkeyup="getTotal()" class="form-control" step="0.1" disabled>
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
                                                <tr>
                                                    <td colspan="2" class="text-right">TOTAL</td>
                                                    <td>
                                                        <h3 class="text-right" id="label-total">{{ $room->type->price }}</h3>
                                                        <input type="hidden" name="total" id="input-total" value="{{ $room->type->price }}">
                                                    </td>
                                                </tr>
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
    <form action="#" id="form-person" class="form-submit" method="POST">
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
                            <label for="first_name">Nombre completo</label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="ci">CI/NIT</label>
                            <input type="text" name="ci" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="cell_phone">N&deg; de celular</label>
                            <input type="text" name="cell_phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="birth_date">Fecha de nac.</label>
                            <input type="date" name="birth_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="origin">Procedencia</label>
                            <input type="text" name="origin" class="form-control">
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

    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var total = parseFloat("{{ $room->type->price }}");
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
                getTotal();
            });
        });

        function getTotal(){
            let totalAccessories = 0;
            $('.tr-accessories input[name="price[]"]').each(function(){
                totalAccessories += $(this).val() ? parseFloat($(this).val()) : 0;
            });
            $('#label-total').text(total + parseFloat(totalAccessories));
            $('#input-total').val(total + parseFloat(totalAccessories));
        }

        function createPerson(){
            $('#select-person_id').select2('close');
            $('#person-modal').modal('show');
        }
    </script>
@stop
