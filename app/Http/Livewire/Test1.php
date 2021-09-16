<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test1 extends Component
{
    public $myCustomer = [];

    public function getCustomer() 
    {
        $this->myCustomer = DB::table('customer')
        ->select('customerid','name','phone1')
        ->orderby('customerid')
        ->limit(10000)
        ->get();
        //$this->mycustomer = json_decode(json_encode($this->mycustomer), true);
        //dd($this->mycustomer);
    }

    public function render()
    {
        return view('livewire.test1');
    }
}