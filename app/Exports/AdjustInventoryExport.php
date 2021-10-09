<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;


class AdjustInventoryExport implements FromCollection, WithHeadings
{
    protected $searchTerm;

    public function __construct($searchTerm)
    {
        $this->searchTerm = $searchTerm;
    }
    
    public function headings(): array
    {
        return [
            'Document No.','Adjust IN', 'Item ID', 'Description', 'Adjust Quantity', 'Cost/Unit', 'Location', 'Employee', 'Transaction Date'
        ];
    }


    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $adjlogs = DB::table('inventoryadjlog')
        ->select('inventoryadjlog.documentno','inventoryadjlog.isadjustin','inventoryadjlog.itemid','inventory.description as description'
                ,'inventoryadjlog.adjquantity','inventoryadjlog.adjvalue','misctable.other as location'
                ,'employee.name as employee','inventoryadjlog.transactiondate')
        ->leftJoin('misctable', function ($join) {
            $join->on('inventoryadjlog.location', '=', 'misctable.code')
                 ->where('misctable.tabletype', 'LO');
                }) 
        ->leftJoin('inventory', 'inventoryadjlog.itemid', '=', 'inventory.itemid')
        ->leftJoin('employee', 'inventoryadjlog.employee_id', '=', 'employee.employeeid')
        ->Where(function($query) {
            $query->where('inventoryadjlog.documentno', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventoryadjlog.itemid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventory.description', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('misctable.other', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventoryadjlog.transactiondate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('employee.name', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->get();
        
        return $adjlogs;
    }
}
