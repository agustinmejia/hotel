<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\PeopleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', function () {
    return redirect('admin/login');
})->name('login');

Route::get('/', function () {
    return redirect('admin');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    // People
    Route::get('people/list/ajax', [PeopleController::class, 'list'])->name('people.list');
    Route::get('people/search/ajax', [PeopleController::class, 'search'])->name('people.search');

    // Reservaciones
    Route::resource('reservations', ReservationsController::class);
});

// Clear cache
Route::get('/admin/clear-cache', function() {
    Artisan::call('optimize:clear');
    return redirect('/admin/profile')->with(['message' => 'Cache eliminada.', 'alert-type' => 'success']);
})->name('clear.cache');
