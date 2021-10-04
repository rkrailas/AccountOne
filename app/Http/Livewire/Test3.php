<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test3 extends Component
{
    public $myOption, $myOption2 = "";

    public function display()
    {
        dd($this->myOption . " | " . $this->myOption2);
    }

    public function clearValue()
    {
        $this->myOption = "";
    }
    
    public function render()
    {
        return view('livewire.test3');
    }
}
