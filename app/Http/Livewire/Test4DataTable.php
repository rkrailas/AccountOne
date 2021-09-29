<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Test4DataTable extends Component
{
    public function edit($gltran)
    {
        dd($gltran);
    }

    public function render()
    {
        $customers = DB::table('customer')
        ->select('customer.customerid','customer.name','customer.contact1','customer.phone1'
                ,'customer.taxid','customer.debtor','customer.creditor','customer.corporate')
        ->whereNotNull('customerid')
        ->orderBy('customerid')
        ->get();

        return view('livewire.test4-data-table',[
            'customers' => $customers,
        ]);
    }
}
