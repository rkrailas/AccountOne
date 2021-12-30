<?php

use Illuminate\Support\Facades\Route;

use App\Http\Livewire\AccStar\Customer\Customer;
use App\Http\Livewire\AccStar\Inventory\Inventory;
use App\Http\Livewire\AccStar\Inventory\AdjustInventory;
use App\Http\Livewire\AccStar\Inventory\InventorySerialNo;
use App\Http\Livewire\AccStar\Inventory\InventoryLotNo;
use App\Http\Livewire\AccStar\Sales\SoServiceTax;
use App\Http\Livewire\AccStar\Sales\SoDeliveryTax;
use App\Http\Livewire\AccStar\Sales\SalesOrder;
use App\Http\Livewire\AccStar\Sales\SoDelivery;
use App\Http\Livewire\AccStar\Sales\SoTax;
use App\Http\Livewire\AccStar\Sales\CancelSalesOrder;
use App\Http\Livewire\AccStar\Sales\CancelSoDelivery;
use App\Http\Livewire\AccStar\Sales\CancelSoTax;
use App\Http\Livewire\AccStar\Sales\CancelSoDeliveryTax;
use App\Http\Livewire\AccStar\Sales\AdjustSoDeliveryTax;
use App\Http\Livewire\AccStar\Sales\CancelAdjustSoDeliveryTax;
use App\Http\Livewire\AccStar\Sales\ReturnGoods;
use App\Http\Livewire\AccStar\Sales\CancelReturnGoods;
use App\Http\Livewire\AccStar\Sales\SalesHistory;
use App\Http\Livewire\AccStar\Sales\SalesBackOrder;
use App\Http\Livewire\AccStar\Finance\ReceiveOnSales;
use App\Http\Livewire\AccStar\Finance\ReceiveOnSalesService;
use App\Http\Livewire\AccStar\Finance\CancelReceiveOnSales;
use App\Http\Livewire\AccStar\Finance\CancelReceiveOnSalesService;
use App\Http\Livewire\AccStar\Finance\ReceiveHistory;
use App\Http\Livewire\AccStar\Finance\BillingNotice;
use App\Http\Livewire\AccStar\Account\ListGjournal;
use App\Http\Livewire\AccStar\Account\PostJournal;
use App\Http\Livewire\AccStar\Account\CancelPostJournal;
use App\Http\Livewire\AccStar\Account\TrialBalance;
use App\Http\Livewire\AccStar\Account\ListCoa;
use App\Http\Livewire\AccStar\Tax\SalesTax;
use App\Http\Livewire\AccStar\Tax\PurchaseTax;
use App\Http\Livewire\AccStar\Tax\Withholdingtax;

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
Route::get('accstar/customer/customer', Customer::class)->name('accstar.customer.customer');

//======== Inventory ========
Route::get('accstar/inventory/inventory', Inventory::class)->name('accstar.inventory.inventory');
Route::get('accstar/inventory/adjustinventory', AdjustInventory::class)->name('accstar.inventory.adjustinventory');
Route::get('accstar/inventory/inventoryserialno', InventorySerialNo::class)->name('accstar.inventory.inventoryserialno');
Route::get('accstar/inventory/inventorylotno', InventoryLotNo::class)->name('accstar.inventory.inventorylotno');


//======== Sales ========
Route::get('accstar/sales/soservicetax', SoServiceTax::class)->name('accstar.sales.soservicetax');
Route::get('accstar/sales/sodeliverytax', SoDeliveryTax::class)->name('accstar.sales.sodeliverytax');
Route::get('accstar/sales/salesorder', SalesOrder::class)->name('accstar.sales.salesorder');
Route::get('accstar/sales/sodelivery', SoDelivery::class)->name('accstar.sales.sodelivery');
Route::get('accstar/sales/sotax', SoTax::class)->name('accstar.sales.sotax');
Route::get('accstar/sales/cancelsalesorder', CancelSalesOrder::class)->name('accstar.sales.cancelsalesorder');
Route::get('accstar/sales/cancelsodelivery', CancelSoDelivery::class)->name('accstar.sales.cancelsodelivery');
Route::get('accstar/sales/cancelsotax', CancelSoTax::class)->name('accstar.sales.cancelsotax');
Route::get('accstar/sales/cancelsodeliverytax', CancelSoDeliveryTax::class)->name('accstar.sales.cancelsodeliverytax');
Route::get('accstar/sales/adjustsodeliverytax', AdjustSoDeliveryTax::class)->name('accstar.sales.adjustsodeliverytax');
Route::get('accstar/sales/canceladjustsodeliverytax', CancelAdjustSoDeliveryTax::class)->name('accstar.sales.canceladjustsodeliverytax');
Route::get('accstar/sales/returngoods', ReturnGoods::class)->name('accstar.sales.returngoods');
Route::get('accstar/sales/cancelreturngoods', CancelReturnGoods::class)->name('accstar.sales.cancelreturngoods');
Route::get('accstar/sales/saleshistory', SalesHistory::class)->name('accstar.sales.saleshistory');
Route::get('accstar/sales/salesbackorder', SalesBackOrder::class)->name('accstar.sales.salesbackorder');

//======== Finance ========
Route::get('accstar/finance/receiveonsales', ReceiveOnSales::class)->name('accstar.finance.receiveonsales');
Route::get('accstar/finance/receiveonsalesservice', ReceiveOnSalesService::class)->name('accstar.finance.receiveonsalesservice');
Route::get('accstar/finance/cancelreceiveonsales', CancelReceiveOnSales::class)->name('accstar.finance.cancelreceiveonsales');
Route::get('accstar/finance/cancelreceiveonsalesservice', CancelReceiveOnSalesService::class)->name('accstar.finance.cancelreceiveonsalesservice');
Route::get('accstar/finance/billingnotice', BillingNotice::class)->name('accstar.finance.billingnotice');
Route::get('accstar/finance/receivehistory', ReceiveHistory::class)->name('accstar.finance.receivehistory');

//======== Account ========
Route::get('accstar/account/gjournal', ListGjournal::class)->name('accstar.account.gljournal');
Route::get('accstar/account/postjournal', PostJournal::class)->name('accstar.account.postjournal');
Route::get('accstar/account/cancelpostjournal', CancelPostJournal::class)->name('accstar.account.cancelpostjournal');
Route::get('accstar/account/trialbalance', TrialBalance::class)->name('accstar.account.trialbalance');
Route::get('accstar/account/listcoa', ListCoa::class)->name('accstar.account.listcoa');

//======== Tax ========
Route::get('accstar/tax/salestax', SalesTax::class)->name('accstar.tax.salestax');
Route::get('accstar/tax/purchasetax', PurchaseTax::class)->name('accstar.tax.purchasetax');
Route::get('accstar/tax/withholdingtax', Withholdingtax::class)->name('accstar.tax.withholdingtax');

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