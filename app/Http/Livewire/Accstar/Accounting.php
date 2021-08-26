<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\gltran;

class Accounting extends Component
{
    public $gltranNo = "";
    public $sumGldebit = 0;
    public $sumGlcredit = 0;
    public $gltran_details = [];

    public function getGltran()
    {
        $this->gltran_details = DB::table('gltran')
            ->select('gltran','glaccount','glaccname','gldescription','gldebit','glcredit')
            ->where('gltran','=',$this->gltranNo)
            ->get();

        // $this->gltran_details->transform(function($item)
        //     {
        //         $item->gldebit = number_format($item->gldebit, 2);
        //         return $item;
        //     });
    }

    public function render()
    {
        $gltrans = DB::table('gltran')
        ->select('gltran')
        ->groupBy('gltran')
        ->having('gltran', '<>', '')
        ->limit(10)
        ->get();

        $journals = DB::table('misctable')
        ->select('code','description')
        ->where('tabletype','=','JR')
        ->get();

        $allocations = DB::table('allocationtable')
        ->select('code','description')
        ->get();

        $accountNos = DB::table('account')
        ->select('account','accname')
        ->where('detail','=',TRUE)
        ->orderby('account')
        ->get(); 

        if ($this->gltranNo != "")
        {
            $this->sumGldebit = number_format($this->gltran_details->sum('gldebit'),2);
            $this->sumGlcredit = number_format($this->gltran_details->sum('glcredit'),2);
        }
        
        return view('livewire.accstar.accounting',[
            'gltrans' => $gltrans,
            'journals' => $journals,
            'allocations' => $allocations,
            'accountNos' => $accountNos,
        ]);
    }
}
