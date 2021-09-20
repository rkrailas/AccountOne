<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostJournal extends Component
{
    public $journalNoFrom, $journalNoTo;
    public $journalDateFrom, $journalDateTo;
    public $listFailed = [];
    public $countPostPass = 0;

    public function postJournal()
    {
        // จัดการเงื่อนไข และ Query เอาเฉพาะเลขที่ใบสำคัญ (Group By)
        // Loop แล้วทำตาม Step ด้านล่าง
        // Update currentbal ต้องตรวจสอบด้วยว่าจะลบหรือบวก (Dr.หมวด 1,5 Cr.หมวด 2,3,4) 
        // Update debitp1-24 หรือ creditp1-24 ต้องตรวจสอบว่าอยู่ในงวดบัญชีไหน'
        // Update currentdebit หรือ currentcredit

        DB::transaction(function () {
            // จัดการเงื่อนไขการดึงข้อมูล
            if ($this->journalNoFrom != "" and $this->journalNoTo == "") {
                $this->journalNoTo = $this->journalNoFrom;
            }

            if ($this->journalDateFrom != "" and $this->journalDateTo == "") {
                $this->journalDateTo = $this->journalDateFrom;
            }

            $xWhere = "1=1";
            if ($this->journalNoFrom != "") {
                $xWhere = $xWhere . " and gltran between '" . $this->journalNoFrom . "' and '" . $this->journalNoTo . "'";
            }

            if ($this->journalDateFrom != "") {
                $xWhere = $xWhere . " and gjournaldt between '" . $this->journalDateFrom . "' and '" . $this->journalDateTo . "'";
            }

            // หาใบสำคัญที่ผ่านรายการไม่ได้
            $strsql = "select gltran from 
                        (select gltran
                        from gltran
                        where glaccount not in (select account from account)
                        or gjournaldt not between (select min(startdate) from perioddetail) 
                                                and (select max(enddate) from perioddetail)"
                . " and " . $xWhere
                . " union all
                       select gltran from gltran where " . $xWhere . " group by gltran having sum(gldebit) <> sum(glcredit) ) a
                    group by a.gltran";
            $this->listFailed = DB::select($strsql);
            $this->listFailed = json_decode(json_encode($this->listFailed), true);

            // ทำเป็นเงื่อนไขใบสำคัญที่ต้องยกเว้น
            $exceptGL = "";
            for ($i = 0; $i < count($this->listFailed); $i++) {
                $exceptGL = $exceptGL . "'" . $this->listFailed[$i]['gltran'] . "'";
                if ($i <> count($this->listFailed) - 1) {
                    $exceptGL = $exceptGL . ',';
                }
            }

            if ($exceptGL != "") {
                $xWhere = $xWhere . " and gltran not in (" . $exceptGL . ")";
            }

            // ผ่านรายการ GL ผ่านรายการ GL ที่ผ่านการตรวจสอบ
            $gltran = DB::table('gltran')
                ->select('gltran.*', 'account.acctype', 'perioddetail.period')
                ->whereRaw($xWhere)
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

            for ($i = 0; $i < count($gltran); $i++) {
                //หาว่าอยู่ Row ไหน
                $index = array_search($gltran[$i]['glaccount'], array_column($account, 'account'));

                // Update currentbal
                if (($gltran[$i]['acctype'] == '1') or ($gltran[$i]['acctype'] == '5')) {
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] + $gltran[$i]['gldebit'];
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] - $gltran[$i]['glcredit'];
                } else {
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] - $gltran[$i]['gldebit'];
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] + $gltran[$i]['glcredit'];
                }

                // Update debitp1-24 หรือ creditp1-24            
                $xDebitpField  = "debitp" . $gltran[$i]['period'];
                $xCreditpField  = "creditp" . $gltran[$i]['period'];

                $account[$index][$xDebitpField] = $account[$index][$xDebitpField] + $gltran[$i]['gldebit'];
                $account[$index][$xCreditpField] = $account[$index][$xCreditpField] + $gltran[$i]['glcredit'];

                // Update currentdebit หรือ currentcredit
                $account[$index]['currentdebit'] = $account[$index]['currentdebit'] + $gltran[$i]['gldebit'];
                $account[$index]['currentcredit'] = $account[$index]['currentcredit'] + $gltran[$i]['glcredit'];

                $account[$index]['employee_id'] = 'Admin555';
                $account[$index]['transactiondate'] = Carbon::now();
            }

            // insert data to glmast
            $insert_glmast = array_map(function ($gltran) {
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
                    'employee_id' => 'Admin555',
                    'transactiondate' => Carbon::now(),
                    'gldescription' => $gltran['gldescription']
                );
            }, $gltran);

            $insert_data = collect($insert_glmast); // Make a collection to use the chunk method
            // it will chunk the dataset in smaller collections containing 500 values each. 
            // Play with the value to get best result
            $chunks = $insert_data->chunk(500);

            foreach ($chunks as $chunk) {
                DB::table('glmast')->insert($chunk->toArray());
            }

            // delete data ingltran
            $deleteGLTran = "";

            for ($i = 0; $i < count($gltran); $i++) {
                $deleteGLTran = $deleteGLTran . $gltran[$i]['id'];

                if ($i < count($gltran) - 1) {
                    $deleteGLTran = $deleteGLTran . ",";
                }
            }

            $deleteGLTran = explode(",", $deleteGLTran);

            DB::table('gltran')
                ->whereIn('id', $deleteGLTran)
                ->delete();

            // update data in account
            foreach ($account as $item) {
                DB::table('account')->where('id', $item['id'])->update($item);
            }
            
            $this->countPostPass = count($gltran);
        });
    }

    public function mount()
    {
        $this->listFailed = [];
    }

    public function render()
    {
        return view('livewire.accstar.post-journal');
    }
}
