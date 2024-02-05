<div class="col-md-12 text-right">
    @if (true)
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
            @if (date('Y-m-d') == $date)
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th colspan="4"><h4 class="text-center">Detalle de habitaciones</h4></th>
                        </tr>
                        <tr>
                            <th style="width: 25%">Disponibles</th>
                            <th style="width: 25%">Ocupadas</th>
                            <th style="width: 25%">Reservadas</th>
                            <th style="width: 25%">Sucias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                @php
                                    $room_available = App\Models\Room::where('status', 'disponible')->get();
                                @endphp
                                <b>{{ $room_available->count() }}</b>
                                <hr style="margin: 5px 0px">
                                <small>
                                    @foreach ($room_available as $item)
                                        {{ $item->code }} &nbsp;
                                    @endforeach
                                    @if ($room_available->count() == 0)
                                        &nbsp;
                                    @endif
                                </small>
                            </td>
                            <td>
                                @php
                                    $room_occupied = App\Models\Room::where('status', 'ocupada')->get();
                                @endphp
                                <b>{{ $room_occupied->count() }}</b>
                                <hr style="margin: 5px 0px">
                                <small>
                                    @foreach ($room_occupied as $item)
                                        {{ $item->code }} &nbsp;
                                    @endforeach
                                    @if ($room_occupied->count() == 0)
                                        &nbsp;
                                    @endif
                                </small>
                            </td>
                            <td>
                                @php
                                    $reservation = App\Models\Reservation::with('details.room')->where('status', 'reservacion')->whereDate('start', date('Y-m-d'))->get();
                                    $reservations = 0;
                                    foreach ($reservation as $item) {
                                        $reservations += $item->details->count();
                                    }
                                @endphp
                                <b>{{ $reservations }}</b>
                                <hr style="margin: 5px 0px">
                                <small>
                                    @foreach ($reservation as $item)
                                        @foreach ($item->details as $detail)
                                            {{ $detail->room->code }} &nbsp;
                                        @endforeach
                                    @endforeach
                                    @if ($reservations == 0)
                                        &nbsp;
                                    @endif
                                </small>
                            </td>
                            <td>
                                @php
                                $room_dirty = App\Models\Room::where('status', 'limpieza')->get(); 
                                @endphp
                                <b>{{ $room_dirty->count() }}</b>
                                <hr style="margin: 5px 0px">
                                <small>
                                    @foreach ($room_dirty as $item)
                                        {{ $item->code }} &nbsp;
                                    @endforeach
                                    @if ($room_dirty->count() == 0)
                                        &nbsp;
                                    @endif
                                </small>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
            @endif

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th colspan="4"><h4 class="text-center">Movimientos de caja</h4></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cashiers as $cashier)
                        <thead>
                            <tr>
                                <th colspan="4"><h5>{{ $cashier->user->name }}</h5></th>
                            </tr>
                            <tr>
                                <th>N&deg;</th>
                                <th>Tipo</th>
                                <th>Detalle</th>
                                <th width="100px">Monto (Bs.)</th>
                            </tr>
                        </thead>
                        @php
                            $cont = 1;
                            $total_revenue = 0;
                            $total_expenses = 0;
                            $total_qr = 0;
                            $total_sales = 0;
                            $total_hosting = 0;
                        @endphp
                        @forelse ($cashier->details as $item)
                            <tr>
                                <td>{{ $cont }}</td>
                                <td>{{ $item->type }}</td>
                                <td>
                                    @if ($item->sale_detail)
                                        Venta de <b>{{ $item->sale_detail->quantity == floatVal($item->sale_detail->quantity) ? intval($item->sale_detail->quantity) : $item->sale_detail->quantity }} {{ $item->sale_detail->product->name }}</b>
                                    @elseif ($item->service)
                                        Uso de <b>{{ $item->service->name }}</b>
                                    @elseif ($item->reservation_detail_day)
                                        Pago de hospedaje habitación <b>{{ $item->reservation_detail_day->reservation_detail->room->code }}</b>
                                    @elseif ($item->penalty)
                                        Pago de multa por <b>{{ $item->penalty->type->name }}</b>
                                        @if ($item->penalty->observations)
                                            <br> <small>{{ $item->penalty->observations }}</small>
                                        @endif
                                    @endif
                                    {!! $item->observations ? '<br>'.$item->observations : '' !!}
                                </td>
                                <td class="text-right">
                                    @if (!$item->cash)
                                        <i class="fa fa-qrcode text-primary" title="Pago con QR"></i>
                                    @endif 
                                    {{ floatval($item->amount) == intval($item->amount) ? intval($item->amount) : $item->amount }}
                                </td>
                            </tr>
                            @php
                                $cont++;
                                if ($item->type == 'ingreso') {
                                    $total_revenue += $item->amount;
                                } else {
                                    $total_expenses += $item->amount;
                                }
                                if(!$item->cash){
                                    $total_qr += $item->amount;
                                }
                                if ($item->sale_detail_id ) {
                                    $total_sales += $item->amount;
                                } else {
                                    $total_hosting += $item->amount;
                                }
                            @endphp
                        @empty
                            <tr>
                                <td colspan="5">No hay datos registardos</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td colspan="3" class="text-right"><b>INGRESO TOTAL</b></td>
                            <td class="text-right"><h4>{{ $total_revenue }}</h4></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><b>EGRESO TOTAL</b></td>
                            <td class="text-right"><h4>{{ $total_expenses }}</h4></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><b>PAGO TOTAL CON QR</b></td>
                            <td class="text-right"><h4>{{ $total_qr }}</h4></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right"><b>TOTAL EN CAJA</b></td>
                            <td class="text-right"><h4>{{ $total_revenue - $total_expenses - $total_qr }}</h4></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>
        </div>
    </div>
</div>