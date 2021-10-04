<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class InventorysExport implements FromCollection, WithHeadings
{
    protected $searchTerm;
    
    public function __construct($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }
	
    public function headings(): array
    {
        return [
            '#ID', 'Name', 'Description', 'Stocktype', 'Category', 'Instock', 'Sale Price'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
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
        ->get();

        return $inventorys;
    }
}
