<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Client;

class Products extends Component
{
    public $orderProducts = [];
    public $allProducts = [];

    public function mount()
    {
        // $this->allProducts = DB::table('clients')
        // ->select('id','name')
        // ->get();
        $this->allProducts = Client::all();
        $this->orderProducts = [];

        // foreach ($this->allProducts as $xxx) 
        // {
        //     $this->orderProducts[] = ['product_id' => '', 'quantity' => 1];
        // }
    }

    public function addProduct()
    {
        $this->orderProducts[] = ['product_id' => '', 'quantity' => 1];
    }

    public function removeProduct($index)
    {
        unset($this->orderProducts[$index]);
        array_values($this->orderProducts);
    }

    public function render()
    {
        //info($this->orderProducts); //สำหรับตรวจสอบค่า
        return view('livewire.accstar.products');
    }

}
