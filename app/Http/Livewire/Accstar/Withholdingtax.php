<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\WithholdingTaxExport;
use Maatwebsite\Excel\Facades\Excel;

class Withholdingtax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "taxdata.journaldate";
    public $sDate, $eDate;
    public $whType;

    public $totalWitholdAmt, $totalWitholdTax;

    public function exportExcel(){
        return Excel::download(new WithholdingTaxExport($this->sDate,$this->eDate,$this->whType,), 'WithholdingTax.xlsx');
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

    public function refreshData()
    {
        $this->resetPage();
        $this->reset(['totalWitholdAmt', 'totalWitholdTax']);
    }

    public function mount()
    {
        $this->sDate = date_format(Carbon::now()->addMonth(-1),'Y-m-d');
        $this->eDate = date_format(Carbon::now(),'Y-m-d');
        $this->whType = "3";
    }

    public function render()
    {
        if ($this->whType == "3") {
            $corporate = " and c.corporate=false ";
        }else{
            $corporate = " and c.corporate=true ";
        }

        $strsql = "select trim(c.name) as custname,
            CONCAT(trim(c.address11),' ',trim(c.address12),' ',trim(c.city1) ,' ',trim(c.state1),' ',trim(c.zipcode1)) as address,
            trim(c.taxid) as taxid,
            c.corporate as corporate,
            c.whpayby as whpayby,
            a.gjournaldt as gjournaldt,
            b.description as description,
            a.witholdtaxrate as witholdtaxrate,
            a.witholdtax as witholdtax,
            a.gldebit as witholdamt
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
            c.corporate as corporate,
            c.whpayby as whpayby,
            a.gjournaldt as gjournaldt,
            b.description as description,
            a.witholdtaxrate as witholdtaxrate,
            a.witholdtax as witholdtax,
            a.witholdamt as witholdamt
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
            c.corporate as corporate,
            c.whpayby as whpayby,
            a.gjournaldt as gjournaldt,
            b.description as description,
            a.witholdtaxrate1 as witholdtaxrate,
            a.witholdtax1 as witholdtax,
            a.witholdamt1 as witholdamt
            from bank a
            left join (select code, description, taxrate from taxtable) b on a.taxscheme1 = b.code
            left join customer c on c.customerid = a.customerid
            where a.ReturnCheck = FALSE
            AND a.IsCancelled = FALSE"
            . $corporate . 
            "AND (a.BookID <> 'R1' AND a.BookID <> 'R3' AND a.BookID <> 'RD' AND a.BookID <> 'RV')
            AND a.WitholdTax1 <> 0
            and a.gjournaldt between '" . $this->sDate . "' and '" . $this->eDate . " 23:59'";

        $withholdingtaxs = DB::select($strsql);

        $withholdingtaxs2 = json_decode(json_encode($withholdingtaxs), true); 

        //Calulate Summary
        for($i=0; $i<count($withholdingtaxs2); $i++)
        {
            $this->totalWitholdAmt = $this->totalWitholdAmt  + $withholdingtaxs2[$i]['witholdamt'];
            $this->totalWitholdTax = $this->totalWitholdTax  + $withholdingtaxs2[$i]['witholdtax'];
        }
        

        return view('livewire.accstar.withholdingtax',[
            'withholdingtaxs' => $withholdingtaxs,
        ]);
    }
}