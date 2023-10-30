@extends('voyager::master')

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <div class="row">
            <div class="col-md-12">
                <h3>Hola, {{ Auth::user()->name }}</h3>
            </div>
        </div>
        
        @php
            $rooms = App\Models\Room::get();
            $cashier_details = App\Models\CashierDetail::whereHas('cashier', function($q){
                $q->where('user_id', Auth::user()->id)->where('status', 'abierta');
            })->get();
            $rooms_available = $rooms->where('status', 'disponible')->count();

            $reservations = App\Models\Reservation::withCount('details')->get();
            $rooms_finish = $reservations->where('status', 'en curso')->where('finish', date('Y-m-d', strtotime(date('Y-m-d').' -1 days')));
            $rooms_reservations = $reservations->where('status', 'reserva')->where('start', date('Y-m-d'));
            $total_debt = 0;
            $products = 0;
        @endphp

        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-bordered" style="border-left: 5px solid #52BE80">
                    <div class="panel-body" style="height: 100px;padding: 15px 10px">
                        <div class="col-md-9">
                            <h5>Habitaciones disponibles</h5>
                            <h2>{{ $rooms_available }}</h2>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="icon fa fa-bed" style="color: #52BE80"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered" style="border-left: 5px solid #E74C3C">
                    <div class="panel-body" style="height: 100px;padding: 15px 20px">
                        <div class="col-md-9">
                            <h5>Reservas</h5>
                            <h2>{{ $rooms_reservations->count() }}</h2>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="icon voyager-book" style="color: #E74C3C"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered" style="border-left: 5px solid #3498DB">
                    <div class="panel-body" style="height: 100px;padding: 15px 20px">
                        <div class="col-md-9">
                            <h5>Salidas</h5>
                            <h2>{{ $rooms_finish->count() ? $rooms_finish->sum('details_count') : 0 }}</h2>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="icon fa fa-calendar-check-o" style="color: #3498DB"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered" style="border-left: 5px solid #E67E22">
                    <div class="panel-body" style="height: 100px;padding: 15px 20px">
                        <div class="col-md-9">
                            <h5>Dinero en caja</h5>
                            <h2><small>Bs.</small>{{ number_format($cashier_details->where('cash', 1)->sum('amount'), 2, ',', '.') }}</h2>
                        </div>
                        <div class="col-md-3 text-right">
                            <i class="icon voyager-dollar" style="color: #E67E22"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="panel">
                    <div class="panel-body">
                        <canvas id="line-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel">
                    <div class="panel-body">
                        <canvas id="bar-chart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel">
                    <div class="panel-body">
                        <canvas id="doughnut-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

{{-- @php
    $sales = App\Models\Sale::where('deleted_at', null)->groupBy('date')
                ->selectRaw('COUNT(id) as count,SUM(total) as total, SUM(discount) as discount, date')
                ->whereRaw('(proforma is null or proforma = 0)')->whereDate('date', '>', date('Y-m-d', strtotime(date('Y-m-d').' -7 days')))->orderBy('date', 'ASC')->get();
    $payments = App\Models\SalesPayment::where('deleted_at', null)->groupByRaw('DATE(created_at)')
                    ->selectRaw('COUNT(id) as count,SUM(amount) as total, DATE(created_at) as date')
                    ->whereDate('created_at', '>', date('Y-m-d', strtotime(date('Y-m-d').' -7 days')))->orderBy('date', 'ASC')->get();
    $products = App\Models\SalesDetail::with('product')->where('deleted_at', null)
                    ->selectRaw('COUNT(id) as count,SUM(quantity) as total, product_id')
                    ->groupBy('product_id')->orderBy('total', 'DESC')->limit(5)->get();
@endphp --}}

@section('css')
    <style>
        .icon{
            font-size: 35px
        }
    </style>
@endsection

@section('javascript')
    <script src="{{ asset('vendor/chartjs/chart.min.js') }}"></script>
    <script>
        // $(document).ready(function(){
        //     let sales = [];
        //     let labels = [];
        //     let values = [];

        //     sales.map(sale => {
        //         labels.push(moment(sale.date).format('dd'));
        //         values.push(sale.total - sale.discount);
        //     });

        //     var data = {
        //         labels,
        //         datasets: [{
        //             label: 'Ventas del día',
        //             data: values,
        //             backgroundColor: 'rgba(54, 162, 235, 0.5)',
        //             borderColor: 'rgba(54, 162, 235, 1)',
        //             hoverOffset: 4
        //         }]
        //     };
        //     var config = {
        //         type: 'line',
        //         data,
        //         options: {
        //             responsive: true,
        //             plugins: {
        //                 legend: {
        //                     position: 'top',
        //                 }
        //             }
        //         },
        //     };
        //     var myChart = new Chart(
        //         document.getElementById('line-chart'),
        //         config
        //     );

        //     // ==============================================
        //     let payments = [];
        //     labels = [];
        //     values = [];

        //     payments.map(payment => {
        //         labels.push(moment(payment.date).format('dd'));
        //         values.push(payment.total);
        //     });

        //     var data = {
        //         labels,
        //         datasets: [{
        //             label: 'Pagos del día',
        //             data: values,
        //             backgroundColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(255, 205, 86, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(39, 174, 96, 1)',
        //                 'rgba(155, 89, 182, 1)',
        //                 'rgba(235, 152, 78, 1)',
        //                 'rgba(52, 73, 94, 1)'
        //             ],
        //             borderColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(255, 205, 86, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(39, 174, 96, 1)',
        //                 'rgba(155, 89, 182, 1)',
        //                 'rgba(235, 152, 78, 1)',
        //                 'rgba(52, 73, 94, 1)'
        //             ],
        //         }]
        //     };
        //     var config = {
        //         type: 'bar',
        //         data,
        //         options: {
        //             responsive: true,
        //             plugins: {
        //                 legend: {
        //                     position: 'top',
        //                 }
        //             }
        //         },
        //     };
        //     var myChart = new Chart(
        //         document.getElementById('bar-chart'),
        //         config
        //     );

        //     // ==============================================
        //     let products = [];
        //     labels = [];
        //     values = [];

        //     products.map(item => {
        //         labels.push(item.product.name);
        //         values.push(parseInt(item.total));
        //     });

        //     var data = {
        //         labels,
        //         datasets: [{
        //             label: 'Productos más vendidos',
        //             data: values,
        //             backgroundColor: [
        //                 'rgba(255, 99, 132, 1)',
        //                 'rgba(39, 174, 96, 1)',
        //                 'rgba(255, 205, 86, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(235, 152, 78, 1)',
        //             ],
        //             hoverOffset: 4
        //         }]
        //     };
        //     var config = {
        //         type: 'doughnut',
        //         data
        //     };
        //     var myChart = new Chart(
        //         document.getElementById('doughnut-chart'),
        //         config
        //     );
        // });
    </script>
@stop