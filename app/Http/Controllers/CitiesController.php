<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\City;

class CitiesController extends Controller
{
    public function search() {
        $q = request('q');
        $data = [];
        if ($q) {
            $data = City::with(['state.country'])->whereRaw('(name like "%'.$q.'%" or province like "%'.$q.'%")')->get();
        }
        return response()->json($data);
    }
}
