@extends('voyager::master')

@section('page_title', 'Añadir stock de producto')

@section('page_header')
    <h1 class="page-title">
        <i class="fa fa-cubes"></i>
        Añadir stock de productos
    </h1>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <form role="form" class="form-submit" action="{{ route('product-branch-offices.store') }}" method="post">
                        @csrf
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
                                <label for="product_id">Producto</label>
                                <select name="product_id" class="form-control select2" required>
                                    <option value="" disabled selected>--Seleccione el producto--</option>
                                    @foreach (App\Models\Product::where('status', 1)->get() as $item)
                                    <option value="{{ $item->id }}">{{ str_pad($item->id, 4, "0", STR_PAD_LEFT) }} - {{ $item->name }} {{ intval($item->price) == floatval($item->price) ? intval($item->price) : $item->price }} Bs.</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="quantity">Stock</label>
                                <input type="number" name="quantity" class="form-control" step="0.1" min="0.1" required>
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
        $(document).ready(function(){

        });
    </script>
@stop
