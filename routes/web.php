<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\ContactSearchBar;
use App\Http\Livewire\AccStar\ListGjournal;
use App\Http\Livewire\AccStar\Customer;
use App\Http\Livewire\AccStar\CustomerForm;
use App\Http\Livewire\AccStar\SoDeliveryTax;
use App\Http\Livewire\AccStar\ReceiveOnSales;
use App\Http\Livewire\AccStar\PostJournal;
use App\Http\Livewire\AccStar\CancelPostJournal;
use App\Http\Livewire\AccStar\TrialBalance;
use App\Http\Livewire\AccStar\Inventory;
use App\Http\Livewire\AccStar\ListCoa;
use App\Http\Livewire\AccStar\AdjustInventory;

use App\Http\Livewire\Test1CustomerForm;
use App\Http\Livewire\Test2Sumgrouparray;
use App\Http\Livewire\Test3;
use App\Http\Livewire\Test4DataTable;
use App\Http\Livewire\Test5;
use App\Http\Livewire\Test6Invoice;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('contactsearch', ContactSearchBar::class);
Route::get('accstar/gjournal', ListGjournal::class)->name('accstar.gljournal');
Route::get('accstar/customer', Customer::class)->name('accstar.customer');
Route::get('accstar/customer/{customer_id}/edit', CustomerForm::class)->name('accstar.customer.form');
Route::get('accstar/sodeliverytax', SoDeliveryTax::class)->name('accstar.sodeliverytax');
Route::get('accstar/receiveonsales', ReceiveOnSales::class)->name('accstar.receiveonsales');
Route::get('accstar/postjournal', PostJournal::class)->name('accstar.postjournal');
Route::get('accstar/cancelpostjournal', CancelPostJournal::class)->name('accstar.cancelpostjournal');
Route::get('accstar/trialbalance', TrialBalance::class)->name('accstar.trialbalance');
Route::get('accstar/inventory', Inventory::class)->name('accstar.inventory');
Route::get('accstar/listcoa', ListCoa::class)->name('accstar.listcoa');
Route::get('accstar/adjustinventory', AdjustInventory::class)->name('accstar.adjustinventory');


Route::get('test1', Test1CustomerForm::class);
Route::get('test2', Test2Sumgrouparray::class);
Route::get('test3', Test3::class);
Route::get('test4', Test4DataTable::class);
Route::get('test5', Test5::class);
Route::get('test6', Test6Invoice::class);