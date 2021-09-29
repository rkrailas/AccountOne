<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test5 extends Component
{
    public $sumBeginDebit, $sumBeginCredit, $sumCurrentDebit, $sumCurrentCredit;
    
    public function render()
    {
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
                    from account";

        $trialBalance = DB::select($strsql);
        $trialBalance = json_decode(json_encode($trialBalance), true); 

        //Get Summary
        $this->sumBeginDebit = array_sum(array_column($trialBalance, 'begindebit'));
        $this->sumBeginCredit = array_sum(array_column($trialBalance, 'begincredit'));
        $this->sumCurrentDebit = array_sum(array_column($trialBalance, 'currentdebit'));
        $this->sumCurrentCredit = array_sum(array_column($trialBalance, 'currentcredit'));

        return view('livewire.test5',[
            'trialBalance' => $trialBalance
        ]);
    }
}
