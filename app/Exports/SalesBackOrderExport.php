<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SalesBackOrderExport implements FromCollection, WithHeadings
{
    protected $searchTerm, $sDate, $eDate;

    public function __construct($searchTerm, $sDate, $eDate)
    {
        $this->searchTerm = $searchTerm;
        $this->sDate = $sDate;
        $this->eDate = $eDate;
    }

    public function headings(): array
    {
        return [
            '#SO Number', 'Date', 'Item', 'Quantity Order', 'Quantity Backorder', 'Amount', 'Customer'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $salesbackorder = DB::table('salesdetail')
        ->selectRaw("sales.sonumber, sales.sodate, inventory.itemid || ' : ' || inventory.description as item
                    , salesdetail.quantityord, salesdetail.quantitybac, salesdetail.amount
                    ,customer.customerid || ' : ' || customer.name as customer")
        ->Join('sales','salesdetail.snumber','=','sales.sonumber')
        ->Join('customer','sales.customerid','=','customer.customerid')
        ->Join('inventory','salesdetail.itemid','=','inventory.itemid')
        ->Where('sales.soreturn','N')
        ->Where('salesdetail.quantitybac','>',0)
        ->whereBetween('sales.sodate',[$this->sDate, $this->eDate])
	    ->get();
        return $salesbackorder;
    }
}
