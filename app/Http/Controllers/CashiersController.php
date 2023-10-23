<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Cashier;
use App\Models\CashierDetail;
use App\Models\User;

class CashiersController extends Controller
{
    public function store(Request $request){
        // dd($request->all());
        DB::beginTransaction();
        try {
            $cashier = Cashier::create([
                'user_id' => $request->user_id,
                'branch_office_id' => $request->branch_office_id,
                'observations' => $request->observations,
                'status' => 'abierta'
            ]);

            if ($request->initial_amount) {
                CashierDetail::create([
                    'cashier_id' => $cashier->id,
                    'type' => 'ingreso',
                    'amount' => $request->initial_amount,
                    'observations' => 'Apertura de caja'
                ]);
            }

            // Update user
            $user = User::find(Auth::user()->id);
            $user->branch_office_id = $request->branch_office_id;
            $user->update();

            DB::commit();
            return redirect()->to($request->redirect ?? 'admin/cashiers')->with(['message' => 'Caja aperturada', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
            return redirect()->to($request->redirect ?? 'admin/cashiers')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
