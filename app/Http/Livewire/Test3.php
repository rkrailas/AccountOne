<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test3 extends Component
{
    public $myOption = "Option4", $myOption2 = "";

    public function display()
    {
        dd($this->myOption);
    }

    public function clearValue()
    {
        $this->dispatchBrowserEvent('clear-select2');
    }
    
    public function render()
    {
        return view('livewire.test3');
    }
}
