<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Room;
use App\Models\Reservation;
use App\Models\ReservationDetail;
use App\Models\ReservationDetailAccessory;

class ReservationsController extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $room_id = request('room_id');
        $room = $room_id ? Room::find($room_id) : null;
        return view('reservations.edit-add', compact('room'));
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
        try {
            $reservation = Reservation::create([
                'user_id' => Auth::user()->id,
                'person_id' => $request->person_id,
                'start' => $request->start,
                'finish' => $request->finish,
                'observation' => $request->observation,
                'status' => 'en curso'
            ]);
            $detail = ReservationDetail::create([
                'reservation_id' => $reservation->id,
                'room_id' => $request->room_id,
            ]);
            Room::where('id', $request->room_id)->update(['status' => 'ocupada']);
            if ($request->accessory_id) {
                for ($i=0; $i < count($request->accessory_id); $i++) { 
                    ReservationDetailAccessory::create([
                        'reservation_detail_id' => $detail->id,
                        'room_accessory_id' => $request->accessory_id[$i],
                        'price' => $request->price[$i],
                        'start' => $request->start
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('voyager.dashboard')->with(['message' => 'Hospedaje registrado', 'alert-type' => 'success']);
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            return redirect()->route('voyager.dashboard')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
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
