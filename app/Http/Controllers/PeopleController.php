<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Person;
use App\Models\City;

class PeopleController extends Controller
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

    public function list(){
        $this->custom_authorize('browse_people');
        $paginate = request('paginate') ?? 10;
        $search = request('search') ?? null;
        $data = Person::with(['reservations' => function($q){
                        $q->where('status', 'en curso');
                    }, 'city.state.country'])
                    ->where(function($query) use ($search){
                        if($search){
                            $query->OrwhereHas('city', function($q) use($search){
                                $q->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('city.state', function($q) use($search){
                                $q->whereRaw("name like '%$search%'");
                            })
                            ->OrwhereHas('city.state.country', function($q) use($search){
                                $q->whereRaw("name like '%$search%'");
                            })
                            ->whereRaw("(full_name like '%$search%' or dni like '%$search%' or phone like '%$search%')");
                        }
                    })
                    ->where('deleted_at', NULL)->orderBy('id', 'DESC')->paginate($paginate);
        return view('vendor.voyager.people.list', compact('data'));
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
        try {

            $city_id = 1;
            if($request->city_id){
                $city_id = $request->city_id;
            }elseif($request->city_name){
                $city_id = City::create([
                    'state_id' => 1,
                    'name' => $request->city_name
                ])->id;
            }

            Person::create([
                'full_name' => $request->full_name,
                'dni' => $request->dni,
                'phone' => $request->phone,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'city_id' => $city_id,
                'job' => $request->job,
                'gender' => $request->gender,
                'photo' => $request->photo
            ]);

            return response()->json(['success' => 1]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 1]);
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

    public function search() {
        $q = request('q');
        $data = [];
        if ($q) {
            $data = Person::whereRaw('(dni like "%'.$q.'%" or full_name like "%'.$q.'%" or phone like "%'.$q.'%")')->get();
        }
        return response()->json($data);
    }
}
