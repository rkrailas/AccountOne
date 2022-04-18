<?php

namespace App\Http\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\InventorySerialNoExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\Collection;

class InventorySerialNo extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "ins.itemid";
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
        return Excel::download(new InventorySerialNoExport($this->searchTerm,$this->sDate,$this->eDate), 'SerialNo.xlsx');
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
        $strsql = "select inv.itemid, inv.description, ins.serialno, ins.ponumber, ins.orderdate, ins.snumber, ins.solddate
                , res.snumber as reserveno, ins.sold
                from inventoryserial ins
                join inventory inv on ins.itemid=inv.itemid
                left join (select a.snumber,a.soreturn,b.itemid,b.serialno
                            from sales a
                            join salesdetail b on a.snumber=b.snumber
                            left join taxdata c on a.customerid=c.customerid and a.snumber=c.reference and c.purchase=false and c.iscancelled=false 
                            where a.soreturn='N' 
                            and c.reference is null
                        ) res on ins.itemid=res.itemid and ins.serialno=res.serialno
                where ins.itemid ilike '%" . $this->searchTerm . "%' 
                    or inv.description ilike '%" . $this->searchTerm . "%'
                    or ins.serialno ilike '%" . $this->searchTerm . "%'
                    or ins.ponumber ilike '%" . $this->searchTerm . "%'
                    or ins.snumber ilike '%" . $this->searchTerm . "%'
                    or res.snumber ilike '%" . $this->searchTerm . "%'
                order by ". $this->sortBy . " " . $this->sortDirection; //where ไม่รองรับฟิลด์ที่เป็น date, boolean
        $itemSN = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

    return view('livewire.inventory.inventory-serial-no',[
        'itemSN' => $itemSN,
    ]);
    }
}
