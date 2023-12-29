@extends('voyager::master')

@section('page_title', 'Reporte de Limpiezas')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-brush"></i> Reporte de Limpiezas
                </h1>
            </div>
            <div class="col-md-4" style="padding-top: 20px">
                <form id="form-report" name="form_report" action="{{ route('report-employes-cleaning.list') }}" method="post">
                    @csrf
                    <input type="hidden" name="type">
                    <div class="form-group">
                        <small>Empleado</small>
                        <select name="employe_id" class="form-control select2" id="select-employe_id">
                            <option value="">Todos</option>
                            @foreach (App\Models\Employe::where('status', 1)->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->full_name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="info_type">
                    </div>
                    <div class="form-group">
                        <small>Mes</small>
                        <input type="month" name="month" class="form-control" required>
                    </div>
                    <div class="form-group text-right">
                        <div class="checkbox">
                            <label><input type="checkbox" name="group_by" value="1">Agrupar por empleado</label>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Generar <i class="voyager-settings"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div id="div-results" style="min-height: 100px"></div>
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

            $('#select-service_id').change(function(){
                let type = $('#select-service_id option:selected').data('type');
                $('#form-report input[name="info_type"]').val(type);
            });

            $('#form-report').on('submit', function(e){
                e.preventDefault();
                $('#div-results').empty();
                // $('#div-results').loading({message: 'Cargando...'});
                $.post($('#form-report').attr('action'), $('#form-report').serialize(), function(res){
                    $('#div-results').html(res);
                })
                .fail(function() {
                    toastr.error('Ocurrió un error!', 'Oops!');
                })
                .always(function() {
                    // $('#div-results').loading('toggle');
                    $('html, body').animate({
                        scrollTop: $("#div-results").offset().top - 70
                    }, 500);
                });
            });
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
