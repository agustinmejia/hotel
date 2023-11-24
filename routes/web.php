<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CashiersController;
use App\Http\Controllers\ProductBranchOfficesController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesController;

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
    Route::post('people/store/ajax', [PeopleController::class, 'store'])->name('people.store');

    // Recepción
    Route::get('reception', function () {
        update_hosting();
        return view('reservations.index');
    })->name('reception.index');

    // Reservations
    Route::resource('reservations', ReservationsController::class);
    Route::get('reservations/list/ajax', [ReservationsController::class, 'list'])->name('reservations.list');
    Route::post('reservations/payment/store', [ReservationsController::class, 'payment_store'])->name('reservations.payment.store');
    Route::post('reservations/product/store', [ReservationsController::class, 'product_store'])->name('reservations.product.store');
    Route::post('reservations/product/payment/store', [ReservationsController::class, 'product_payment_store'])->name('reservations.product.payment.store');
    Route::post('reservations/penalties/payment/store', [ReservationsController::class, 'penalties_payment_store'])->name('reservations.penalties.payment.store');
    Route::post('reservations/close', [ReservationsController::class, 'close'])->name('reservations.close');
    Route::post('reservations/change-room', [ReservationsController::class, 'change_room'])->name('reservations.change.room');
    Route::post('reservations/add-people', [ReservationsController::class, 'add_people'])->name('reservations.add.people');
    Route::post('reservations/add-penalty', [ReservationsController::class, 'add_penalty'])->name('reservations.add.penalty');

    // City
    Route::get('cities/search/ajax', [CitiesController::class, 'search'])->name('cities.search');

    // Rooms
    Route::post('rooms/{id}/update/status', [RoomsController::class, 'update_status'])->name('rooms.update.status');

    // Products
    Route::get('products/search/ajax', [ProductsController::class, 'search'])->name('products.search');

    Route::resource('product-branch-offices', ProductBranchOfficesController::class);
    Route::get('product-branch-offices/list/ajax', [ProductBranchOfficesController::class, 'list'])->name('product-branch-offices.list');

    // Cashier
    Route::resource('cashiers', CashiersController::class);
    Route::get('cashiers/list/ajax', [CashiersController::class, 'list'])->name('cashiers.list');
    Route::get('cashiers/{id}/close', [CashiersController::class, 'close_index'])->name('cashiers.close.index');
    Route::post('cashiers/{id}/close/store', [CashiersController::class, 'close_store'])->name('cashiers.close.store');
    Route::get('cashiers/{id}/print', [CashiersController::class, 'print'])->name('cashiers.print');

    // Sales
    Route::resource('sales', SalesController::class);
    Route::get('sell', [SalesController::class, 'create']);

    // Reports
    Route::get('report-general', [ReportsController::class, 'general_index'])->name('report-general.index');

    // Import
    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import/store', [ImportController::class, 'store'])->name('import.store');
});

// Clear cache
Route::get('/admin/clear-cache', function() {
    Artisan::call('optimize:clear');
    return redirect('/admin/profile')->with(['message' => 'Cache eliminada.', 'alert-type' => 'success']);
})->name('clear.cache');
