<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Models
use App\Models\Product;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function search() {
        $q = request('q');
        $data = [];
        if ($q) {
            $data = Product::with(['type'])
                        ->whereRaw('(id = '.intval($q).' or name like "%'.$q.'%")')
                        ->orWhere(function($query) use ($q){
                            $query->OrwhereHas('type', function($query) use($q){
                                $query->whereRaw('name like "%'.$q.'%"');
                            });
                        })->get();
        }
        return response()->json($data);
    }
}
