<div class="col-md-12 text-right">
    @if ($reservations->count())
        <button type="button" onclick="report_export('print')" class="btn btn-danger"><i class="glyphicon glyphicon-print"></i> Imprimir</button>
        {{-- <button type="button" onclick="report_export('excel')" class="btn btn-success"><i class="glyphicon glyphicon-download"></i> Excel</button> --}}
    @endif
</div>
@php
    $days = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    $months = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
@endphp
<div class="col-md-12">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>N&deg;</th>
                        <th>Nombre completo</th>
                        <th>Cant. personas</th>
                        <th>Habitación</th>
                        <th>Precio</th>
                        <th>Hora</th>
                        <th>Turno</th>
                        <th>Accesorios</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $cont = 1;
                    @endphp
                    @forelse ($reservations as $item)
                        <tr>
                            <td>{{ $cont }}</td>
                            <td>{{ $item->person->full_name }}</td>
                            <td>{{ $item->aditional_people->count() +1 }}</td>
                            <td>
                                @foreach ($item->details as $detail)
                                {{ $detail->room->code }} <br>
                                @endforeach
                            </td>
                            <td class="text-right">
                                @foreach ($item->details as $detail)
                                    @foreach ($detail->days as $day)
                                        {{ intval($day->amount) }}
                                    @endforeach
                                    <br>
                                @endforeach
                            </td>
                            <td>{{ date('H:i', strtotime($item->created_at)) }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>
                                @foreach ($item->details as $detail)
                                    @foreach ($detail->accessories as $accessory)
                                        <label class="label label-default">{{ $accessory->accessory->name }}</label>
                                    @endforeach
                                    <br>
                                @endforeach
                            </td>
                            <td>
                                @foreach ($item->details as $detail)
                                    @foreach ($detail->days as $day)
                                        <label class="label label-{{ $day->status == 'pagado' ? 'success' : 'warning' }}">{{ $day->status }}</label>
                                    @endforeach
                                    <br>
                                @endforeach
                            </td>
                            <td>{{ $item->observation }}</td>
                        </tr>
                        @php
                            $cont++;
                        @endphp
                    @empty
                        <tr>
                            <td colspan="10">No hay datos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>