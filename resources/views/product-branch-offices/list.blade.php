<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sucursal</th>
                    <th>Producto</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->branch_office->name }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ intval($item->quantity) == floatval($item->quantity) ? intval($item->quantity) : $item->quantity }}</td>
                    <td class="no-sort no-click bread-actions text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown">
                                Más <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" style="left: -90px !important">
                                @if (Auth::user()->hasPermission('add_product_branch_offices'))
                                <li><a href="#" title="Agregar" class="btn-add-stock" data-toggle="modal" data-target="#add-stock-modal" data-item='@json($item)'>Inventariar</a></li>
                                @endif
                                <li><a href="#" data-toggle="modal" data-target="#sales-history-modal" onclick="salesHistory({{ $item->product_id }})">Historial de ventas</a></li>
                            </ul>
                        </div>
                        @if (Auth::user()->hasPermission('delete_product_branch_offices'))
                            <button title="Borrar" class="btn btn-sm btn-danger" onclick="deleteItem({{ $item->id }})" data-toggle="modal" data-target="#delete_custom_modal">
                                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Borrar</span>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr class="odd">
                        <td valign="top" colspan="5" class="dataTables_empty">No hay datos disponibles en la tabla</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if(count($data)>0)
            <p class="text-muted">Mostrando del {{$data->firstItem()}} al {{$data->lastItem()}} de {{$data->total()}} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $data->links() }}
        </nav>
    </div>
</div>

<script>
    var page = "{{ request('page') }}";
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            if(link){
                page = link.split('=')[1];
                list(page);
            }
        });

        $('.btn-add-stock').click(function(){
            let item = $(this).data('item');
            $('#add-stock-form input[name="product_branch_office_id"]').val(item.id);
        });
    });
</script>