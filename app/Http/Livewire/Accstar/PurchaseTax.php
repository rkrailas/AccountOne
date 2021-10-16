<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\PurchaseTaxExport;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "taxdata.journaldate";
    public $numberOfPage = 10;
    public $searchTerm = null;
    public $sDate, $eDate, $sendTaxDate;
    public $isInputTax = "All";

    public $totalBeforeVAT, $totalTaxAmount, $totalAmountCur, $sumSelectedVAT;

    public $selectedRows = [];
    public $selectPageRows = false;

    public $currentPageNo, $currentNoOfPage;

    public function saveSendTaxDate()
    {
        DB::table('taxdata')
        ->whereIn('id', $this->selectedRows)
        ->update(['ram_sendtaxdate' => $this->sendTaxDate]);

        $this->dispatchBrowserEvent('updatedSendTaxDate',['message' => 'บันทึกวันที่ยื่นภาษีเรียบร้อยแล้ว']);
        $this->reset(['selectedRows', 'selectPageRows', 'sumSelectedVAT']);
    }

    public function updatedSelectPageRows($value)
    {
        if ($value){
            $this->selectedRows = $this->taxdatas->pluck('id')->map(function ($id) {
                return (string) $id;
            });            
        }else{
            $this->reset(['selectedRows', 'selectPageRows']);
        }

        $this->sumSelectedVAT();
    }

    public function sumSelectedVAT()
    {
        $this->sumSelectedVAT = 0;

        if ($this->selectedRows) {
            $data = DB::table('taxdata')
            ->selectRaw("sum(taxamount) as sumselectedvat")
            ->whereIn('id',$this->selectedRows)
            ->get();
            $data = json_decode(json_encode($data[0]), true);
    
            $this->sumSelectedVAT = $data['sumselectedvat'];
        }
        
    }

    public function updatedSelectedRows()
    {
        $this->sumSelectedVAT();
    }

    public function getTaxdatasProperty()
    {
        return DB::table('taxdata')
        ->select('taxdata.id','taxdata.taxdate','taxdata.journaldate','taxdata.taxnumber','taxdata.reference','customer.name'
        ,'taxdata.taxamount','taxdata.amountcur','customer.taxid','customer.branchno'
        ,'taxdata.gltran','taxdata.description','taxdata.ram_sendtaxdate','taxdata.isinputtax')
        ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',true)
        ->Where('taxdata.iscancelled',false)
        ->where(function($query) {
            if ($this->isInputTax == "1"){
                $query->where('isinputtax',true);
            }else if ($this->isInputTax == "2"){
                $query->where('isinputtax',true)
                      ->where('ram_sendtaxdate', '<>', Null);
            }else if ($this->isInputTax == "3"){
                $query->where('isinputtax',true)
                ->where('ram_sendtaxdate', '=', Null);
            }else if ($this->isInputTax == "4"){
                $query->where('isinputtax',false);
            }
        })
        ->whereBetween('taxdata.journaldate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
            $query->where('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.reference', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.amountcur', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.taxid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.gltran', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.description', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);
    }

    public function clearValues()
    {
        // $this->totalBeforeVAT = 0;
        // $this->totalTaxamount = 0;
        // $this->totalAmountcur = 0;
        $this->reset(['totalBeforeVAT', 'totalTaxAmount', 'totalAmountCur']);
    }

    public function exportExcel(){
        return Excel::download(new PurchaseTaxExport($this->searchTerm,$this->sDate,$this->eDate), 'PurchaseTax.xlsx');
    }

    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;
        if ($this->sortDirection == "asc"){
            $this->sortDirection = "desc";
        }else{
            $this->sortDirection = "asc";
        }
    }

    public function refreshData()
    {
        $this->resetPage();
        $this->clearValues();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
        $this->clearValues();
    }

    public function mount()
    {
        $this->sDate = date_format(Carbon::now()->addMonth(-1),'Y-m-d');
        $this->eDate = date_format(Carbon::now(),'Y-m-d');
        $this->sendTaxDate = date_format(Carbon::now(),'Y-m-d');
    }

    public function render()
    {
        $mytaxdatas = $this->taxdatas;

        //Calulate Summary
        $mySummary = DB::table('taxdata')
        ->selectRaw("sum(taxamount) as totaltaxamount, sum(amountcur) as totalamountcur")
        ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',true)
        ->Where('taxdata.iscancelled',false)
        ->whereBetween('taxdata.journaldate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
            $query->where('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.reference', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.amountcur', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.taxid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.gltran', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.description', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->get();

        $mySummary = json_decode(json_encode($mySummary[0]), true);
        $this->totalTaxAmount = $mySummary['totaltaxamount'];
        $this->totalAmountCur = $mySummary['totalamountcur'];
        $this->totalBeforeVAT = $this->totalAmountCur - $this->totalTaxAmount;

        //If change page then clear selected rows
        if ($this->currentPageNo ) {
            if ($this->currentPageNo != $this->taxdatas->currentPage())  {
                $this->reset(['selectedRows', 'selectPageRows', 'sumSelectedVAT']);
                $this->currentPageNo = $this->taxdatas->currentPage();
            }
        }else{
            $this->currentPageNo = $this->taxdatas->currentPage();
        }

        //If change number of page then clear selected rows
        if ($this->currentNoOfPage ) {
            if ($this->currentNoOfPage != $this->taxdatas->perPage())  {
                $this->reset(['selectedRows', 'selectPageRows', 'sumSelectedVAT']);
                $this->currentNoOfPage = $this->taxdatas->perPage();
            }
        }else{
            $this->currentNoOfPage = $this->taxdatas->perPage();
        }

        return view('livewire.accstar.purchase-tax',[
            'taxdatas' => $mytaxdatas,
        ]);
    }
}
