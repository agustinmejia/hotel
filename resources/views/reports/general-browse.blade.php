@extends('voyager::master')

@section('page_title', 'Reporte General')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-file-text"></i> Reporte General
                </h1>
            </div>
            <div class="col-md-4" style="padding-top: 20px">
                {{-- <a href="{{ route('report-general.index') }}?type=print" class="btn btn-danger" target="_blank">
                    <i class="fa fa-print"></i> <span>Imprimir</span>
                </a> --}}
                <form id="form-report" name="form_report" action="{{ route('report-general.list') }}" method="post">
                    @csrf
                    <input type="hidden" name="type">
                    <div class="form-group">
                        <small>Fecha</small>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
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
        .td-total{
            background-color: rgba(27, 38, 49, 0.2)
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
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