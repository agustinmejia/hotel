<div class="col-md-12">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>N&deg;</th>
                <th>Usuario</th>
                <th>Fecha</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $meses = ['', 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
                $cont = 1;
            @endphp
            @forelse ($sales as $item)
                <tr>
                    <td>{{ $cont }}</td>
                    <td>{{ $item->sale->user ? $item->sale->user->name : 'No definido' }}</td>
                    <td>
                        {{ date('d/', strtotime($item->created_at)).$meses[intval(date('m', strtotime($item->created_at)))].date('/Y H:i', strtotime($item->created_at)) }} <br>
                        <small>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                    </td>
                    <td class="text-right">{{ intval($item->quantity) }}</td>
                    <td class="text-right">{{ $item->price }}</td>
                    <td class="text-right">{{ $item->quantity * $item->price }}</td>
                </tr>
                @php
                    $cont++;
                @endphp
            @empty
                <tr>
                    <td colspan="5" class="text-center"><h5>No hay ventas registradas</h5></td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>