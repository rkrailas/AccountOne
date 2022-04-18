<?php

namespace App\Http\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\InventoryLotNumberExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\Collection;

class InventoryLotNo extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "pug.itemid";
    public $numberOfPage = 10;
    public $searchTerm = null;
    public $sDate, $eDate, $haveStock, $isSummary;

    public $totalBeforeVAT, $totalTaxAmount, $totalAmountCur;

    public function clearValues()
    {
        $this->totalBeforeVAT = 0;
        $this->totalTaxamount = 0;
        $this->totalAmountcur = 0;
    }

    public function exportExcel(){
        return Excel::download(new InventoryLotNumberExport($this->searchTerm,$this->sDate,$this->eDate,$this->haveStock), 'LotNumbers.xlsx');
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
        $this->haveStock = true;
    }

    public function render()
    {
        if ($this->isSummary) {
            $strsql = "select pug.itemid, inv.description, pug.lotnumber, '' as ponumber, '' as podate, sum(pug.quantity - pug.sold) as instock
                    from purchasedetaillog pug
                    join inventory inv on pug.itemid=inv.itemid 
                    where inv.stocktype='9'
                    and (pug.itemid ilike '%" . $this->searchTerm . "%' 
                        or inv.description ilike '%" . $this->searchTerm . "%'
                        or pug.lotnumber ilike '%" . $this->searchTerm . "%'
                        or pug.ponumber ilike '%" . $this->searchTerm . "%')";
           
            if ($this->haveStock) {
                $strsql = $strsql . " and (pug.quantity - pug.sold) <> 0";
            };
            
            $strsql = $strsql . " group by pug.itemid, inv.description, pug.lotnumber";
        }else{
            $strsql = "select pug.itemid, inv.description, pug.lotnumber, pug.ponumber, pug.podate, pug.quantity - pug.sold as instock
            from purchasedetaillog pug
            join inventory inv on pug.itemid=inv.itemid 
            where inv.stocktype='9'
            and (pug.itemid ilike '%" . $this->searchTerm . "%' 
                or inv.description ilike '%" . $this->searchTerm . "%'
                or pug.lotnumber ilike '%" . $this->searchTerm . "%'
                or pug.ponumber ilike '%" . $this->searchTerm . "%')";
            
                if ($this->haveStock) {
                $strsql = $strsql . " and (pug.quantity - pug.sold) <> 0";
            };
        }

        $strsql = $strsql . " order by ". $this->sortBy . " " . $this->sortDirection; //where ไม่รองรับฟิลด์ที่เป็น date, boolean
        $lotNumbers = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

    return view('livewire.inventory.inventory-lot-no',[
        'lotNumbers' => $lotNumbers,
    ]);
    }
}
