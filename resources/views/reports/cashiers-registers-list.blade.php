<div class="col-md-12 text-right">
    @if (count($cashier_details))
        {{-- <button type="button" onclick="report_export('print')" class="btn btn-danger"><i class="glyphicon glyphicon-print"></i> Imprimir</button> --}}
        {{-- <button type="button" onclick="report_export('excel')" class="btn btn-success"><i class="glyphicon glyphicon-download"></i> Excel</button> --}}
    @endif
</div>
@php
    $meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
@endphp
<div class="col-md-12">
    <div class="panel panel-bordered">
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>N&deg;</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Descripción</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cont = 1;
                            $total = 0;
                        @endphp
                        @forelse ($cashier_details as $item)
                            <tr>
                                <td>{{ $cont }}</td>
                                <td>
                                    {{ date('d/', strtotime($item->created_at)).$meses[intval(date('m', strtotime($item->created_at)))].date('/Y H:i', strtotime($item->created_at)) }} <br>
                                    <small>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                                </td>
                                <td>{{ $item->cashier->user ? $item->cashier->user->name : 'No definido' }}</td>
                                <td>{{ $item->observations ?? 'No definida' }}</td>
                                <td>{{ $item->cash ? 'Efectivo' : 'Transferencia'  }}</td>
                                <td class="text-right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                            </tr>
                            @php
                                $cont++;
                                $total += $item->amount;
                            @endphp
                        @empty
                            <tr>
                                <td class="6">No hay datos</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td class="text-right" colspan="5"><b>TOTAL</b></td>
                            <td class="text-right"><b style="font-size: 18px">{{ number_format($total, 2, ',', '.') }}</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>