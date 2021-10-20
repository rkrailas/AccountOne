<?php

namespace App\Http\Livewire\Accstar\Tax;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\SalesTaxExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "taxdata.journaldate";
    public $numberOfPage = 10;
    public $searchTerm = null;
    public $sDate, $eDate;

    public $totalBeforeVAT, $totalTaxAmount, $totalAmountCur;

    public function clearValues()
    {
        $this->totalBeforeVAT = 0;
        $this->totalTaxamount = 0;
        $this->totalAmountcur = 0;
    }

    public function exportExcel(){
        return Excel::download(new SalesTaxExport($this->searchTerm,$this->sDate,$this->eDate), 'SalesTax.xlsx');
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
    }

    public function render()
    {
        $taxdatas = DB::table('taxdata')
        ->select('taxdata.id','taxdata.taxdate','taxdata.journaldate','taxdata.taxnumber','taxdata.reference','customer.name'
        ,'taxdata.taxamount','taxdata.amountcur','customer.taxid','customer.branchno'
        ,'taxdata.gltran','taxdata.description')
        ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',false)
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
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        //Cal Summary
        $mySummary = DB::table('taxdata')
        ->selectRaw("sum(taxamount) as totaltaxamount, sum(amountcur) as totalamountcur")
        ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',false)
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
        //dd($mySummary);
        $this->totalTaxAmount = $mySummary['totaltaxamount'];
        $this->totalAmountCur = $mySummary['totalamountcur'];
        $this->totalBeforeVAT = $this->totalAmountCur - $this->totalTaxAmount;

        //$totalBeforeVAT, $totalTaxAmount, $totalAmountCur;

        return view('livewire.accstar.tax.sales-tax',[
            'taxdatas' => $taxdatas,
        ]);
    }
}
