<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test1 extends Component
{
    public $showEditModal = null;
    public $soHeader = []; //sodate,invoiceno,invoicedate,deliveryno,deliverydate,payby,duedate,journaldate,exclusivetax
                           //,taxontotal,salesaccount,taxrate,salestax,discountamount,sototal,customerid,full_address,shipcost
    public $soDetails = []; //itemid,description,quantity,salesac,unitprice,amount,discountamount,netamount,taxrate,taxamount,id,inventoryac
    public $sumQuantity, $sumAmount, $sumDebit, $sumCredit = 0;
    public $itemNos_dd, $taxRates_dd, $salesAcs_dd, $customers_dd; //Dropdown
    public $sNumberDelete, $modelMessage;
    public $genGLs = []; //gltran, gjournaldt, glaccount, glaccname, gldescription, gldebit, glcredit, jobid, department
                        //, allcated, currencyid, posted, bookid, employee_id, transactiondate

    public function confirmDelete($snumber) //แสดง Modal ยืนยันการลบใบสั่งขาย
    {
        $this->modelMessage = "คุณต้องการลบใบสั่งขายเลขที่: " . $snumber;
        $this->sNumberDelete = $snumber;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function render()
    {
        // .Summary grid     
        if($this->soDetails != Null)
        {            
            $this->reCalculateSummary();
        }else{
            $this->sumQuantity = 0;
            $this->sumAmount = 0;
            $this->soHeader['discountamount'] = 0;
            $this->soHeader['salestax'] = 0;
            $this->soHeader['sototal'] = 0;
            $this->soHeader['customerid'] = "";
        }
        // ./Summary grid 
        
        // .Bind Data to Dropdown
        $this->itemNos_dd = DB::table('inventory')
        ->select('itemid','description')
        ->orderby('itemid')
        ->get();

        $this->salesAcs_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail',TRUE)
        ->orderby('account')
        ->get();

        $this->taxRates_dd = DB::table('taxtable')
        ->select('code','taxrate')
        ->where('taxtype','1')
        ->orderby('code')
        ->get();

        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();
        // ./Bind Data to Dropdown

        $salesOrders = DB::table('sales')
            ->select('sales.id','snumber','sodate','name','sototal', 'sales.sodate')
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('posted', FALSE)            
            ->where('soreturn','N')
            ->whereIn('snumber',function ($query) {
                $query->select('snumber')->from('salesdetail')
                ->Where('quantitybac', '>' , 0);
            })        
            ->orderBy('sales.sodate','desc')
            ->get();
        // /.getSalesOrder

        return view('livewire.test1',[
            'salesOrders' => $salesOrders]);
    }
}