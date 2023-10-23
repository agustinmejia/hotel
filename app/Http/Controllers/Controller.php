<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function custom_authorize($permission, $message = null){
        if(!Auth::user()->hasPermission($permission)){
            abort(403, Str::upper($message ?? 'THIS ACTION IS UNAUTHORIZED.'));
        }
    }
}
