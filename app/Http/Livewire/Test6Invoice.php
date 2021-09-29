<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Test6Invoice extends Component
{
    public function print()
    {
        $this->dispatchBrowserEvent('print-invoice');
    }

    public function render()
    {
        return view('livewire.test6-invoice');
    }
}
