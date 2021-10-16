<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class PurchaseTaxExport implements FromCollection, WithHeadings
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
            'วันที่ใบกำกับ', 'วันที่บันทึกบัญชี', 'เลขที่ใบกำกับ', 'เลขที่อ้างอิง', 'ผู้ขาย', 'ยอดก่อน VAT', 'VAT', 'ยอดรวม', 'เลขที่ใบสำคัญ', 'คำอธิบาย', 'เลขผู้เสียภาษี', 'สาขา'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $taxdatas = DB::table('taxdata')
        ->selectRaw("taxdate,journaldate,taxnumber,reference,customer.name,amountcur-taxamount as beforvat,taxamount,amountcur
                ,gltran,description,customer.taxid,customer.branchno")
        ->leftJoin('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',true)
        ->Where('taxdata.iscancelled',false)
        ->whereBetween('taxdata.journaldate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
            $query->where('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.reference', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.amountcur', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.taxid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.gltran', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.description', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->get();

        return $taxdatas;
    }
}

