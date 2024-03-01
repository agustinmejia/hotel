<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\ProductBranchOffice;
use App\Models\ProductBranchOfficeStockChange;
use App\Models\SaleDetail;

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
                            ->OrwhereHas('product.type', function($query) use($search){
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
                return redirect()->route($request->route_redirect ?? 'product-branch-offices.index')->with(['message' => 'Ya existe un registro de este producto es la sucursal seleccionada', 'alert-type' => 'error']);    
            }

            ProductBranchOffice::create([
                'branch_office_id' => $request->branch_office_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'initial_quantity' => $request->initial_quantity
            ]);
            return redirect()->route($request->route_redirect ?? 'product-branch-offices.index')->with(['message' => 'Stock de producto registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            // throw $th;
            return redirect()->route($request->route_redirect ?? 'product-branch-offices.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
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
            if($product_branch_office->quantity != $product_branch_office->initial_quantity	){
                return redirect()->route('product-branch-offices.index')->with(['message' => 'El producto ya se vendió y no tiene el stock inicial', 'alert-type' => 'warning']);
            }
            $product_branch_office->delete();
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Registro de stock eliminado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function change_stock(Request $request){
        DB::beginTransaction();
        try {
            $product_branch_office = ProductBranchOffice::find($request->product_branch_office_id);
            $old_quantity = $product_branch_office->quantity;
            $product_branch_office->quantity += $request->quantity;
            $product_branch_office->update();

            ProductBranchOfficeStockChange::create([
                'user_id' => Auth::user()->id,
                'product_branch_office_id' => $request->product_branch_office_id,
                'type' => 'ingreso',
                'quantity' => $request->quantity,
                'old_quantity' => $old_quantity,
                'observation' => $request->observation
            ]);

            DB::commit();

            return redirect()->route('product-branch-offices.index')->with(['message' => 'Registro de stock exitoso', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('product-branch-offices.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    public function product_sales_history($id){
        $sales = SaleDetail::with(['sale.user'])->where('product_id', $id)->orderBy('created_at', 'DESC')->get();
        return view('product-branch-offices.partials.sales-history', compact('sales'));
    }
}
