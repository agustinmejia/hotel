@extends('voyager::master')

@section('page_title', 'Viendo Deudores')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title">
                    <i class="voyager-dollar"></i> Deudores
                </h1>
            </div>
            {{-- <div class="col-md-8 text-right" style="padding-top: 20px">
                @if (Auth::user()->hasPermission('add_reservations'))
                    <a href="{{ route('reservations.create') }}" class="btn btn-success btn-add-new">
                        <i class="voyager-plus"></i> <span>Crear</span>
                    </a>
                @endif
            </div> --}}
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
                            <label class="radio-inline"><input type="radio" class="radio-status" name="status" value="pendiente" checked>Pendientes</label>
                            <label class="radio-inline"><input type="radio" class="radio-status" name="status" value="pagada">Pagada</label>
                        </div>
                    </div>
                    <div class="row" id="div-results" style="min-height: 120px; padding-bottom: 120px"></div>
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
            let url = "{{ route('person-defaulters.list')}}";
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
            let url = '{{ url("admin/reservations") }}/'+id;
            $('#delete_custom_form').attr('action', url);
        }
    </script>
@stop
