<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test5 extends Component
{
    public $sumBeginDebit, $sumBeginCredit, $sumCurrentDebit, $sumCurrentCredit;
    
    public function render()
    {
        return view('livewire.test5');
    }
}
