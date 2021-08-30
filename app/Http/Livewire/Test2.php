<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test2 extends Component
{
    public $customers_dd;
    public $customer;

    public function updated($item)
    {
        if ($item == "customer") {
            dd($this->customer);
        };
    }

    public function render()
    {
        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();

        return view('livewire.test2');
    }
}
