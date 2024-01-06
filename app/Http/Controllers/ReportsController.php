<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Employe;
use App\Models\Room;
use App\Models\EmployeActivity;

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
        $this->custom_authorize('browse_report-employes-payments');
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

    public function services_index(){
        $this->custom_authorize('browse_report-services');
        return view('reports.services-browse');
    }

    public function services_list(Request $request){
        $info_type = $request->info_type;
        $rooms = Room::with(['reservation_detail' => function($q){
                        $q->where('status', 'ocupada');
                    },'reservation_detail.accessories' => function($q) use($request){
                    if($request->info_type == 'accessory'){
                        $q->where('room_accessory_id', $request->service_id);
                    }
                }, 'reservation_detail.food.type' => function($q) use($request){
                    if($request->info_type == 'food_type'){
                        $q->where('id', $request->service_id);
                    }
                }, 'reservation_detail.reservation.aditional_people'])
                ->where('status', 'ocupada')->orderBy('floor_number')->orderBy('code')->get();
        return view('reports.services-list', compact('rooms', 'info_type'));
    }

    public function employes_cleaning_index(){
        $this->custom_authorize('browse_report-employes-cleaning');
        return view('reports.cleaning-browse');
    }

    public function employes_cleaning_list(Request $request){
        $group_by = $request->group_by;
        $activities = EmployeActivity::with(['employe', 'room'])
                        ->whereRaw($request->employe_id ? "employe_id = ".$request->employe_id : 1)
                        ->whereRaw($request->month ? "DATE_FORMAT(created_at, '%Y-%m') = '".$request->month."'" : 1)->get();
        return view('reports.cleaning-list', compact('activities', 'group_by'));
    }

    public function employes_debts_index(){
        $this->custom_authorize('browse_report-debts');
        return view('reports.debts-browse');
    }
}
