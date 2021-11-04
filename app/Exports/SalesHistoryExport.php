<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class SalesHistoryExport implements FromCollection, WithHeadings
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
            'ใบสั่งขาย', 'วันที่', 'ผู้ซื้อ', 'ใบกำกับ', 'วันที่', 'บันทึก', 'ยอดเงิน', 'ภาษี'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $salesOrders = DB::table('taxdata')
        ->selectRaw("sales.sonumber, sales.sodate, customer.customerid || ': ' || customer.name as customername
                    , taxdata.taxnumber, taxdata.taxdate, sales.sonote, taxdata.amount, taxdata.taxamount")
        ->Join('sales','taxdata.reference','=','sales.sonumber')
        ->Join('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',false)
        ->Where('taxdata.iscancelled',false)
        ->whereBetween('sales.sodate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
            $query->where('sales.sonumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('sales.sodate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.customerid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxdate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('sales.sonote', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.amount', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%');
            })
	    ->get();
        return $salesOrders;
    }
}
