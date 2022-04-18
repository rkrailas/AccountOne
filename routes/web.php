<?php

use Illuminate\Support\Facades\Route;

use App\Http\Livewire\Customer\Customer;
use App\Http\Livewire\Inventory\Inventory;
use App\Http\Livewire\Inventory\AdjustInventory;
use App\Http\Livewire\Inventory\InventorySerialNo;
use App\Http\Livewire\Inventory\InventoryLotNo;
use App\Http\Livewire\Sales\SoServiceTax;
use App\Http\Livewire\Sales\SoDeliveryTax;
use App\Http\Livewire\Sales\SalesOrder;
use App\Http\Livewire\Sales\SoDelivery;
use App\Http\Livewire\Sales\SoTax;
use App\Http\Livewire\Sales\CancelSalesOrder;
use App\Http\Livewire\Sales\CancelSoDelivery;
use App\Http\Livewire\Sales\CancelSoTax;
use App\Http\Livewire\Sales\CancelSoDeliveryTax;
use App\Http\Livewire\Sales\AdjustSoDeliveryTax;
use App\Http\Livewire\Sales\CancelAdjustSoDeliveryTax;
use App\Http\Livewire\Sales\ReturnGoods;
use App\Http\Livewire\Sales\CancelReturnGoods;
use App\Http\Livewire\Sales\SalesHistory;
use App\Http\Livewire\Sales\SalesBackOrder;
use App\Http\Livewire\Finance\ReceiveOnSales;
use App\Http\Livewire\Finance\ReceiveOnSalesService;
use App\Http\Livewire\Finance\CancelReceiveOnSales;
use App\Http\Livewire\Finance\CancelReceiveOnSalesService;
use App\Http\Livewire\Finance\ReceiveHistory;
use App\Http\Livewire\Finance\BillingNotice;
use App\Http\Livewire\Account\ListGjournal;
use App\Http\Livewire\Account\PostJournal;
use App\Http\Livewire\Account\CancelPostJournal;
use App\Http\Livewire\Account\TrialBalance;
use App\Http\Livewire\Account\ListCoa;
use App\Http\Livewire\Tax\SalesTax;
use App\Http\Livewire\Tax\PurchaseTax;
use App\Http\Livewire\Tax\Withholdingtax;

use App\Http\Livewire\Test1CustomerForm;
use App\Http\Livewire\Test2Sumgrouparray;
use App\Http\Livewire\Test3;
use App\Http\Livewire\Test4DataTable;
use App\Http\Livewire\Test5;
use App\Http\Livewire\Test6Invoice;
use App\Http\Livewire\Test7;

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

//======== Customer ========
Route::get('customer/customer', Customer::class)->name('customer.customer');

//======== Inventory ========
Route::get('inventory/inventory', Inventory::class)->name('inventory.inventory');
Route::get('inventory/adjustinventory', AdjustInventory::class)->name('inventory.adjustinventory');
Route::get('inventory/inventoryserialno', InventorySerialNo::class)->name('inventory.inventoryserialno');
Route::get('inventory/inventorylotno', InventoryLotNo::class)->name('inventory.inventorylotno');


//======== Sales ========
Route::get('sales/soservicetax', SoServiceTax::class)->name('sales.soservicetax');
Route::get('sales/sodeliverytax', SoDeliveryTax::class)->name('sales.sodeliverytax');
Route::get('sales/salesorder', SalesOrder::class)->name('sales.salesorder');
Route::get('sales/sodelivery', SoDelivery::class)->name('sales.sodelivery');
Route::get('sales/sotax', SoTax::class)->name('sales.sotax');
Route::get('sales/cancelsalesorder', CancelSalesOrder::class)->name('sales.cancelsalesorder');
Route::get('sales/cancelsodelivery', CancelSoDelivery::class)->name('sales.cancelsodelivery');
Route::get('sales/cancelsotax', CancelSoTax::class)->name('sales.cancelsotax');
Route::get('sales/cancelsodeliverytax', CancelSoDeliveryTax::class)->name('sales.cancelsodeliverytax');
Route::get('sales/adjustsodeliverytax', AdjustSoDeliveryTax::class)->name('sales.adjustsodeliverytax');
Route::get('sales/canceladjustsodeliverytax', CancelAdjustSoDeliveryTax::class)->name('sales.canceladjustsodeliverytax');
Route::get('sales/returngoods', ReturnGoods::class)->name('sales.returngoods');
Route::get('sales/cancelreturngoods', CancelReturnGoods::class)->name('sales.cancelreturngoods');
Route::get('sales/saleshistory', SalesHistory::class)->name('sales.saleshistory');
Route::get('sales/salesbackorder', SalesBackOrder::class)->name('sales.salesbackorder');

//======== Finance ========
Route::get('finance/receiveonsales', ReceiveOnSales::class)->name('finance.receiveonsales');
Route::get('finance/receiveonsalesservice', ReceiveOnSalesService::class)->name('finance.receiveonsalesservice');
Route::get('finance/cancelreceiveonsales', CancelReceiveOnSales::class)->name('finance.cancelreceiveonsales');
Route::get('finance/cancelreceiveonsalesservice', CancelReceiveOnSalesService::class)->name('finance.cancelreceiveonsalesservice');
Route::get('finance/billingnotice', BillingNotice::class)->name('finance.billingnotice');
Route::get('finance/receivehistory', ReceiveHistory::class)->name('finance.receivehistory');

//======== Account ========
Route::get('account/gjournal', ListGjournal::class)->name('account.gljournal');
Route::get('account/postjournal', PostJournal::class)->name('account.postjournal');
Route::get('account/cancelpostjournal', CancelPostJournal::class)->name('account.cancelpostjournal');
Route::get('account/trialbalance', TrialBalance::class)->name('account.trialbalance');
Route::get('account/listcoa', ListCoa::class)->name('account.listcoa');

//======== Tax ========
Route::get('tax/salestax', SalesTax::class)->name('tax.salestax');
Route::get('tax/purchasetax', PurchaseTax::class)->name('tax.purchasetax');
Route::get('tax/withholdingtax', Withholdingtax::class)->name('tax.withholdingtax');

//======== Test ========
Route::get('/', function () {
    return view('welcome');
});
Route::get('test1', Test1CustomerForm::class);
Route::get('test2', Test2Sumgrouparray::class);
Route::get('test3', Test3::class);
Route::get('test4', Test4DataTable::class);
Route::get('test5', Test5::class);
Route::get('test6', Test6Invoice::class);
Route::get('test7', Test7::class);