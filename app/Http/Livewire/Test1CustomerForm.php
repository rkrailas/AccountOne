<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Test1CustomerForm extends Component
{
    public $myCustomer = [];
    public $selectCustomer;

    public function testAccount()
    {
        $account = DB::table('account')
            ->select('*')
            ->where('detail', true)
            ->get();
        $account = json_decode(json_encode($account), true);

        $index = array_search("2131-13", array_column($account, 'account'));

    }

    public function getAccount()
    {
        ini_set('max_execution_time', 300); // 5 minutes

        // หาใบสำคัญที่ผ่านรายการไม่ได้
        $strsql = "select gltran from 
                        (select gltran
                        from gltran
                        where glaccount not in (select account from account)
                        or gjournaldt not between (select min(startdate) from perioddetail) 
                                                and (select max(enddate) from perioddetail)
                    union all
                       select gltran from gltran group by gltran having sum(gldebit) <> sum(glcredit) ) a
                    group by a.gltran";
        $listFailed = DB::select($strsql);
        $listFailed = json_decode(json_encode($listFailed), true);

        // ทำเป็นเงื่อนไขใบสำคัญที่ต้องยกเว้น
        $exceptGL = "";
        for ($i = 0; $i < count($listFailed); $i++) {
            $exceptGL = $exceptGL . "'" . $listFailed[$i]['gltran'] . "'";
            if ($i <> count($listFailed) - 1) {
                $exceptGL = $exceptGL . ',';
            }
        }
        $exceptGL = "gltran not in (" . $exceptGL . ")";

        $gltran = DB::table('gltran')
            ->select('gltran.*', 'account.acctype', 'perioddetail.period')
            //->whereRaw($exceptGL) //??? ยังไม่รองรับกรณีไม่มีใบที่ผิดเลย
            ->Join('account', 'gltran.glaccount', '=', 'account.account')
            ->Join('perioddetail', function ($join) {
                $join->on('perioddetail.startdate', '<=', 'gltran.gjournaldt');
                $join->on('perioddetail.enddate', '>=', 'gltran.gjournaldt');
            })
            ->orderBy('id')
            ->get();
        $gltran = json_decode(json_encode($gltran), true);

        $account = DB::table('account')
                    ->select('*')
                    ->where('detail', true)
                    ->get();
        $account = json_decode(json_encode($account), true);

        for ($o = 0; $o < count($gltran); $o++) 
        {

            //หาว่าอยู่ Row ไหน
            $index = array_search($gltran[$o]['glaccount'], array_column($account, 'account'));

            // Update currentbal
            if (($gltran[$o]['acctype'] == '1') or ($gltran[$o]['acctype'] == '5')) {
                $account[$index]['currentbal'] = $account[$index]['currentbal'] + $gltran[$o]['gldebit'];
                $account[$index]['currentbal'] = $account[$index]['currentbal'] - $gltran[$o]['glcredit'];
            } else {
                $account[$index]['currentbal'] = $account[$index]['currentbal'] - $gltran[$o]['gldebit'];
                $account[$index]['currentbal'] = $account[$index]['currentbal'] + $gltran[$o]['glcredit'];
            }

            // Update debitp1-24 หรือ creditp1-24            
            $xDebitpField  = "debitp" . $gltran[$o]['period'];
            $xCreditpField  = "creditp" . $gltran[$o]['period'];

            $account[$index][$xDebitpField] = $account[$index][$xDebitpField] + $gltran[$o]['gldebit'];
            $account[$index][$xCreditpField] = $account[$index][$xCreditpField] + $gltran[$o]['glcredit'];

            // Update currentdebit หรือ currentcredit
            $account[$index]['currentdebit'] = $account[$index]['currentdebit'] + $gltran[$o]['gldebit'];
            $account[$index]['currentcredit'] = $account[$index]['currentcredit'] + $gltran[$o]['glcredit'];

            $account[$index]['employee_id'] = 'Admin555';
            $account[$index]['transactiondate'] = Carbon::now();            
        }
        
        $gltran_insert = array_map(function($gltran) {
            return array(
                'bookid' => $gltran['bookid'],
                'gjournal' => $gltran['gjournal'],
                'gltran' => $gltran['gltran'],
                'gjournaldt' => $gltran['gjournaldt'],
                'glaccount' => $gltran['glaccount'],
                'gldebit' => $gltran['gldebit'],
                'glcredit' => $gltran['glcredit'],
                'invoiceno' => $gltran['currencyid'],
                'jobid' => $gltran['jobid'],
                'department' => $gltran['department'],
                'allocated' => $gltran['allocated'],
                'employee_id' => $gltran['employee_id'],
                'transactiondate' => $gltran['transactiondate'],
                'gldescription' => $gltran['gldescription']
            );
        }, $gltran);

        $insert_data = collect($gltran_insert); // Make a collection to use the chunk method
        // it will chunk the dataset in smaller collections containing 500 values each. 
        // Play with the value to get best result
        $chunks = $insert_data->chunk(500);
        foreach ($chunks as $chunk)
        {
            DB::table('glmast')->insert($chunk->toArray());
        }

        $deleteGLTran = "";
        for ($i = 0; $i < count($gltran); $i++)
        {
            $deleteGLTran = $deleteGLTran . $gltran[$i]['id'];

            if ($i < count($gltran)-1) 
            {
                $deleteGLTran = $deleteGLTran . ",";
            }
        }
        $deleteGLTran = explode(",", $deleteGLTran);
        DB::table('gltran')
            ->whereIn('id', $deleteGLTran)
            ->delete();

        foreach ($account as $item)
        {
            DB::table('account')->where('id', $item['id'])->update($item);
        }
    }

    public function clearSelectCustomer()
    {
        $this->selectCustomer = "";
    }

    public function render()
    {
        $data = DB::table('customer')
            ->select('customerid','name')
            ->orderby('customerid')
            ->get();

        return view('livewire.test1-customer-form',[
            'customer' => $data,
        ]);
    }
}
