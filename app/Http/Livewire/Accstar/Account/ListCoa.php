<?php

namespace App\Http\Livewire\Accstar\Account;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ListCoa extends Component
{
    public $coa;
    public $tree;

    public function myOutputList($TreeArray)
    {        
        $this->coa = $this->coa . '<ul>';
        foreach ($TreeArray as $key => $arr)
        {
            if ($key == "id")
            {
                $this->coa = $this->coa . '<li>';
            }

            if(is_array($arr)) 
            {
                $this->myOutputList($arr);
            }
            else
            {
                if ($key == "id")
                {
                    $this->coa = $this->coa . $arr . " : ";
                }
                if ($key == "accname")
                {
                    $this->coa = $this->coa . $arr;
                }
            }

            if ($key == "name")
            {
                $this->coa = $this->coa . '</li>';
            }
        }
        $this->coa = $this->coa . '</ul>';
    }

    public function chartofaccounts()
    {
        $strsql = "select account as id, accgroup as parent_id, accname
                    from account a where acctype='1'
                    order by account";
        $account = DB::select($strsql);
        $account = json_decode(json_encode($account), true);

        $this->tree = $this->buildTree($account);

        $this->myOutputList($this->tree);
    }

    public function buildTree(array $elements, $parentId = "0") { //OK แต่ติดที่ทำไมต้องเริ่มที่ 0
        $branch = array();
    
        foreach ($elements as $element) {   
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
    
        return $branch;
    }  

    public function render()
    {
        return view('livewire.accstar.account.list-coa');
    }
}
