@extends('partials.template-print', ['page_title' => 'Reporte General'])

@php
    $months = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $days = ['', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
@endphp

@section('header')
    <h2 style="margin: 0px">REPORTE DE HOSPEDAJES</h2>
    <small>
        {{ $days[date('N', strtotime($date))] }}, {{ date('d', strtotime($date)) }} de {{ $months[intval(date('m', strtotime($date)))] }} del {{ date('Y', strtotime($date)) }} <br>
        {{ Auth::user()->name }}
    </small>
@endsection

@section('content')
    <div class="content">
        <table width="100%" border="1" cellpadding="5">
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
@endsection

@section('css')
    <style>
        thead th {
            background-color: #ECF0F1
        }
        .content table {
            border-collapse: collapse;
            font-size: 11px
        }
        .text-right {
            text-align: right
        }
        th h4, td h4 {
            margin: 5px 0px
        }
        .td-total{
            background-color: rgba(27, 38, 49, 0.2)
        }
    </style>
@endsection