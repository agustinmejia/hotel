<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function general_index(Request $requets){
        $this->custom_authorize('browse_report-general');
        return view('reports.general-browse');
    }
}
