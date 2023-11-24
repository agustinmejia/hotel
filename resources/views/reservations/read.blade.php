@extends('voyager::master')

@section('page_title', 'Registrar Hospedaje')

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
@endphp

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <br>
            <div class="col-md-6" style="padding-left: 15px">
                <a href="{{ route('reservations.index') }}" class="btn btn-warning"><i class="fa fa-arrow-circle-left"></i> Volver</a>
            </div>
            <div class="col-md-6 text-right" style="padding-right: 15px">
                @if ($reservation->details->where('status', 'ocupada')->count() > 0)
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#close-reservation-modal">Cerrar <i class="voyager-lock"></i></button>
                @endif
            </div>
        </div>

        @include('partials.check-cashier', ['cashier' => $cashier])

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12 div-details">
                                <b>DETALLES DE HOSPEDAJE</b>
                                <table style="width: 100%; margin-top: 20px">
                                    <tr>
                                        <td><b>Huesped(es):</b></td>
                                        <td>
                                            {{ $reservation->person->full_name }}
                                            @if ($reservation->aditional_people->count() > 0)
                                                @php
                                                    $cont = 1;
                                                @endphp
                                                @foreach ($reservation->aditional_people as $item)
                                                    {{ $reservation->aditional_people->count() == $cont ? ' y ' : ', ' }} {{ $item->person->full_name }}
                                                    @php
                                                        $cont++;
                                                    @endphp
                                                @endforeach
                                            @endif
                                        </td>
                                        <td><b>Fecha de registro:</b></td>
                                        <td>{{ $days[date('w', strtotime($reservation->start))] }}, {{ date('d', strtotime($reservation->start)) }} de {{ $months[intval(date('m', strtotime($reservation->start)))] }} de {{ date('Y', strtotime($reservation->start)) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="dataTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>N&deg;</th>
                                            <th>Habitación</th>
                                            <th>Gastos</th>
                                            <th>Estado</th>
                                            <th>Deuda</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cont = 1;
                                            $total = 0;
                                        @endphp
                                        @foreach ($reservation->details as $detail)
                                            <tr>
                                                <td>{{ $cont }}</td>
                                                <td>
                                                    {{ $detail->room->code }} | <small>{{ $detail->room->type->name }}</small> <br>
                                                    <label class="label label-default">{{ $detail->days->count() }} {{ $detail->days->count() > 1 ? 'días' : 'día' }} de hospedaje</label>
                                                </td>
                                                <td>
                                                    <ul>
                                                        @php
                                                            $amount_days = $detail->days->where('status', 'pendiente')->sum('amount');
                                                            $amount_penalties = $detail->penalties->where('status', 'pendiente')->sum('amount');
                                                        @endphp
                                                        <li>{{ $detail->days->where('status', 'pendiente')->count() }} {{ $detail->days->where('status', 'pendiente')->count() > 1 ? 'días adeudados' : 'día adeudado' }} | <b>{{ $amount_days }} <small>Bs.</small></b></li>
                                                        @php
                                                            $sales_amount = 0;
                                                            foreach($detail->sales as $sale){
                                                                foreach($sale->details as $sale_detail){
                                                                    if($sale_detail->status == 'pendiente'){
                                                                        $sales_amount += $sale_detail->price * $sale_detail->quantity;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        @if ($sales_amount > 0)
                                                            <li>Deuda de productos consumidos por un valor de <b>{{ $sales_amount }} <small>Bs.</small></b></li>
                                                        @endif
                                                        @if ($amount_penalties > 0)
                                                            <li>Multas por un valor de <b>{{ $amount_penalties }} <small>Bs.</small></b></li>
                                                        @endif
                                                    </ul>
                                                </td>
                                                <td>
                                                    @php
                                                        switch ($detail->status) {
                                                            case 'ocupada':
                                                                $label = 'primary';
                                                                break;
                                                            case 'finalizada':
                                                                $label = 'danger';
                                                                break;
                                                            case 'reservada':
                                                                $label = 'warning';
                                                                break;
                                                            
                                                            default:
                                                                $label = 'default';
                                                                break;
                                                        }
                                                    @endphp
                                                    <label class="label label-{{ $label }}">{{ ucfirst($detail->status) }}</label>
                                                </td>
                                                <td class="text-right">{{ $amount_days + $sales_amount + $amount_penalties }}</td>
                                                <td class="no-sort no-click bread-actions text-right">
                                                    @if (Auth::user()->hasPermission('read_reservations') && $detail->status != 'reservada')
                                                        <a href="{{ route('reservations.show', $reservation->id).'?room_id='.$detail->room_id }}&disable_close=true" title="Ver" class="btn btn-sm btn-warning" target="_blank">
                                                            <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php
                                                $cont++;
                                                $total += $amount_days + $sales_amount + $amount_penalties;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right">TOTAL</td>
                                            <td class="text-right"><h4><small>Bs.</small> {{ $total }}</h4></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Close reservation modal --}}
    <form action="{{ route('reservations.close') }}" id="form-close-reservation" class="form-submit" method="POST">
        @csrf
        {{-- Enviar solo las habitaciones ocupadas --}}
        @foreach ($reservation->details->where('status', 'ocupada') as $item)
            <input type="hidden" name="reservation_detail_id[]" value="{{ $item->id }}">
        @endforeach
        <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
        <div class="modal modal-danger fade" tabindex="-1" id="close-reservation-modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-tags"></i> Cierre de hospedaje</h4>
                    </div>
                    <div class="modal-body">
                        @if ($total)
                        <div class="form-group">
                            <p>Al cerrar el hospedaje se acepta que se han realizado el pago de toda la deuda, desea continuar?</p>
                            <h3 class="text-danger text-right"><span style="font-size: 12px">Deuda Bs. </span>{{ number_format($total, 2, ',', '.') }}</h3>
                        </div>
                        <div class="form-group text-right">
                            <label class="checkbox-inline"><input type="checkbox" name="payment_qr" value="1" title="En caso de que el pago no sea en efectivo" style="transform: scale(1.5); accent-color: #e74c3c;">Pago con Qr</label>
                        </div>
                        @else
                        <div class="form-group">
                            <p>Está a punto de cerar el hospedaje y desalojar {{ $reservation->details->count() > 1 ? 'las habitaciones' : 'la habitación' }}, desea continuar?</p>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger btn-submit">Cerrar <i class="fa fa-tags"></i></button>
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
        .select2{
            width: 100% !important;
        }
        .table-products h4 {
            margin: 0px !important
        }
        #products-details .form-control {
            padding: 0px 5px !important;
            height: 25px;
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var productSelected = null;
        var user = @json(Auth::user());
        var cashier = @json($cashier);
        $(document).ready(function(){
            customSelect('#select-product', '{{ route("products.search") }}', formatResultProducts, data => { productSelected = data; return data.name}, '#add-product-modal', null);
            $('#select-branch_office_id').select2({dropdownParent: '#create-cashier-modal'});

            if (user.branch_office_id) {
                $('#select-branch_office_id').val(user.branch_office_id).trigger('change');
            }

            $('#add-product-modal').on('shown.bs.modal', function () {
                $('#products-details .tr-item').remove();
                setNumber();
                $('#label-total').text('0.00');
                $('#label-payment').text('0.00');
                $('#label-debt').text('0.00');
            });

            $('#select-product').change(function(){
                let product = productSelected;
                $('#products-details').append(`
                    <tr id="tr-item-${product.id}" class="tr-item">
                        <td class="td-number"></td>
                        <td>
                            ${product.name}
                            <input type="hidden" name="product_id[]" value="${product.id}" />
                        </td>
                        <td>
                            ${product.price}
                            <input type="hidden" name="price[]" id="input-price-${product.id}" value="${product.price}" />
                        </td>
                        <td style="width: 100px"><input type="number" name="quantity[]" id="input-quantity-${product.id}" class="form-control" onkeyup="getSubtotal(${product.id})" onchange="getSubtotal(${product.id})" value="1" min="1" step="1" max="${product.stock[0].quantity}" required /></td>
                        <td class="text-center"><input type="checkbox" name="pay[]" class="checkbox-pay" id="checkbox-pay-${product.id}" value="${product.id}" data-id="${product.id}" style="transform: scale(1.5);" onclick="getPayments()" ${cashier ? '' : 'disabled title="No has aperturado caja"'} /></td>
                        <td class="text-right"><span id="label-subtotal-${product.id}" class="label-subtotal">${product.price}</span></td>
                        <td><button class="btn btn-link" onclick="removeTr(${product.id})"><i class="voyager-trash text-danger"></i></a></td>
                    </tr>
                `);

                setNumber();
                getSubtotal(product.id);
            });

            $('.checkbox-payment').click(function(){
                var payment_total = 0;
                $('.checkbox-payment').each(function(index) {
                    if($(this).is(':checked') && !$(this).attr('disabled')){
                        payment_total += parseFloat($(this).data('amount'));
                    };
                });
                $('#label-total-payment-rooms').text(payment_total);
            });

            $('.checkbox-sale_detail_id').click(function(){
                var amount = 0;
                $('.checkbox-sale_detail_id').each(function(index) {
                    if($(this).is(':checked')){
                        amount += parseFloat($(this).data('total'));
                    };
                });
                $('#label-total-payment-products').text(amount);
                if(amount > 0){
                    $('#tr-total-payment-products').fadeIn();
                }else{
                    $('#tr-total-payment-products').fadeOut();
                }
            });
        });

        function setNumber(){
            var length = 0;
            $(".td-number").each(function(index) {
                $(this).text(index +1);
                length++;
            });

            if(length > 0){
                $('#tr-empty').css('display', 'none');
            }else{
                $('#tr-empty').fadeIn('fast');
            }
        }

        function getSubtotal(id){
            let price = $(`#input-price-${id}`).val() ? parseFloat($(`#input-price-${id}`).val()):0;
            let quantity = $(`#input-quantity-${id}`).val() ? parseFloat($(`#input-quantity-${id}`).val()):0;
            $(`#label-subtotal-${id}`).text((price * quantity).toFixed(2));
            getTotal();
            getPayments();
        }

        function getTotal(){
            let total = 0;
            $(".label-subtotal").each(function(index) {
                total += parseFloat($(this).text());
            });
            $('#label-total').text(total.toFixed(2));
        }

        function getPayments(){
            let payment_total = 0;
            $(".checkbox-pay").each(function(index) {
                let id = $(this).data('id');
                if($(`#checkbox-pay-${id}`).is(':checked')){
                    payment_total += parseFloat($(`#label-subtotal-${id}`).text());
                };
            });
            $('#label-payment').text(payment_total.toFixed(2))

            let total = parseFloat($(`#label-total`).text());
            $('#label-debt').text((total - payment_total).toFixed(2))
        }

        function removeTr(id){
            $(`#tr-item-${id}`).remove();
            setNumber();
            getTotal();
        }
    </script>
@stop
