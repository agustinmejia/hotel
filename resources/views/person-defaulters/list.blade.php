<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>N&deg;</th>
                    <th>ID</th>
                    <th>Persona</th>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Detalle</th>
                    <th>Estado</th>
                    <th class="text-right">Monto</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                    $cont = 1;
                @endphp
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $cont }}</td>
                        <td>{{ str_pad($item->id, 4, "0", STR_PAD_LEFT) }}</td>
                        <td>{{ $item->person ? $item->person->full_name : 'No definido' }}</td>
                        <td>{{ date('d', strtotime($item->created_at)).'/'.$months[intval(date('m', strtotime($item->created_at)))].'/'.date('Y H:i', strtotime($item->created_at)) }}</td>
                        <td>{{ $item->type == 1 ? 'Abandonó sin pagar' : 'Paga luego' }}</td>
                        <td>{{ $item->observations }}</td>
                        <td>
                            @php
                                switch ($item->status) {
                                    case 'pendiente':
                                        $label = 'danger';
                                        $status = $item->status;
                                        break;
                                    case 'pagada':
                                        $label = 'success';
                                        $status = $item->status;
                                        break;
                                    default:
                                        $label = 'default';
                                        $status = $item->status ?? 'no definido';
                                        break;
                                }
                            @endphp
                            <label class="label label-{{ $label }}">{{ ucfirst($status) }}</label>
                        </td>
                        <td class="text-right">{{ $item->amount }}</td>
                        <td class="no-sort no-click bread-actions text-right">
                            <a href="{{ route('person-defaulters.show', $item->id) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                            </a>
                        </td>
                    </tr>
                    @php
                        $cont++;
                    @endphp
                @empty
                    <tr class="odd">
                        <td valign="top" colspan="9" class="dataTables_empty">No hay datos disponibles en la tabla</td>
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