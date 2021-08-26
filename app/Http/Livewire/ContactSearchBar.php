<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ContactSearchBar extends Component
{
    public $query;
    public $contacts;
    public $highlightIndex;

    public function mount()
    {
        $this->reset1();
    }

    public function reset1()
    {
        $this->query = '';
        $this->contacts = [];
        $this->highlightIndex = 0;
    }

    public function incrementHighlight()
    {
        if ($this->highlightIndex == count($this->contacts) - 1)
        {
            $this->highlightIndex = 0;
        }

        $this->highlightIndex++;
    }

    public function decrementHighlight()
    {
        if ($this->highlightIndex == 0)
        {
            $this->highlightIndex = count($this->contacts) - 1;
        }
        
        $this->highlightIndex--;
    }

    public function selectContact($selectByClick = null)
    {
        if ($selectByClick == null){
            $contact = $this->contacts[$this->highlightIndex] ?? null;
            if ($contact){
                dd($contact);
            }
        }else{
            $contact = $this->contacts[$selectByClick] ?? null;
            if ($contact){
                dd($contact);
            }
        }  
    }

    public function updatedQuery()
    {
        $data = DB::table('customer')
                        ->select('customerid','name')
                        ->where('name', 'LIKE', '%'.$this->query.'%')
                        ->get()
                        ->toArray();
        $this->contacts = json_decode(json_encode($data), true); 
    }


    public function render()
    {
        return view('livewire.contact-search-bar');
    }
}