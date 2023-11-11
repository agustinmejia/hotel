<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

// Imports
use App\Imports\PeopleImport;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
        $this->custom_authorize('browse_import');
        return view('import.browse');
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            switch ($request->type == 1) {
                case 'value':
                    Excel::import(new PeopleImport, request()->file('file'));
                    DB::commit();
                    return redirect()->route('voyager.people.index')->with(['message' => 'Importación exitosa', 'alert-type' => 'success']);
                    break;
                
                default:
                return redirect()->route('import.index')->with(['message' => 'El tipo de dato a importar es desconocido', 'alert-type' => 'error']);
                    break;
            }
        } catch (\Throwable $th) {
            DB::rollback();
            return redirect()->route('import.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
