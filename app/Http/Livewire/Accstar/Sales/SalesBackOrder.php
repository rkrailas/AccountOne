<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\SalesBackOrderExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesBackOrder extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "sales.sodate";
    public $numberOfPage = 10;
    public $searchTerm = null;
    public $sDate, $eDate;

    public function exportExcel(){
        return Excel::download(new SalesBackOrderExport($this->searchTerm,$this->sDate,$this->eDate), 'SalesBackOrder.xlsx');
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
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->sDate = date_format(Carbon::now()->addMonth(-3),'Y-m-d');
        $this->eDate = date_format(Carbon::now(),'Y-m-d');
    }

    public function render()
    {
        $salesBackOrders = DB::table('salesdetail')
        ->selectRaw("sales.sonumber, sales.sodate, inventory.itemid || ' : ' || inventory.description as item
                    , salesdetail.quantityord, salesdetail.quantitybac, salesdetail.amount
                    , customer.customerid || ' : ' || customer.name as customer")
        ->Join('sales','salesdetail.snumber','=','sales.sonumber')
        ->Join('customer','sales.customerid','=','customer.customerid')
        ->Join('inventory','salesdetail.itemid','=','inventory.itemid')
        ->Where('sales.soreturn','N')
        ->Where('salesdetail.quantitybac','>',0)
        ->whereBetween('sales.sodate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
                        $query->where('sales.sonumber', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('sales.sodate', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('customer.customerid', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('salesdetail.quantityord', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('salesdetail.quantitybac', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('inventory.itemid', 'ilike', '%'.$this->searchTerm.'%')
                                ->orWhere('inventory.description', 'ilike', '%'.$this->searchTerm.'%');
                            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);
        $this->resetPage();
        
        return view('livewire.accstar.sales.sales-back-order',[
            'salesBackOrders' => $salesBackOrders,
        ]);
    }
}
