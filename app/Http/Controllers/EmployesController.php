<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Employe;
use App\Models\EmployePayment;

class EmployesController extends Controller
{
    public function payments_index($id){
        $employe = Employe::find($id);
        return view('employes.payments', compact('employe'));
    }

    public function payments_store($id, Request $request){
        $redirect = $request->redirect ?? $_SERVER['HTTP_REFERER'];
        try {
            EmployePayment::create([
                'employe_id' => $id,
                'user_id' => Auth::user()->id,
                'description' => $request->description,
                'amount' => $request->amount,
                'date' => $request->date
            ]);
            return redirect()->to($redirect)->with(['message' => 'Adelanto registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            return redirect()->to($redirect)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
