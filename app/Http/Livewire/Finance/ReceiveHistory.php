<?php

namespace App\Http\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\SalesHistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\Collection;

class ReceiveHistory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "a.transactiondate";
    public $numberOfPage = 10;
    public $searchTerm = null;
    public $sDate, $eDate;
    public $bankHeader, $bankDetails = [];
    public $showEditModal = null;
    public $sumBalance, $sumPlus, $sumDeduct, $sumWitholdTax, $netAmount;

    public function clearValues()
    {
        $this->reset(['bankHeader','bankDetails','sumBalance', 'sumPlus', 'sumDeduct', 'sumWitholdTax', 'netAmount']);
    }

    public function exportExcel(){
        //return Excel::download(new SalesHistoryExport($this->searchTerm,$this->sDate,$this->eDate), 'SalesHistory.xlsx');
    }

    public function edit($gltran)
    {
        $this->showEditModal = TRUE;
        $this->clearValues();

        // bankHeader
        $strsql = "select gltran, to_char(gjournaldt,'YYYY-MM-DD') as gjournaldt
            , customerid || ' : ' || customername as customername, documentref, amount
            , t1.code || ' : ' || t1.description as taxscheme, witholdamt, witholdtax, witholdtaxrate
            , t2.code || ' : ' || t2.description as  taxscheme1, witholdamt1, witholdtax1, witholdtaxrate1
            , payby, account, accountcus, accounttax, taxrunningno, posted, billingno, notes
            , fincharge, findiscount, feeamt, accountcharge, accountdis, accountfee
            from bank bk
            left join taxtable t1 on bk.taxscheme = t1.code and t1.taxtype='2'
            left join taxtable t2 on bk.taxscheme1 = t2.code and t2.taxtype='2'
            where gltran='" . $gltran . "'";
        $data = DB::select($strsql);

        if (count($data) > 0) {
            $this->bankHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ

            $this->bankHeader['amount'] = round($this->bankHeader['amount'], 2);
            $this->bankHeader['witholdamt'] = round($this->bankHeader['witholdamt'], 2);
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdtax'], 2);
            $this->bankHeader['witholdtaxrate'] = round($this->bankHeader['witholdtaxrate'], 2);
            $this->bankHeader['witholdamt1'] = round($this->bankHeader['witholdamt1'], 2);
            $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdtax1'], 2);
            $this->bankHeader['witholdtaxrate1'] = round($this->bankHeader['witholdtaxrate1'], 2);
            $this->bankHeader['fincharge'] = round($this->bankHeader['fincharge'], 2);
            $this->bankHeader['findiscount'] = round($this->bankHeader['findiscount'], 2);
            $this->bankHeader['feeamt'] = round($this->bankHeader['feeamt'], 2);

            $this->sumPlus = $this->bankHeader['fincharge'];
            $this->sumDeduct = $this->bankHeader['findiscount'] + $this->bankHeader['feeamt'];
            $this->sumWitholdTax = $this->bankHeader['witholdtax'] + $this->bankHeader['witholdtax1'];
        }

        // bankDetails
        $strsql = "select bankdetail.id, bankdetail.taxref, bankdetail.description, bankdetail.balance, bankdetail.amount, bankdetail.tax
            , taxdata.amount as oriamount, taxdata.taxamount as oritax, bankdetail.taxdataid
            from bankdetail
            join taxdata on bankdetail.taxdataid = taxdata.id
            where bankdetail.gltran = '" . $gltran . "'";
        $data = DB::select($strsql);

        if (count($data) > 0) {
            $this->bankDetails = json_decode(json_encode($data), true);

            for ($i = 0; $i < count($this->bankDetails); $i++) {
                $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'], 2);
                $this->sumBalance = $this->sumBalance + $this->bankDetails[$i]['balance'];
            }
        }

        $this->netAmount = $this->sumBalance - $this->sumWitholdTax + $this->sumPlus - $this->sumDeduct;

        $this->dispatchBrowserEvent('show-receiveHistoryForm'); //แสดง Model Form
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
        $this->clearValues();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
        $this->clearValues();
    }

    public function mount()
    {
        $this->sDate = date_format(Carbon::now()->addMonth(-3),'Y-m-d');
        $this->eDate = date_format(Carbon::now(),'Y-m-d');
    }

    public function render()
    {
        // $receiveJournals = DB::table('bank')
        // ->selectRaw("bank.gltran, bank.gjournaldt, customer.customerid || ' : ' || customer.name as customer
        //             , sum(bankdetail.balance) as balance, sum(bankdetail.amount) as paidamount
        //             , bank.witholdtax + bank.witholdtax1 as witholdtax
        //             , bank.fincharge - bank.findiscount - bank.feeamt as plus_deduct, bank.transactiondate")
        // ->Join('bankdetail','bank.gltran','=','bankdetail.gltran')
        // ->Join('customer','bank.customerid','=','customer.customerid')
        // ->Where('bank.posted',true)
        // ->whereBetween('bank.gjournaldt',[$this->sDate, $this->eDate])
        // ->Where(function($query) {
        //     $query->where('bank.gltran', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('bank.gjournaldt', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('customer.customerid', 'ilike', '%'.$this->searchTerm.'%')
        //             ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%');
        //     })
        // ->groupBy('bank.gltran', 'bank.gjournaldt', 'customer.customerid', 'customer.name', 'bank.witholdtax', 'bank.witholdtax1'
        //             ,'bank.fincharge', 'bank.findiscount', 'bank.feeamt', 'bank.transactiondate')
        // ->orderBy($this->sortBy,$this->sortDirection)
        // ->paginate($this->numberOfPage);

        //--
        $strsql = "SELECT a.gltran, a.gjournaldt, c.customerid || ' : ' || c.name as customer, sum(b.balance) as balance, sum(b.amount) as paidamount
                , a.witholdtax + a.witholdtax1 as witholdtax, a.fincharge - a.findiscount - a.feeamt as plus_deduct, a.transactiondate
            FROM bank a
            JOIN bankdetail b ON  a.gltran=b.gltran
            JOIN customer c ON a.customerid=c.customerid
            WHERE a.posted=true AND a.gjournaldt BETWEEN '" . $this->sDate . "' AND '" . $this->eDate . "'
                AND (a.gltran ILIKE '%" . $this->searchTerm . "%'
                    OR c.customerid ILIKE '%" . $this->searchTerm . "%'
                    OR c.name ILIKE '%" . $this->searchTerm . "%')
            GROUP BY(a.gltran, a.gjournaldt, c.customerid, c.name, a.witholdtax, a.witholdtax1,a.fincharge
                    , a.findiscount, a.feeamt, a.transactiondate)
            ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $receiveJournals = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);
        
        return view('livewire.finance.receive-history',[
            'receiveJournals' => $receiveJournals,
        ]);
    }
}
