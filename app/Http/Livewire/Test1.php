<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test1 extends Component
{
    public $customers_dd;

    public function modelTest()
    {
        $this->dispatchBrowserEvent('show-modaltest555');
    }

    public function render()
    {
        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();

        return view('livewire.test1');
    }
}