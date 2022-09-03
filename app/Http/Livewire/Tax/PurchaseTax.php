<?php

namespace App\Http\Livewire\Tax;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\PurchaseTaxExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\Collection;

class PurchaseTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "a.journaldate";
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
        $strsql = "SELECT a.id,a.taxdate,a.journaldate,a.taxnumber,a.reference,b.name,a.taxamount,a.amountcur,b.taxid,b.branchno
                ,a.gltran,a.description,a.ram_sendtaxdate,a.isinputtax
                FROM taxdata a
                LEFT JOIN customer b ON a.customerid=b.customerid
                WHERE a.purchase=true AND a.iscancelled=false 
                    AND a.journaldate BETWEEN '" . $this->sDate . "' AND '" . $this->eDate . "'";

        if ($this->isInputTax == "1"){
            $strsql = $strsql . "AND a.isinputtax = true";
        }else if ($this->isInputTax == "2"){
            $strsql = $strsql . "AND a.isinputtax = true AND a.ram_sendtaxdate <> null";
        }else if ($this->isInputTax == "3"){
            $strsql = $strsql . "AND a.isinputtax = true AND a.ram_sendtaxdate = null";
        }else if ($this->isInputTax == "4"){
            $strsql = $strsql . "AND a.isinputtax = false";
        }

        $sqlstr = $strsql . "AND (a.taxnumber ILIKE '%" . $this->searchTerm . "%'
                OR a.reference ILIKE '%" . $this->searchTerm . "%'
                OR CAST(a.taxamount AS TEXT) ILIKE '%" . $this->searchTerm . "%'
                OR CAST(a.amountcur AS TEXT) ILIKE '%" . $this->searchTerm . "%'
                OR b.taxid ILIKE '%" . $this->searchTerm . "%'
                OR b.name ILIKE '%" . $this->searchTerm . "%'
                OR a.gltran ILIKE '%" . $this->searchTerm . "%'
                OR a.description ILIKE '%" . $this->searchTerm . "%')
        ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $taxdatas = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return $taxdatas;
    }

    // public function getTaxdatasProperty_Notuse()
    // {
    //     return DB::table('taxdata')
    //     ->select('taxdata.id','taxdata.taxdate','taxdata.journaldate','taxdata.taxnumber','taxdata.reference','customer.name'
    //     ,'taxdata.taxamount','taxdata.amountcur','customer.taxid','customer.branchno'
    //     ,'taxdata.gltran','taxdata.description','taxdata.ram_sendtaxdate','taxdata.isinputtax')
    //     ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
    //     // ??? 3-9-22 รอแก้ให้เป็น sqlstring 
    //     // ->Where('taxdata.purchase',true)
    //     // ->Where('taxdata.iscancelled',false)
    //     ->where(function($query) {
    //         if ($this->isInputTax == "1"){
    //             $query->where('isinputtax',true);
    //         }else if ($this->isInputTax == "2"){
    //             $query->where('isinputtax',true)
    //                   ->where('ram_sendtaxdate', '<>', Null);
    //         }else if ($this->isInputTax == "3"){
    //             $query->where('isinputtax',true)
    //             ->where('ram_sendtaxdate', '=', Null);
    //         }else if ($this->isInputTax == "4"){
    //             $query->where('isinputtax',false);
    //         }
    //     })
    //     ->whereBetween('taxdata.journaldate',[$this->sDate, $this->eDate])
    //     ->Where(function($query) {
    //         $query->where('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('taxdata.reference', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('taxdata.amountcur', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('customer.taxid', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('taxdata.gltran', 'ilike', '%'.$this->searchTerm.'%')
    //                 ->orWhere('taxdata.description', 'ilike', '%'.$this->searchTerm.'%');
    //         })
    //     ->orderBy($this->sortBy,$this->sortDirection)
    //     ->paginate($this->numberOfPage);
    // }

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

        //Calulate Summary Ori Notuse
        // $mySummary = DB::table('taxdata')
        // ->selectRaw("sum(taxamount) as totaltaxamount, sum(amountcur) as totalamountcur")
        // ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
        // ->Where('taxdata.purchase',true)
        // ->Where('taxdata.iscancelled',false)
        // ->whereBetween('taxdata.journaldate',[$this->sDate, $this->eDate])
        // ->Where(function($query) {
        //     $query->where('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('taxdata.reference', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('taxdata.amountcur', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('customer.taxid', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('taxdata.gltran', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('taxdata.description', 'ilike', '%'.$this->searchTerm.'%');
        //     })
        // ->get();

        // $mySummary = json_decode(json_encode($mySummary[0]), true);
        // $this->totalTaxAmount = $mySummary['totaltaxamount'];
        // $this->totalAmountCur = $mySummary['totalamountcur'];
        // $this->totalBeforeVAT = $this->totalAmountCur - $this->totalTaxAmount;

        //Calulate Summary New
        $strsql = "SELECT sum(a.taxamount) as totaltaxamount, sum(a.amountcur) as totalamountcur
        FROM taxdata a
        LEFT JOIN customer b ON a.customerid=b.customerid
        WHERE a.purchase=true AND a.iscancelled=false AND a.journaldate BETWEEN '" . $this->sDate . "' AND '" . $this->eDate . "'
            AND (a.taxnumber ILIKE '%" . $this->searchTerm . "%'
                OR a.reference ILIKE '%" . $this->searchTerm . "%'
                OR CAST(a.taxamount AS TEXT) ILIKE '%" . $this->searchTerm . "%'
                OR CAST(a.amountcur AS TEXT) ILIKE '%" . $this->searchTerm . "%'
                OR b.taxid ILIKE '%" . $this->searchTerm . "%'
                OR b.name ILIKE '%" . $this->searchTerm . "%'
                OR a.gltran ILIKE '%" . $this->searchTerm . "%'
                OR a.description ILIKE '%" . $this->searchTerm . "%')";

        $mySummary = DB::select($strsql);

        //$mySummary = json_decode(json_encode($mySummary[0]), true);
        $this->totalTaxAmount = $mySummary[0]->totaltaxamount;
        $this->totalAmountCur = $mySummary[0]->totalamountcur;
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

        return view('livewire.tax.purchase-tax',[
            'taxdatas' => $mytaxdatas,
        ]);
    }
}
