@extends('voyager::master')

@section('page_title', 'Apertura de Caja')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-logbook"></i>
        Apertura de Caja
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <form role="form" class="form-submit" action="{{ route('cashiers.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <div class="panel-body">
                            @php
                                $branch_office = App\Models\BranchOffice::where('status', 1)->get();
                            @endphp
                            <div class="form-group col-md-6">
                                <label for="branch_office_id">Sucursal</label>
                                <select name="branch_office_id" class="form-control select2" required>
                                    <option value="" disabled selected>--Seleccione la sucursal--</option>
                                    @foreach ($branch_office as $item)
                                    <option value="{{ $item->id }}" @if($branch_office->count() == 1) selected @endif>{{ $item->name }} {{ $item->address ? ' - '.$item->address : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="initial_amount">Monto de apertura</label>
                                <input type="number" name="initial_amount" class="form-control">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="observations">Observaciones</label>
                                <textarea name="observations" class="form-control" rows="3"></textarea>
                                <small>Puede ingresar una descripción del monto de apertura</small>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <button type="submit" class="btn btn-primary save btn-submit">Guardar <i class="voyager-check"></i> </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    
@stop

@section('javascript')
    <script>
        var user = @json(Auth::user());
        $(document).ready(function(){
            if (user.branch_office_id) {
                $('#select-branch_office_id').val(user.branch_office_id).trigger('change');
            }
        });
    </script>
@stop
