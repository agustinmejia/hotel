@extends('voyager::master')

@section('page_title', 'Planilla de Pago')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-file-text"></i> Planilla de Pago
                </h1>
            </div>
            <div class="col-md-4" style="padding-top: 20px">
                
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div id="div-results" style="min-height: 100px">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>N&deg;</th>
                                            <th>Nombre completo</th>
                                            <th>CI</th>
                                            <th>Cargo</th>
                                            <th>Sueldo</th>
                                            <th>Adelantos</th>
                                            <th>Descuentos</th>
                                            <th>Saldo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $cont = 1;
                                            $employes = App\Models\Employe::with(['job', 'payments'])->where('status', 1)->get();
                                            $total = 0;
                                        @endphp
                                        @forelse ($employes as $item)
                                            @php
                                                $advancement_amount = $item->payments->where('status', 'pendiente')->sum('amount');
                                            @endphp
                                            <tr>
                                                <td>{{ $cont }}</td>
                                                <td>{{ $item->full_name }}</td>
                                                <td>{{ $item->dni }}</td>
                                                <td>{{ $item->job->name }}</td>
                                                <td class="text-right">{{ number_format($item->job->salary, 2, ',', '.') }}</td>
                                                <td class="text-right">{{ number_format($advancement_amount, 2, ',', '.') }}</td>
                                                <td class="text-right">0</td>
                                                <td class="text-right">{{ number_format($item->job->salary - $advancement_amount, 2, ',', '.') }}</td>
                                            </tr>
                                            @php
                                                $cont = 1;
                                                $total += $item->job->salary - $advancement_amount;
                                            @endphp
                                        @empty
                                            <tr>
                                                <td colspan="8">No hay datos registrado</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="7" class="text-right"><b>TOTAL</b></td>
                                            <td class="text-right"><b>{{ number_format($total, 2, ',', '.') }}</b></td>
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
@stop

@section('css')
    <style>
        tfoot {
            background-color: #f7f7f7
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {

        });

        function report_export(type){
            $('#form-report').attr('target', '_blank');
            $('#form-report input[name="type"]').val(type);
            window.form_report.submit();
            $('#form-report').removeAttr('target');
            $('#form-report input[name="type"]').val('');
        }
    </script>
@stop
