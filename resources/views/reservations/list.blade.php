<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Huesped</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>N&deg; de hab.</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                @endphp
                @forelse ($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        {{ $item->person->full_name }} <br>
                        <small>{{ $item->person->dni }}</small>
                    </td>
                    <td>{{ date('d', strtotime($item->start)) }}/{{ $months[intval(date('m', strtotime($item->start)))] }}/{{ date('Y', strtotime($item->start)) }}</td>
                    <td>
                        @if ($item->finish)
                        {{ date('d', strtotime($item->finish)) }}/{{ $months[intval(date('m', strtotime($item->finish)))] }}/{{ date('Y', strtotime($item->finish)) }}
                        @else
                            No definido
                        @endif
                    </td>
                    <td>{{ $item->details->count() }}</td>
                    <td>
                        @php
                            switch ($item->status) {
                                case 'en curso':
                                    $label = 'primary';
                                    $status = $item->status;
                                    break;
                                case 'finalizado':
                                    $label = 'danger';
                                    $status = $item->status;
                                    break;
                                case 'reservacion':
                                    $label = 'warning';
                                    $status = 'reservación';
                                    break;
                                
                                default:
                                    $label = 'default';
                                    $status = $item->status ?? 'no definido';
                                    break;
                            }
                        @endphp
                        <label class="label label-{{ $label }}">{{ ucfirst($status) }}</label>
                    </td>
                    <td class="no-sort no-click bread-actions text-right">
                        @if (Auth::user()->hasPermission('read_reservations'))
                            <a href="{{ route('reservations.show', $item->id) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                            </a>
                        @endif
                        @if (Auth::user()->hasPermission('delete_reservations') && $item->status == 'en curso')
                            <button title="Borrar" class="btn btn-sm btn-danger" disabled onclick="deleteItem({{ $item->id }})" data-toggle="modal" data-target="#delete_custom_modal">
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
    });
</script>