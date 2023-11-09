<form action="{{ route('people.store').'?ajax=1' }}" id="form-person" class="form-submit" method="POST">
    @csrf
    <div class="modal modal-primary fade" tabindex="-1" id="person-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-tag"></i> Registrar huesped</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="full_name">Nombre completo</label>
                            <input type="text" name="full_name" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="dni">CI/NIT</label>
                            <input type="text" name="dni" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="phone">N&deg; de celular</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="birthday">Fecha de nac.</label>
                            <input type="date" name="birthday" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="job">Ocupación</label>
                            <input type="text" name="job" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="city_id">Procedencia</label>
                            <select name="city_id" class="form-control" id="select-city_id"></select>
                            <input type="text" name="city_name" id="input-city_name" class="form-control" placeholder="Nombre de la ciudad" style="display: none">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="street">Dirección</label>
                            <textarea name="street" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark btn-submit">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</form>