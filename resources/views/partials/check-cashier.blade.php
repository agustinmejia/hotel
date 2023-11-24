@if(!$cashier)
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron" style="padding: 10px 30px; margin: 0px">
                <h1>Advertencia</h1>
                <p>No ha realizado apertura de caja, por lo que no podrá registrar pagos ni ventas.</p>
                <p><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#create-cashier-modal" role="button">Abrir caja <i class="fa fa-money"></i></a></p>
            </div>
        </div>
    </div>
@endif