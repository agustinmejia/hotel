<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\ProductBranchOffice;

class ProductBranchOfficesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->custom_authorize('browse_product_branch_offices');
        return view('product-branch-offices.browse');
    }

    public function list(){
        $this->custom_authorize('browse_product_branch_offices');
        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $data = ProductBranchOffice::with(['product', 'branch_office'])
                    ->where(function($query) use ($search){
                        if($search){
                            $query->OrwhereHas('product', function($query) use($search){
                                $query->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('branch_office', function($query) use($search){
                                $query->whereRaw("name like '%$search%'");
                            });
                        }
                    })
                    ->where('deleted_at', NULL)->orderBy('id', 'DESC')->paginate($paginate);
        return view('product-branch-offices.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->custom_authorize('add_product_branch_offices');
        return view('product-branch-offices.edit-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            // Buscar si el producto ya está en stock
            $product_branch_office = ProductBranchOffice::where([
                'branch_office_id' => $request->branch_office_id,
                'product_id' => $request->product_id
            ])->first();
            if($product_branch_office){
                return redirect()->route('product-branch-offices.index')->with(['message' => 'Ya existe un registro de este producto es la sucursal seleccionada', 'alert-type' => 'error']);    
            }

            ProductBranchOffice::create([
                'branch_office_id' => $request->branch_office_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Stock de producto registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            // throw $th;
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product_branch_office = ProductBranchOffice::find($id);
            $product_branch_office->delete();
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Stock de producto eliminado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            // throw $th;
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
