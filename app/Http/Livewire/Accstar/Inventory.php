<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\InventorysExport;
use Maatwebsite\Excel\Facades\Excel;

class Inventory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "inventory.itemid";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public function exportExcel(){
        return Excel::download(new InventorysExport($this->searchTerm), 'Inventorys.xlsx');
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

    public function render()
    {
        $inventorys = DB::table('inventory')
        ->select('inventory.id','inventory.itemid','inventory.description','b.other as stocktype'
                ,'c.other as category','inventory.instock','inventory.salesprice')
        ->leftJoin('misctable as b', function ($join) {
            $join->on('inventory.stocktype', '=', 'b.code')
                 ->where('b.tabletype', 'I1');
                }) 
        ->leftJoin('misctable as c', function ($join) {
            $join->on('inventory.category', '=', 'c.code')
                    ->where('c.tabletype', 'CA');
                })
        ->Where(function($query) {
            $query->where('inventory.itemid', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('inventory.description', 'like', '%'.$this->searchTerm.'%');
            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.accstar.inventory',[
            'inventorys' => $inventorys,
        ]);
    }
}
