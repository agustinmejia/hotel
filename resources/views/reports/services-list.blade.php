<div class="col-md-12 text-right">
    @if (count($rooms))
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
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>N&deg;</th>
                            <th>Piso</th>
                            <th>Habitaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cont = 1;
                            $total_people_food = 0;
                        @endphp
                        @foreach ($rooms as $item)
                            @if ($info_type == 'accessory' && count($item->reservation_detail))
                                @php
                                    $reservation_detail = $item->reservation_detail[0];
                                @endphp
                                @if ($reservation_detail->accessories->count() > 0)
                                    <tr>
                                        <td>{{ $cont }}</td>
                                        <td>{{ $item->floor_number }}</td>
                                        <td><b>{{ $item->code }}</b></td>
                                    </tr>
                                    @php
                                        $cont++;
                                    @endphp
                                @endif
                            @endif
                            @if ($info_type == 'food_type' && count($item->reservation_detail))
                                @php
                                    $count_food = 0;
                                    $count_people = 1;
                                    $reservation_detail = $item->reservation_detail[0];
                                    $count_food += $reservation_detail->food->count();
                                    $count_people += $reservation_detail->reservation->aditional_people->count();
                                @endphp
                                @if ($count_food > 0)
                                    <tr>
                                        <td>{{ $cont }}</td>
                                        <td>{{ $item->floor_number }}</td>
                                        <td><b>{{ $item->code }}</b> <br> {{ $count_people }} {{ $count_people > 1 ? 'personas' : 'persona' }}</td>
                                    </tr>
                                    @php
                                        $cont++;
                                        $total_people_food += $count_people;
                                    @endphp
                                @endif
                            @endif
                        @endforeach
                    </tbody>
                    @if ($info_type == 'food_type')
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right">TOTAL</td>
                            <td>{{ $total_people_food }} {{ $total_people_food > 1 ? 'personas' : 'persona' }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
</div>