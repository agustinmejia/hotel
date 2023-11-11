@extends('voyager::master')

@section('page_title', 'Importar')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-upload"></i>
        Importar
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <form class="form-submit" action="{{ route('import.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="file">Datos</label>
                                        <select name="type" class="form-control select2" id="select-type" required>
                                            <option value="">--Seleccione el tipo de dato--</option>
                                            <option value="1" data-img="{{ asset('images/import/people.png') }}" data-file="{{ asset('excel/examples/lista de personas.xlsx') }}">Datos personales</option>
                                            <option value="2" disabled>Productos</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="file">Archivo</label>
                                        <input type="file" name="file" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required>
                                        <small>NOTA: los archivo no deben contener las cabeceras, solo los datos.</small>
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary btn-submit">Importar</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-6 text-center">
                                <img src="" id="img-example" alt="" width="100%">
                                <br><br>
                                <a href="#" id="link-example" style="display: none">Descargar ejemplo <i class="voyager-upload"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    
@stop

@section('javascript')
    <script>
        $(document).ready(function(){
            $('#select-type').change(function(){
                let img = $('#select-type option:selected').data('img');
                let file = $('#select-type option:selected').data('file');
                $('#img-example').attr('src', img);
                $('#link-example').attr('href', file)
                $('#link-example').fadeIn('fast');
            });
        });
    </script>
@stop
