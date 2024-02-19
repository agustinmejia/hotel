@extends('voyager::master')

@section('page_title', 'Viendo Cajas')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <h1 class="page-title">
                    <i class="voyager-logbook"></i> Cajas
                </h1>
            </div>
            <div class="col-md-8 text-right" style="padding-top: 20px">
                @if (Auth::user()->hasPermission('delete_cashiers'))
                    <a href="{{ route('cashiers.create') }}" class="btn btn-success btn-add-new">
                        <i class="voyager-plus"></i> <span>Crear</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Mostrar <select id="select-paginate" class="form-control input-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" id="input-search" class="form-control">
                            </div>
                            <div class="col-md-12 text-right" style="margin-bottom: 30px !important">
                                <label class="radio-inline"><input type="radio" class="radio-status" name="status" value="">Todas</label>
                                <label class="radio-inline"><input type="radio" class="radio-status" name="status" value="abierta" checked>Abiertas</label>
                                <label class="radio-inline"><input type="radio" class="radio-status" name="status" value="cerrada">Cerradas</label>
                            </div>
                        </div>
                        <div class="row" id="div-results" style="min-height: 120px; padding-bottom: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add register modal --}}
    <form action="{{ route('cashiers.add.register') }}" class="form-submit" id="form-add_register" method="POST">
        @csrf
        <input type="hidden" name="id">
        <div class="modal fade" tabindex="-1" id="add_register-modal" role="dialog">
            <div class="modal-dialog modal-primary">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><i class="voyager-logbook"></i> Agregar registro</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="type">Tipo de registro</label>
                            <select name="type" class="form-control">
                                <option value="egreso">Egreso</option>
                                <option value="ingreso">Ingreso</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">Monto</label>
                            <input type="number" name="amount" class="form-control" step="1" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="observations">Descripción</label>
                            <textarea name="observations" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                        <input type="submit" class="btn btn-primary btn-submit" value="Guardar">
                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')

@stop

@section('javascript')
    <script>
        var countPage = 10;

        $(document).ready(function() {
            list();
            
            $('#input-search').on('keyup', function(e){
                if(e.keyCode == 13) {
                    list();
                }
            });

            $('.radio-status').click(function(e){
                list();
            });

            $('#select-paginate').change(function(){
                countPage = $(this).val();
                list();
            });
        });

        function list(page = 1){
            $('#div-results').loading({message: 'Cargando...'});
            let url = "{{ route('cashiers.list')}}";
            let search = $('#input-search').val() ? $('#input-search').val() : '';
            let status = $('.radio-status:checked').val();
            $.ajax({
                url: `${url}?paginate=${countPage}&page=${page}&search=${search}&status=${status}`,
                type: 'get',
                success: function(result){
                $("#div-results").html(result);
                $('#div-results').loading('toggle');
            }});
        }

        function deleteItem(id){
            let url = '{{ url("admin/cashiers") }}/'+id;
            $('#delete_custom_form').attr('action', url);
        }

    </script>
@stop
