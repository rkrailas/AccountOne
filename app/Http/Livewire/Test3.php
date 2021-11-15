<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test3 extends Component
{
    public $myOption = "Option4", $myOption2 = "";
    public $account_dd;
    public $myaccount;

    public function display()
    {
        $this->dispatchBrowserEvent('addval-select2', [
            'myval' => '',
            ]);
    }

    public function clearValue()
    {
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function render()
    {
        $this->account_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail',TRUE)
        ->orderby('account')
        ->get();

        $strsql = "select account from buyer where customerid='VP0290'";
        $data = DB::select($strsql);
        $this->myaccount = $data[0]->account;

        return view('livewire.test3');
    }
}
