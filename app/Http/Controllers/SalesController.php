<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\ProductBranchOffice;

class SalesController extends Controller
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
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->custom_authorize('edit_sales');
        $cashier = Cashier::where('user_id', Auth::user()->id)->where('status', 'abierta')->first();
        return view('sales.edit-add', compact('cashier'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        DB::beginTransaction();
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        try {
            $sale = Sale::create([
                'user_id' => Auth::user()->id,
                'person_id' => $request->person_id ?? 1,
                'date' => $request->date.' '.date('H:i:s'),
                'status' => 'pagada'
            ]);

            for ($i=0; $i < count($request->product_id); $i++) { 
                $sale_detail = SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $request->product_id[$i],
                    'price' => $request->price[$i],
                    'quantity' => $request->quantity[$i],
                    'status' => 'pagado'
                ]);

                // Registrar pago en caja
                CashierDetail::create([
                    'cashier_id' => $request->cashier_id,
                    'sale_detail_id' => $sale_detail->id,
                    'type' => 'ingreso',
                    'amount' => $request->price[$i] * $request->quantity[$i],
                    'cash' => !$request->payment_qr ? 1: 0
                ]);

                // Descontar del stock
                $cashier = Cashier::find($request->cashier_id);
                $product = ProductBranchOffice::where('product_id', $request->product_id[$i])
                            ->where('branch_office_id', $cashier->branch_office_id)
                            ->where('quantity', '>=', $request->quantity[$i])
                            ->first();
                $product->quantity -= $request->quantity[$i];
                $product->update();
            }

            DB::commit();
            return redirect()->to($redirect)->with(['message' => 'Venta registrada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
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
        //
    }
}
