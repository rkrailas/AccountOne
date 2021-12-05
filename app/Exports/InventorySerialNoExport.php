<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;


class InventorySerialNoExport implements FromCollection, WithHeadings
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
	            'รหัสสินค้า', 'รายละเอียด', 'Serial No.', 'เลขที่ใบสั่งซื้อ', 'วันที่สั่งซื้อ', 'ขาย', 'เลขที่ใบสั่งขาย'
	        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $invsn = DB::table('inventoryserial as invsn')
        ->selectRaw('invsn.itemid, inv.description, invsn.serialno, invsn.ponumber, invsn.orderdate, invsn.sold
                ,case when invsn.sold then invsn.snumber
                else null end as snumber
                ,case when invsn.sold then invsn.solddate
                else null end as solddate')
        ->leftJoin('inventory as inv','invsn.itemid','=','inv.itemid')
        ->whereBetween('invsn.orderdate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
            $query->where('invsn.itemid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inv.description', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('invsn.serialno', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('invsn.ponumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('invsn.orderdate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('invsn.sold', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('invsn.snumber', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->get();

        return $invsn;
    }
}
