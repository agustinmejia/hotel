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
                    <div class="form-group">
                        <label for="full_name">Nombre completo</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="dni">CI/NIT</label>
                        <input type="text" name="dni" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">N&deg; de celular</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="birthday">Fecha de nac.</label>
                        <input type="date" name="birthday" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="origin">Procedencia</label>
                        <input type="text" name="origin" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="job">Ocupación</label>
                        <input type="text" name="job" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="street">Dirección</label>
                        <textarea name="street" class="form-control" rows="3"></textarea>
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