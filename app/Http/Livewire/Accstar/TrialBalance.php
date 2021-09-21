<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TrialBalance extends Component
{
    public $sortDirection = "asc";
    public $sortBy = "account";
    public $searchTerm = null;
    public $sumBeginDebit, $sumBeginCredit, $sumCurrentDebit, $sumCurrentCredit;

    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;

        if ($this->sortDirection == "asc"){
            $this->sortDirection = "desc";
        }else{
            $this->sortDirection = "asc";
        }        
    }

    public function render()
    {
        $xWhere = " where detail=true and (account like '%" . $this->searchTerm . "%' or accnameother like '%" . $this->searchTerm . "%')";
        $xOrderBy = " order by " . $this->sortBy . " " . $this->sortDirection;

        $strsql = "select account, accnameother, begindebit, begincredit, currentdebit, currentcredit
                    , case
                        when acctype in ('1','5') and currentdebit > currentcredit THEN currentdebit - currentcredit
                        when acctype in ('2','3','4') and currentdebit > currentcredit THEN currentdebit - currentcredit
                        ELSE 0
                    END as nowdr
                    , case
                        when acctype in ('1','5') and currentcredit > currentdebit THEN currentcredit - currentdebit
                        when acctype in ('2','3','4') and currentcredit > currentdebit THEN currentcredit - currentdebit
                        ELSE 0
                    END as nowcr
                    , currentbal 
                    from account"
                    . $xWhere
                    . $xOrderBy;
        $trialBalance = DB::select($strsql);
        $trialBalance = json_decode(json_encode($trialBalance), true); 

        //Get Summary
        $this->sumBeginDebit = array_sum(array_column($trialBalance, 'begindebit'));
        $this->sumBeginCredit = array_sum(array_column($trialBalance, 'begincredit'));
        $this->sumCurrentDebit = array_sum(array_column($trialBalance, 'currentdebit'));
        $this->sumCurrentCredit = array_sum(array_column($trialBalance, 'currentcredit'));

        return view('livewire.accstar.trial-balance',[
            'trialBalance' => $trialBalance
        ]);
    }
}
