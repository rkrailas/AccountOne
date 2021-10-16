<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class WithholdingTaxExport implements FromCollection, WithHeadings
{
    protected $sDate, $eDate;

    public function __construct($sDate, $eDate, $whType)
    {
        $this->sDate = $sDate;
        $this->eDate = $eDate;
        $this->whType = $whType;
    }

    public function headings(): array
    {
        return [
            'ผู้มีรายได้', 'ที่อยู่', 'เลขที่ประจำตัว', 'วัน/เดือน/ปี', 'ประเภทเงินได้', 'อัตราภาษี', 'เงินที่จ่าย', 'ภาษี', 'เงื่อนไข'
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if ($this->whType == "3") {
            $corporate = " and c.corporate=false ";
        }else{
            $corporate = " and c.corporate=true ";
        }

        $strsql = "select trim(c.name) as custname,
            CONCAT(trim(c.address11),' ',trim(c.address12),' ',trim(c.city1) ,' ',trim(c.state1),' ',trim(c.zipcode1)) as address,
            trim(c.taxid) as taxid,
            a.gjournaldt as gjournaldt,
            b.description as description,
            a.witholdtaxrate as witholdtaxrate,
            a.gldebit as witholdamt,
            a.witholdtax as witholdtax,
            c.whpayby as whpayby
            from glcash a
            left join (select code, description, taxrate from taxtable) b on a.witholdscheme = b.code
            left join customer c on c.customerid = a.customerid
            where a.witholdtax <> 0"
            . $corporate . 
            "and a.gjournaldt between '" . $this->sDate . "' and '" . $this->eDate . " 23:59'

            union all
            
            select trim(c.name) as custname,
            CONCAT(trim(c.address11),' ',trim(c.address12),' ',trim(c.city1) ,' ',trim(c.state1),' ',trim(c.zipcode1)) as address,
            c.taxid as taxid,            
            a.gjournaldt as gjournaldt,
            b.description as description,
            a.witholdtaxrate as witholdtaxrate,
            a.witholdamt as witholdamt,
            a.witholdtax as witholdtax, 
            c.whpayby as whpayby
            from bank a
            left join (select code, description, taxrate from taxtable) b on a.taxscheme = b.code
            left join customer c on c.customerid = a.customerid
            where a.ReturnCheck = FALSE
            AND a.IsCancelled = FALSE"
            . $corporate . 
            "AND (a.BookID <> 'R1' AND a.BookID <> 'R3' AND a.BookID <> 'RD' AND a.BookID <> 'RV')
            AND a.WitholdTax <> 0
            and a.gjournaldt between '" . $this->sDate . "' and '" . $this->eDate . " 23:59'

            union all

            select trim(c.name) as custname,
            CONCAT(trim(c.address11),' ',trim(c.address12),' ',trim(c.city1) ,' ',trim(c.state1),' ',trim(c.zipcode1)) as address,
            c.taxid as taxid,
            a.gjournaldt as gjournaldt,
            b.description as description,
            a.witholdtaxrate1 as witholdtaxrate,
            a.witholdamt1 as witholdamt,
            a.witholdtax1 as witholdtax,
            c.whpayby as whpayby
            from bank a
            left join (select code, description, taxrate from taxtable) b on a.taxscheme1 = b.code
            left join customer c on c.customerid = a.customerid
            where a.ReturnCheck = FALSE
            AND a.IsCancelled = FALSE"
            . $corporate . 
            "AND (a.BookID <> 'R1' AND a.BookID <> 'R3' AND a.BookID <> 'RD' AND a.BookID <> 'RV')
            AND a.WitholdTax1 <> 0
            and a.gjournaldt between '" . $this->sDate . "' and '" . $this->eDate . " 23:59'";

        $withholdingtaxs = collect(DB::select($strsql));

        return $withholdingtaxs;
    }
}
