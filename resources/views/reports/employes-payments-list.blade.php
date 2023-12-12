<div class="col-md-12 text-right">
    @if (count($employes))
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
                            <th>Empleado</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $cont = 1;
                        @endphp
                        @forelse ($employes as $item)
                            <tr>
                                <td>{{ $cont }}</td>
                                <td>{{ $item->full_name }}</td>
                                <td>
                                    <table width="100%">
                                        @php
                                            $total = 0;
                                        @endphp
                                        @foreach ($item->payments as $payment)
                                            <tr>
                                                <td>{{ date('d/', strtotime($payment->date)).$months[intval(date('m', strtotime($payment->date)))].date('/Y', strtotime($payment->date)) }}</td>
                                                <td>{{ $payment->description }}</td>
                                                <td class="text-right">{{ $payment->amount == intval($payment->amount) ? intval($payment->amount) : $payment->amount }}</td>
                                            </tr>
                                            @php
                                                $total += $payment->amount;
                                            @endphp
                                        @endforeach
                                        <tr>
                                            <td colspan="2" class="text-right"><b>TOTAL</b></td>
                                            <td class="text-right"><b>{{ $total}}</b></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @php
                                $cont++;
                            @endphp
                        @empty
                            
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>