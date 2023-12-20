<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Procedencia</th>
                    <th>Nombre completo</th>
                    <th>Celular</th>
                    <th>Fecha de<br>nacimiento</th>
                    <th>Género</th>
                    <th>Fotografía</th>
                    <th>Registrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                @endphp
                @forelse ($data as $item)
                    @php
                        $image = asset('images/default.jpg');
                        if($item->photo){
                            $image = asset('storage/'.str_replace('.', '-cropped.', $item->photo));
                        }
                        $now = \Carbon\Carbon::now();
                        $birthday = new \Carbon\Carbon($item->birthday);
                        $age = $birthday->diffInYears($now);
                    @endphp
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            {{ $item->city->name }} <br>
                            <small>
                                @if ($item->city->state)
                                    {{ $item->city->state->name }}
                                    @if ($item->city->state->country)
                                        - {{ $item->city->state->country->name }}            
                                    @endif
                                @endif
                            </small>
                            @if ($item->reservations->count())
                                <br>
                                <label class="label label-success">Hospedado</label>
                            @endif
                        </td>
                        <td>
                            {{ $item->full_name }} <br>
                            <small>{{ $item->dni }}</small>
                        </td>
                        <td>{{ $item->phone }}</td>
                        <td>
                            @if ($item->birthday)
                                {{ date('d', strtotime($item->birthday)).'/'.$months[intval(date('m', strtotime($item->birthday)))].'/'.date('Y', strtotime($item->birthday)) }} <br>
                                <small>{{ $age }} años</small>
                            @endif
                        </td>
                        <td>{{ $item->gender }}</td>
                        <td><img src="{{ $image }}" alt="{{ $item->full_name }}" style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px"></td>
                        <td>
                            {{ $item->user ? $item->user->name : '' }} <br>
                            {{ date('d/', strtotime($item->created_at)).$months[intval(date('m', strtotime($item->created_at)))].date('/Y H:i', strtotime($item->created_at)) }} <br>
                            <small>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                        </td>
                        <td class="no-sort no-click bread-actions text-right">
                            @if (Auth::user()->hasPermission('read_people'))
                                <a href="{{ route('voyager.people.show', $item->id) }}" title="Ver" class="btn btn-sm btn-warning">
                                    <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('edit_people'))
                                <a href="{{ route('voyager.people.edit', $item->id) }}" title="Editar" class="btn btn-sm btn-info">
                                    <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                                </a>
                            @endif
                            @if (Auth::user()->hasPermission('delete_people'))
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