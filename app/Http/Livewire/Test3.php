<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test3 extends Component
{
    public $myOption = "Option4", $myOption2 = "";
    public $account_dd;
    public $account_code = [], $myWhere;
    public $soDetails;
    public $itemNos_dd;

    public function updatedSoDetails()
    {
        dd('Here');
    }

    public function display()
    {
        // soDetails
        $data2 = DB::table('salesdetail')
            ->select('salesdetail.itemid','salesdetail.description','salesdetail.quantity','salesdetail.salesac','salesdetail.unitprice'
                    ,'salesdetail.discountamount','salesdetail.taxrate','salesdetail.taxamount','salesdetail.id','salesdetail.inventoryac'
                    ,'inventory.stocktype','salesdetail.serialno')
            ->join('inventory', 'salesdetail.itemid', '=', 'inventory.itemid')
            ->where('snumber', 'SO2111-000010')
            ->where('soreturn', 'N')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true); 

        $this->dispatchBrowserEvent('show-myModal');
    }

    public function clearValue()
    {
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function mount()
    {
        $this->soDetails = [];
    }

    public function getAccount()
    {
        //dd($this->account_code);
        $this->reset(['myWhere']);

        if ($this->account_code) {            
            $xCondition = "";
            foreach ($this->account_code as $index => $row)
            {
                $xCondition = $xCondition . "'" . $row . "'";

                if ($index < count($this->account_code) - 1){
                    $xCondition = $xCondition . ",";
                }
            }
            $this->myWhere = " WHERE account in (" . $xCondition . ")";
        }
    }

    public function render()
    {
        $this->account_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail',TRUE)
        ->orderby('account')
        ->get();

        $this->itemNos_dd = DB::table('inventory')
        ->select('itemid','description')
        ->orderby('itemid')
        ->get();

        $strsql = "select account, accname from account" . $this->myWhere;
        $listAccount = DB::select($strsql);

        return view('livewire.test3',[
            'listAccount' => $listAccount,
        ]);
    }
}
