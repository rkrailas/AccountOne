<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceiveOnSales extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "gjournaldt";
    public $numberOfPage = 10;
    public $searchTerm = null;
    
    public $showEditModal = null;
    public $customers_dd, $taxTypes_dd; //Dropdown
    public $sNumberDelete, $modelMessage;
    public $bankHeader = [];
    public $bankDetails = [];
    public $sumPlus, $sumDeduct = 0;

    public function calPlusDeduct()
    {
        $this->sumPlus = $this->bankHeader['fincharge'];
        $this->sumDeduct = $this->bankHeader['findiscount'] + $this->bankHeader['feeamt'];
    }

    public function updatedBankHeaderFincharge()
    {
        $this->calPlusDeduct();
    }

    public function updatedBankHeaderFindiscount()
    {
        $this->calPlusDeduct();
    }

    public function updatedBankHeaderFeeamt()
    {
        $this->calPlusDeduct();
    }

    public function updatedBankHeaderTaxscheme()
    {
        $this->bankHeader['taxrate'] = 0;
        $data = DB::table('taxtable')
        ->select("taxrate")
        ->where('code', $this->bankHeader['taxscheme'])
        ->get();
        if ($data->count() > 0) {
            $this->bankHeader['taxrate'] = $data[0]->taxrate;
        }

        $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdamt'] * $this->bankHeader['taxrate'] / 100, 2);
    }

    public function updatedBankHeaderTaxscheme1()
    {
        $this->bankHeader['taxrate1'] = 0;
        $data = DB::table('taxtable')
        ->select("taxrate")
        ->where('code', $this->bankHeader['taxscheme1'])
        ->get();
        if ($data->count() > 0) {
            $this->bankHeader['taxrate1'] = $data[0]->taxrate;
        }

        $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdamt1'] * $this->bankHeader['taxrate1'] / 100, 2);
    }

    public function updatedBankHeaderWitholdamt1()
    {
        $this->bankHeader['taxrate1'] = 0;
        $data = DB::table('taxtable')
        ->select("taxrate")
        ->where('code', $this->bankHeader['taxscheme1'])
        ->get();
        if ($data->count() > 0) {
            $this->bankHeader['taxrate1'] = $data[0]->taxrate;
        }

        $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdamt1'] * $this->bankHeader['taxrate1'] / 100, 2);
    }

    public function updatedBankDetails()
    {
        $this->bankHeader['witholdamt']= 0;
        
        for($i=0; $i<count($this->bankDetails);$i++)
        {
            $this->bankHeader['witholdamt'] = $this->bankHeader['witholdamt'] + round($this->bankDetails[$i]['amount'] - 
                                                ($this->bankDetails[$i]['amount'] * $this->bankDetails[$i]['tax'] / $this->bankDetails[$i]['balance']),2);
        }
    }

    public function sortJR($sortby)
    {
        $this->sortBy = $sortby;
        if ($this->sortDirection == "asc"){
            $this->sortDirection = "desc";
        }else{
            $this->sortDirection = "asc";
        }
    }

    public function addNew()
    {
        $this->dispatchBrowserEvent('show-receiveOnSalesForm'); //แสดง Model Form
    }

    public function calculateSummary()
    {
        $sumReceieAmount = 0;

        for($i=0; $i<count($this->bankDetails);$i++)
        {
            $sumReceieAmount = $this->bankDetails[$i]['amount'];
        }

        $this->bankHeader['amount'] = round($sumReceieAmount -  $this->bankHeader['witholdamt'] -  $this->bankHeader['witholdamt1'] 
                                        + $this->sumPlus - $this->sumDeduct , 2);
    }

    public function edit($gltran)
    {
        $this->showEditModal = TRUE;

        // .Clear Value
        $this->sumPlus = 0;
        $this->sumDeduct = 0;

        // .bankHeader
        $data = DB::table('bank')
            ->selectRaw("gltran, to_char(gjournaldt,'YYYY-MM-DD') as gjournaldt, customerid, customername, documentref, amount
                        , taxscheme, witholdamt, witholdtax, witholdtaxrate, taxscheme1, witholdamt1, witholdtax1, witholdtaxrate1
                        , payby, account, accountcus, accounttax, taxrunningno, posted, fincharge, findiscount, feeamt")
            ->where('gltran', $gltran)
            ->get();
        $this->bankHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        $this->bankHeader['amount'] = round($this->bankHeader['amount'],2);
        $this->bankHeader['witholdamt'] = round($this->bankHeader['witholdamt'],2);
        $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdtax'],2);
        $this->bankHeader['witholdtaxrate'] = round($this->bankHeader['witholdtaxrate'],2);
        $this->bankHeader['witholdamt1'] = round($this->bankHeader['witholdamt1'],2);
        $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdtax1'],2);
        $this->bankHeader['witholdtaxrate1'] = round($this->bankHeader['witholdtaxrate1'],2);
        $this->bankHeader['fincharge'] = round($this->bankHeader['fincharge'],2);
        $this->bankHeader['findiscount'] = round($this->bankHeader['findiscount'],2);
        $this->bankHeader['feeamt'] = round($this->bankHeader['feeamt'],2);
        // ./bankHeader

        // .bankDetails
        $data2 = DB::table('bankdetail')
            ->select('taxref', 'description', 'balance', 'amount', 'tax')
            ->where('gltran', $gltran)
            ->get();
        $this->bankDetails = json_decode(json_encode($data2), true); 

        for($i=0; $i<count($this->bankDetails);$i++)
        {
            $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'],2);
            $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'],2);
        }
        // ./bankDetails

        $this->dispatchBrowserEvent('show-receiveOnSalesForm'); //แสดง Model Form
    }

    public function confirmDelete($gltran)
    {

    }

    
    public function render()
    {
        // .Summary grid     
        if($this->bankDetails != Null)
        {            
            $this->calculateSummary();
        }else{
            $this->bankHeader['amount'] = 0;
        }
        // ./Summary grid 

        // .Bind Data to Dropdown
        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();

        $this->taxTypes_dd = DB::table('taxtable')
        ->select('code','description','taxrate')
        ->where('taxtype','2')
        ->orderBy('code')
        ->get();
        // ./Bind Data to Dropdown

        // .ใบสำคัญรับเงินที่ยังไม่ ปิดรายการ
        $recieptJournals = DB::table('bank')
        ->select('gltran','gjournaldt','customername','amount')
        ->where('posted', FALSE)            
        ->where('bookid','R1')
        ->Where(function($query) 
        {
            $query->where('gltran', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('gjournaldt', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('customername', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('amount', 'like', '%'.$this->searchTerm.'%');
        })    
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);
        // /.ใบสำคัญรับเงินที่ยังไม่ ปิดรายการ

        return view('livewire.accstar.receive-on-sales',[
            'recieptJournals' => $recieptJournals
        ]);
    }
}
