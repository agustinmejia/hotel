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
use App\Http\Controllers\EmployesController;

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

Route::group(['prefix' => 'admin', 'middleware' => 'requests.log'], function () {
    Voyager::routes();

    // People
    Route::get('people/list/ajax', [PeopleController::class, 'list'])->name('people.list');
    Route::get('people/search/ajax', [PeopleController::class, 'search'])->name('people.search');
    Route::post('people/store/ajax', [PeopleController::class, 'store'])->name('people.store');
    Route::delete('people/{id}', [PeopleController::class, 'destroy']);

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
    Route::post('reservations/add-payment', [ReservationsController::class, 'add_payment'])->name('reservations.add.payment');
    Route::post('reservations/total-payment', [ReservationsController::class, 'total_payment'])->name('reservations.total.payment');
    Route::post('reservations/update/amount-day', [ReservationsController::class, 'update_amount_day'])->name('reservations.update.amount_day');
    Route::post('reservations/remove-service', [ReservationsController::class, 'remove_service'])->name('reservations.remove.service');
    Route::post('reservations/details/update/daily-payment', [ReservationsController::class, 'details_update_daily_payment'])->name('reservations.details.update.daily-payment');
    Route::get('reservations/services/list', [ReservationsController::class, 'services_list'])->name('services.list');
    Route::post('reservations/services/store', [ReservationsController::class, 'services_store'])->name('services.store');

    // City
    Route::get('cities/search/ajax', [CitiesController::class, 'search'])->name('cities.search');

    // Rooms
    Route::post('rooms/{id}/update/status', [RoomsController::class, 'update_status'])->name('rooms.update.status');

    // Products
    Route::get('products/search/ajax', [ProductsController::class, 'search'])->name('products.search');

    // Branch office
    Route::resource('product-branch-offices', ProductBranchOfficesController::class);
    Route::get('product-branch-offices/list/ajax', [ProductBranchOfficesController::class, 'list'])->name('product-branch-offices.list');
    Route::post('product-branch-offices/change-stock', [ProductBranchOfficesController::class, 'change_stock'])->name('product-branch-offices.change.stock');
    Route::get('product-branch-offices/product/{id}/sales-history', [ProductBranchOfficesController::class, 'product_sales_history']);

    // Cashier
    Route::resource('cashiers', CashiersController::class);
    Route::get('cashiers/list/ajax', [CashiersController::class, 'list'])->name('cashiers.list');
    Route::get('cashiers/{id}/close', [CashiersController::class, 'close_index'])->name('cashiers.close.index');
    Route::post('cashiers/{id}/close/store', [CashiersController::class, 'close_store'])->name('cashiers.close.store');
    Route::get('cashiers/{id}/print', [CashiersController::class, 'print'])->name('cashiers.print');
    Route::post('cashiers/add/register', [CashiersController::class, 'add_register'])->name('cashiers.add.register');

    // Sales
    Route::resource('sales', SalesController::class);
    Route::get('sell', [SalesController::class, 'create']);

    // Employes
    Route::get('employes/{id}/payments', [EmployesController::class, 'payments_index'])->name('employes.payments');
    Route::post('employes/{id}/payments/store', [EmployesController::class, 'payments_store'])->name('employes.payments.store');
    Route::post('employes/{id}/payoff/store', [EmployesController::class, 'payoff_store'])->name('employes.payoff.store');

    // Reports
    Route::get('report-general', [ReportsController::class, 'general_index'])->name('report-general.index');
    Route::post('report-general/list', [ReportsController::class, 'general_list'])->name('report-general.list');
    Route::get('report-employes-payments', [ReportsController::class, 'employes_payments_index'])->name('report-employes-payments.index');
    Route::post('report-employes-payments/list', [ReportsController::class, 'employes_payments_list'])->name('report-employes-payments.list');
    Route::get('report-services', [ReportsController::class, 'services_index'])->name('report-services.index');
    Route::post('report-services/list', [ReportsController::class, 'services_list'])->name('report-services.list');
    Route::get('report-employes-cleaning', [ReportsController::class, 'employes_cleaning_index'])->name('report-employes-cleaning.index');
    Route::post('report-employes-cleaning/list', [ReportsController::class, 'employes_cleaning_list'])->name('report-employes-cleaning.list');
    Route::get('report-employes-debts', [ReportsController::class, 'employes_debts_index'])->name('report-employes-debts.index');
    Route::get('report-cashiers-registers', [ReportsController::class, 'cashiers_registers_index'])->name('report-cashiers-registers.index');
    Route::post('report-cashiers-registers/list', [ReportsController::class, 'cashiers_registers_list'])->name('report-cashiers-registers.list');
    Route::get('report-reservations', [ReportsController::class, 'reservations_index'])->name('report-reservations.index');
    Route::post('report-reservations/list', [ReportsController::class, 'reservations_list'])->name('report-reservations.list');

    // Import
    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import/store', [ImportController::class, 'store'])->name('import.store');
});

// Clear cache
Route::get('/admin/clear-cache', function() {
    Artisan::call('optimize:clear');
    return redirect('/admin/profile')->with(['message' => 'Cache eliminada.', 'alert-type' => 'success']);
})->name('clear.cache');
