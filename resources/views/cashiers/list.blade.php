<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sucursal</th>
                    <th>Monto</th>
                    <th>Registrada</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                @endphp
                @forelse ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->branch_office->name }}</td>
                    <td>{{ $item->details->where('cash', 1)->where('type', 'ingreso')->sum('amount') - $item->details->where('cash', 1)->where('type', 'egreso')->sum('amount') }}</td>
                    <td>
                        {{ $item->user ? $item->user->name : '' }} <br>
                        {{ date('d/', strtotime($item->created_at)).$meses[intval(date('m', strtotime($item->created_at)))].date('/Y H:i', strtotime($item->created_at)) }} <br>
                        <small>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                    </td>
                    <td><label class="label label-{{ $item->status == 'abierta' ? 'success' : 'danger' }}">{{ ucfirst($item->status) }}</label></td>
                    <td class="no-sort no-click bread-actions text-right">
                        @if ($item->status == 'abierta' && $item->user_id == Auth::user()->id)
                            <div class="btn-group" style="margin-right: 3px">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="hidden-xs hidden-sm">Más</span><span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" style="left: -100px">
                                    <li><a href="#" class="btn-add-regiter" data-id="{{ $item->id }}" data-toggle="modal" data-target="#add_register-modal">Agregar movimiento</a></li>
                                </ul>
                            </div>
                        @endif
                        @if (Auth::user()->hasPermission('read_cashiers'))
                        <a href="{{ route('cashiers.show', $item->id) }}" title="Ver" class="btn btn-sm btn-warning view">
                            <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                        </a>
                        @endif
                        @if ($item->details->count() == 0 && Auth::user()->hasPermission('delete_cashiers'))
                            <button title="Borrar" class="btn btn-sm btn-danger" onclick="deleteItem({{ $item->id }})" data-toggle="modal" data-target="#delete_custom_modal">
                                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Borrar</span>
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                    <tr class="odd">
                        <td valign="top" colspan="6" class="dataTables_empty">No hay datos disponibles en la tabla</td>
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

        $('.btn-add-regiter').click(function(){
            $('#form-add_register input[name="id"]').val($(this).data('id'));
        });
    });
</script>