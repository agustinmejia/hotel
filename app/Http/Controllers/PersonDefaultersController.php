<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\PersonDefaulter;
use App\Models\Cashier;

class PersonDefaultersController extends Controller
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
        return view('person-defaulters.browse');
    }

    public function list(){
        $this->custom_authorize('browse_people');
        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $status = request('status') ?? null;
        $data = PersonDefaulter::with(['cashier', 'person', 'reservation_detail', 'payments'])
                    ->where(function($query) use ($search){
                        if($search){
                            $query->OrwhereHas('person', function($q) use($search){
                                $q->whereRaw("(full_name like '%$search%' or dni like '%$search%' or phone like '%$search%')");
                            })
                            ->OrWhereRaw("observations like '%$search%'");
                        }
                    })
                    ->whereRaw($status ? "status = '$status'" : 1)
                    ->where('deleted_at', NULL)->orderBy('id', 'DESC')->paginate($paginate);
        return view('person-defaulters.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person_defaulter = PersonDefaulter::with(['cashier', 'person', 'payments', 'reservation_detail.days.payments', 'reservation_detail.sales.details.product', 'reservation_detail.penalties', 'reservation_detail.room'])->where('id', $id)->first();
        $cashier = Cashier::where('status', 'abierta')->where('user_id', Auth::user()->id)->first();
        return view('person-defaulters.read', compact('person_defaulter', 'cashier'));
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
