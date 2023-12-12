<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Employe;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function general_index(Request $requets){
        $this->custom_authorize('browse_report-general');
        $type = request('type') ?? null;
        switch ($type) {
            case 'print':
                return view('reports.general-print');
                break;
            default:
                return view('reports.general-browse');
                break;
        }
    }

    public function employes_payments_index(){
        return view('reports.employes-payments-browse');
    }

    public function employes_payments_list(Request $requet){
        $employes = Employe::with(['payments' => function($q) use($requet){
                        $q->whereRaw($requet->status ? "status = '".$requet->status."'" : 1)
                        ->whereRaw($requet->start ? "date >= '".$requet->start."'" : 1)
                        ->whereRaw($requet->finish ? "date <= '".$requet->finish."'" : 1);
                    }])->get();
        return view('reports.employes-payments-list', compact('employes'));
    }
}
