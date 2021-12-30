<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class InventoryLotNumberExport implements FromCollection, WithHeadings
{
    protected $searchTerm, $sDate, $eDate;

    public function __construct($searchTerm, $sDate, $eDate, $haveStock)
    {
        $this->searchTerm = $searchTerm;
        $this->sDate = $sDate;
        $this->eDate = $eDate;
        $this->haveStock = $haveStock;
    }

    public function headings(): array
    {
        return [
            'รหัสสินค้า', 'รายละเอียด', 'Lot Number', 'ใบสั่งซื้อ', 'วันที่สั่งซื้อ', 'จำนวนคงเหลือ'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $strsql = "select pug.itemid, inv.description, pug.lotnumber, pug.ponumber, pug.podate, pug.quantity - pug.sold as instock
                from purchasedetaillog pug
                join inventory inv on pug.itemid=inv.itemid 
                where inv.stocktype='9'
                and (pug.itemid ilike '%" . $this->searchTerm . "%' 
                    or inv.description ilike '%" . $this->searchTerm . "%'
                    or pug.lotnumber ilike '%" . $this->searchTerm . "%'
                    or pug.ponumber ilike '%" . $this->searchTerm . "%'
                    )";
        if ($this->haveStock) {
            $strsql = $strsql . " and pug.quantity - pug.sold <> 0";
        };
        $data = collect(DB::select($strsql));

        return $data;
    }
}
