<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Test2 extends Component
{
    public function getDateWiseScore($data) {
        //Group & Summary Array
        
        $groups = array();
        foreach ($data as $item) {
            $key = $item['account'];
            if (!array_key_exists($key, $groups)) {
                $groups[$key] = array(
                    'account' => $item['account'],
                    'gldebit' => $item['gldebit'],
                    'glcredit' => $item['glcredit'],
                );
            } else {
                $groups[$key]['gldebit'] = $groups[$key]['gldebit'] + $item['gldebit'];
                $groups[$key]['glcredit'] = $groups[$key]['glcredit'] + $item['glcredit'];
            }
        }

        foreach ($groups as $item){
            if ($item['gldebit'] > $item['glcredit']){
                $groups[$item['account']]['gldebit'] = $groups[$item['account']]['gldebit'] - $groups[$item['account']]['glcredit'];
                $groups[$item['account']]['glcredit'] = 0;
            }else{
                $groups[$item['account']]['glcredit'] = $groups[$item['account']]['glcredit'] - $groups[$item['account']]['gldebit'];
                $groups[$item['account']]['gldebit'] = 0;
            }
        }
        return $groups;
    }

    public function render()
    {
        $aa = 
            [
                1 => ["account" => "1001","gldebit" => 10,"glcredit" => 0],
                2 => ["account" => "1001","gldebit" => 0,"glcredit" => 20],
                3 => ["account" => "1002","gldebit" => 0,"glcredit" => 20],
                4 => ["account" => "1002","gldebit" => 0,"glcredit" => 20],
                5 => ["account" => "1002","gldebit" => 10,"glcredit" => 0],
            ];
        
        dd($this->getDateWiseScore($aa));

        return view('livewire.test2');
    }
}
