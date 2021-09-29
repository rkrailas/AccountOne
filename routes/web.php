<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\ContactSearchBar;
use App\Http\Livewire\AccStar\ListGjournal;
use App\Http\Livewire\AccStar\Customer;
use App\Http\Livewire\AccStar\CustomerForm;
use App\Http\Livewire\AccStar\Accounting;
use App\Http\Livewire\AccStar\SoDeliveryTax;
use App\Http\Livewire\AccStar\ReceiveOnSales;
use App\Http\Livewire\AccStar\PostJournal;
use App\Http\Livewire\AccStar\CancelPostJournal;
use App\Http\Livewire\AccStar\TrialBalance;
use App\Http\Livewire\AccStar\Products;

use App\Http\Livewire\Test1;
use App\Http\Livewire\Test2Sumgrouparray;
use App\Http\Livewire\Test3Coa;
use App\Http\Livewire\Test4DataTable;

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
Route::get('accstar/products', Products::class)->name('accstar.products');


Route::get('test1', Test1::class);
Route::get('test2', Test2Sumgrouparray::class);
Route::get('test3', Test3Coa::class);
Route::get('test4', Test4DataTable::class);
Route::get('accstar/accounting', Accounting::class); //Not Used