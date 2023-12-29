<div class="col-md-12 text-right">
    @if (count($activities))
        {{-- <button type="button" onclick="report_export('print')" class="btn btn-danger"><i class="glyphicon glyphicon-print"></i> Imprimir</button> --}}
        {{-- <button type="button" onclick="report_export('excel')" class="btn btn-success"><i class="glyphicon glyphicon-download"></i> Excel</button> --}}
    @endif
</div>
@php
    $months = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
@endphp
<div class="col-md-12">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="table-responsive">
                @if ($group_by)
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>N&deg;</th>
                                <th>Nombre completo</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cont = 1;
                            @endphp
                            @forelse ($activities->groupBy('employe_id') as $key => $item)
                                <tr>
                                    <td>{{ $cont }}</td>
                                    <td>{{ $item[0]->employe->full_name }}</td>
                                    <td class="text-right">{{ count($item) }}</td>
                                </tr>
                                @php
                                    $cont++;
                                @endphp
                            @empty
                                <tr>
                                    <td class="3">No hay datos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>N&deg;</th>
                                <th>Nombre completo</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cont = 1;
                            @endphp
                            @forelse ($activities as $item)
                                <tr>
                                    <td>{{ $cont }}</td>
                                    <td>{{ $item->employe->full_name }}</td>
                                    <td class="text-right">{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                                </tr>
                                @php
                                    $cont++;
                                @endphp
                            @empty
                                <tr>
                                    <td class="3">No hay datos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
</div>