<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CashiersController;
use App\Http\Controllers\ProductBranchOfficesController;
use App\Http\Controllers\RoomsController;

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

    // Reservations
    Route::resource('reservations', ReservationsController::class);
    Route::post('reservations/payment/store', [ReservationsController::class, 'payment_store'])->name('reservations.payment.store');
    Route::post('reservations/product/store', [ReservationsController::class, 'product_store'])->name('reservations.product.store');
    Route::post('reservations/product/payment/store', [ReservationsController::class, 'product_payment_store'])->name('reservations.product.payment.store');
    Route::post('reservations/close', [ReservationsController::class, 'close'])->name('reservations.close');

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
});

// Clear cache
Route::get('/admin/clear-cache', function() {
    Artisan::call('optimize:clear');
    return redirect('/admin/profile')->with(['message' => 'Cache eliminada.', 'alert-type' => 'success']);
})->name('clear.cache');
