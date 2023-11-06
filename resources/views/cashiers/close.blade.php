@extends('voyager::master')

@section('page_title', 'Cerrar caja')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-logbook"></i> Cerrar Caja
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="row">
                        @php
                            $cashier_in = $cashier->details->where('cash', 1)->where('type', 'ingreso')->where('deleted_at', NULL)->sum('amount');
                            $cashier_out = $cashier->details->where('cash', 1)->where('type', 'egreso')->where('deleted_at', NULL)->sum('amount');
                            $movements = $cashier_in - $cashier_out;
                        @endphp
                        <div class="col-md-6" style="height: 550px; overflow-y: auto">
                            <form name="form_close" class="form-submit" action="{{ route('cashiers.close.store', ['id' => $cashier->id]) }}" method="post">
                                @csrf
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Corte</th>
                                            <th>Cantidad</th>
                                            <th>Sub Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lista_cortes"></tbody>
                                </table>

                                {{-- confirm modal --}}
                                <div class="modal modal-danger fade" tabindex="-1" id="close_modal" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title"><i class="voyager-lock"></i> Confirme que desea cerrar la caja?</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>Esta acción cerrará la caja y no podrá realizar modificaciones posteriores</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-danger btn-submit">Sí, cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="amount_total" value="{{ $movements }}">
                                <input type="hidden" name="amount_real" value="0">
                                <input type="hidden" name="amount_surplus" value="0">
                                <input type="hidden" name="amount_missing" value="0">
                            </form>
                        </div>
                        <div class="col-md-6" style="padding-top: 10px">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Ingresos</p>
                                </div>
                                <div class="col-md-6">
                                    <h3 class="text-right" style="padding-right: 20px">{{ number_format($cashier_in, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Egresos</p>
                                </div>
                                <div class="col-md-6">
                                <h3 class="text-right" style="padding-right: 20px">{{ number_format($cashier_out, 2, ',', '.') }}</h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Total</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px">{{ number_format($movements, 2, ',', '.') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto en caja</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px" id="label-total">0,00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto sobrante</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px" id="label-plus_amount">0,00</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin-top: 20px">Monto faltante</p>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel-heading" style="border-bottom:0;">
                                        <h3 class="text-right" style="padding-right: 20px" id="label-missing_amount">0,00</h3>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-danger btn-block btn-confirm" data-toggle="modal" data-target="#close_modal">Cerrar caja <i class="voyager-lock"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        #lista_cortes td {
            vertical-align: text-top;
        }
        #lista_cortes td h4 {
            margin: 0px
        }
        .label-money {
            font-size: 18px
        }
    </style>
@stop

@section('javascript')
    <script>
        const APP_URL = '{{ url('') }}';
    </script>
    <script src="{{ asset('js/cash_value.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.input-corte').keyup(function(){
                getMissingAmount()
            });
            $('.input-corte').change(function(){
                getMissingAmount()
            });
        });

        function getMissingAmount(){
            let total = parseFloat("{{ $movements }}");
            let total_cashier = parseFloat($('#label-total').text());
            let missing_amount = total - total_cashier;
            let plus_amount = total_cashier - total;
            $('#label-missing_amount').text(missing_amount > 0 ? missing_amount.toFixed(2) : 0);
            $('.form-submit input[name="amount_missing"]').val(missing_amount > 0 ? missing_amount : 0);
            $('#label-plus_amount').text(plus_amount > 0 ? plus_amount.toFixed(2) : 0);
            $('.form-submit input[name="amount_surplus"]').val(plus_amount > 0 ? plus_amount : 0);
            $('.form-submit input[name="amount_real"]').val(total_cashier);
            if(missing_amount > 0){
                $('#label-missing_amount').addClass('text-danger');
                // $('.btn-confirm').attr('disabled', 'disabled');
            }else{
                $('#label-missing_amount').removeClass('text-danger');
                // $('.btn-confirm').removeAttr('disabled');
            }
            plus_amount > 0 ? $('#label-plus_amount').addClass('text-primary') : $('#label-plus_amount').removeClass('text-primary');
        }
    </script>
@stop
