@extends('voyager::master')

@section('page_title', 'Vender')

@section('page_header')
    <h1 class="page-title">
        <i class="fa fa-shopping-cart"></i>
        Vender
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        
        @include('partials.check-cashier', ['cashier' => $cashier])

        <div class="row">
            <form action="{{ route('sales.store') }}" class="form-submit" method="post">
                @csrf
                <input type="hidden" name="cashier_id" value="{{ $cashier ? $cashier->id : null }}">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="select-product">Productos</label>
                                <select class="form-control select2" id="select-product"></select>
                            </div>
                            <div class="form-group">
                                <table class="table table-hover table-products">
                                    <thead>
                                        <tr>
                                            <th>N&deg;</th>
                                            <th>Detalle</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th class="text-right">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="products-details">
                                        <tr id="tr-empty">
                                            <td colspan="6">No hay productos en la cesta</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4" class="text-right"><b>TOTAL</b></td>
                                            <td class="text-right"><h4 id="label-total">0.00</h4></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <div class="form-group col-md-12">
                                <label class="control-label" for="select-person_id">Cliente</label>
                                <select name="person_id" class="form-control" id="select-person_id"></select>
                            </div>
                            <div class="form-group col-md-12">
                                <label class="control-label" for="date">Fecha</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label class="checkbox-inline"><input type="checkbox" name="payment_qr" title="En caso de que el pago no sea en efectivo"> Pago con transferencia/QR</label>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="submit" class="btn btn-primary btn-block btn-submit" @if(!$cashier) disabled @endif>Vender <i class="fa fa-shopping-cart"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Create person modal --}}
    @include('partials.add-person-alt-modal')

    {{-- Create add cashier modal --}}
    @include('partials.add-cashier-modal', ['redirect' => 'admin/sell'])
@stop

@section('css')
    
@stop

@section('javascript')
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
        var productSelected = null;
        var cashier = @json($cashier);
        $(document).ready(function(){
            customSelect('#select-product', '{{ route("products.search") }}', formatResultProducts, data => { productSelected = data; return data.name}, null, null);
            customSelect('#select-person_id', '{{ route("people.search") }}', formatResultPeople, data => data.full_name, null, 'createPerson()');

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
                        <td class="text-right"><span id="label-subtotal-${product.id}" class="label-subtotal">${product.price}</span></td>
                        <td style="width:50px"><button class="btn btn-link" onclick="removeTr(${product.id})"><i class="voyager-trash text-danger"></i></a></td>
                    </tr>
                `);

                setNumber();
                getSubtotal(product.id);
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
        }

        function getTotal(){
            let total = 0;
            $(".label-subtotal").each(function(index) {
                total += parseFloat($(this).text());
            });
            $('#label-total').text(total.toFixed(2));
        }

        function removeTr(id){
            $(`#tr-item-${id}`).remove();
            setNumber();
            getTotal();
        }

        function createPerson(){
            $('#select-person_id').select2('close');
            $('#person-modal').modal('show');
        }
    </script>
@stop
