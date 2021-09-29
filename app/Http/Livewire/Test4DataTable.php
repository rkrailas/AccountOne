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
         $recieptJournals = DB::table('bank')
         ->select('gltran','gjournaldt','customername','amount')
         ->where('posted', FALSE)            
         ->where('bookid','R1')
         ->orderBy('gltran')
         ->get();

        return view('livewire.test4-data-table',[
            'recieptJournals' => $recieptJournals,
        ]);
    }
}
