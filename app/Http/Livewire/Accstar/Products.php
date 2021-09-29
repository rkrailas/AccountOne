<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Products extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "inventory.itemid";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $showEditModal;

    public function addNew()
    {

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
        $products = DB::table('inventory')
        ->select('inventory.id','inventory.itemid','inventory.description','b.other as stocktype'
                ,'c.other as category','inventory.instock')
        ->leftJoin('misctable as b', function ($join) {
            $join->on('inventory.stocktype', '=', 'b.code')
                 ->where('b.tabletype', 'CA');
                }) 
        ->leftJoin('misctable as c', function ($join) {
            $join->on('inventory.category', '=', 'c.code')
                    ->where('c.tabletype', 'I1');
                }) 
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.accstar.products',[
            'products' => $products,
        ]);
    }
}
