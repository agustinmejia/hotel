<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Person;

class PeopleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function list() {
        //
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
            Person::create([
                'full_name' => $request->full_name,
                'dni' => $request->dni,
                'phone' => $request->phone,
                'address' => $request->address,
                'birthday' => $request->birthday,
                'origin' => $request->origin,
                'job' => $request->job,
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
            $data = Person::whereRaw('(dni like "%'.$q.'%" or full_name like "%'.$q.'%" or phone like "%'.$q.'%" or origin like "%'.$q.'%")')->get();
        }
        return response()->json($data);
    }
}
