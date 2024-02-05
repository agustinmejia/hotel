<form action="{{ route('cashiers.store') }}" id="form-create-cashier" class="form-submit" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
    <input type="hidden" name="redirect" value="{{ $redirect ?? '' }}">
    <div class="modal modal-primary fade" tabindex="-1" id="create-cashier-modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="fa fa-money"></i> Aperturar caja</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="branch_office_id">Sucursal</label>
                        <select name="branch_office_id" id="select-branch_office_id" class="form-control" required>
                            <option value="">--Seleccionar sucursal--</option>
                            @foreach (App\Models\BranchOffice::where('status', 1)->get() as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="initial_amount">Monto de apertura</label>
                        <input type="number" name="initial_amount" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="observations">Observaciones</label>
                        <textarea name="observations" class="form-control" rows="3"></textarea>
                    </div>
                    {{-- <div class="form-group">
                        <div class="checkbox">
                            <label data-toggle="tooltip" data-placement="top" title="Si seleccionas ésta opción el monto de apertura no se agregará a la caja"><input type="checkbox" name="shift_change" value="1">Cambio de turno</label>
                        </div>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark btn-submit">Aperturar <i class="fa fa-money"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>