<?php

namespace App\Http\Livewire\Account;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelPostJournal extends Component
{
    public $journalNoFrom, $journalNoTo;
    public $journalDateFrom, $journalDateTo;
    public $postDateFrom, $postDateTo;
    public $countCancelPostPass = 0;
    public $enableBtnCancelPost = false;
    public $listPass = [];
    public $sqlWhere;

    public function reSetPage()
    {
        $this->reset(['journalNoFrom', 'journalNoTo', 'journalDateFrom','journalDateTo','postDateFrom','postDateTo'
                    ,'countCancelPostPass','sqlWhere','enableBtnCancelPost','listPass']);
    }

    public function getJournal()
    {
        // จัดการเงื่อนไขการดึงข้อมูล
        if ($this->journalNoFrom != "" and $this->journalNoTo == "") {
            $this->journalNoTo = $this->journalNoFrom;
        }

        if ($this->journalDateFrom != "" and $this->journalDateTo == "") {
            $this->journalDateTo = $this->journalDateFrom;
        }

        if ($this->postDateFrom != "" and $this->postDateTo == "") {
            $this->postDateTo = $this->postDateFrom;
        }

        $this->sqlWhere = "1=1";
        if ($this->journalNoFrom != "") {
            $this->sqlWhere = $this->sqlWhere . " and gltran between '" . $this->journalNoFrom . "' and '" . $this->journalNoTo . "'";
        }

        if ($this->journalDateFrom != "") {
            $this->sqlWhere = $this->sqlWhere . " and gjournaldt between '" . $this->journalDateFrom . "' and '" . $this->journalDateTo . "'";
        }

        if ($this->postDateFrom != "") {
            $this->sqlWhere = $this->sqlWhere . " and to_char(glmast.transactiondate,'YYYY-MM-DD') between '" . $this->postDateFrom . "' and '" . $this->postDateTo . "'";
        }

        $strsql = "select gltran from glmast where " . $this->sqlWhere . " group by gltran order by gltran";
        $this->listPass = DB::select($strsql);
        $this->listPass = json_decode(json_encode($this->listPass), true);
        if ($this->listPass){
            $this->enableBtnCancelPost = true;
        }
    }

    public function cancelPostJournal()
    {
        DB::transaction(function () {
            // ยกเลิกผ่านรายการ GL
            $glmast = DB::table('glmast')
                ->select('glmast.*', 'account.acctype', 'perioddetail.period')
                ->whereRaw($this->sqlWhere)
                ->Join('account', 'glmast.glaccount', '=', 'account.account')
                ->Join('perioddetail', function ($join) {
                    $join->on('perioddetail.startdate', '<=', 'glmast.gjournaldt');
                    $join->on('perioddetail.enddate', '>=', 'glmast.gjournaldt');
                })
                ->orderBy('id')
                ->get();
            $glmast = json_decode(json_encode($glmast), true);

            $account = DB::table('account')
                ->select('*')
                ->where('detail', true)
                ->get();
            $account = json_decode(json_encode($account), true);

            for ($i = 0; $i < count($glmast); $i++) {
                //หาว่าอยู่ Row ไหน
                $index = array_search($glmast[$i]['glaccount'], array_column($account, 'account'));

                // Update currentbal
                if (($glmast[$i]['acctype'] == '1') or ($glmast[$i]['acctype'] == '5')) {
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] - $glmast[$i]['gldebit'];
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] + $glmast[$i]['glcredit'];
                } else {
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] + $glmast[$i]['gldebit'];
                    $account[$index]['currentbal'] = $account[$index]['currentbal'] - $glmast[$i]['glcredit'];
                }

                // Update debitp1-24 หรือ creditp1-24            
                $xDebitpField  = "debitp" . $glmast[$i]['period'];
                $xCreditpField  = "creditp" . $glmast[$i]['period'];

                $account[$index][$xDebitpField] = $account[$index][$xDebitpField] - $glmast[$i]['gldebit'];
                $account[$index][$xCreditpField] = $account[$index][$xCreditpField] - $glmast[$i]['glcredit'];

                // Update currentdebit หรือ currentcredit
                $account[$index]['currentdebit'] = $account[$index]['currentdebit'] - $glmast[$i]['gldebit'];
                $account[$index]['currentcredit'] = $account[$index]['currentcredit'] - $glmast[$i]['glcredit'];

                $account[$index]['employee_id'] = 'Admin555';
                $account[$index]['transactiondate'] = Carbon::now();
            }

             // insert data to glmast
             $insert_gltran = array_map(function ($glmast) {
                return array(
                    'bookid' => $glmast['bookid'],
                    'gjournal' => $glmast['gjournal'],
                    'gltran' => $glmast['gltran'],
                    'gjournaldt' => $glmast['gjournaldt'],
                    'glaccount' => $glmast['glaccount'],
                    'gldebit' => $glmast['gldebit'],
                    'glcredit' => $glmast['glcredit'],
                    'currencyid' => $glmast['invoiceno'],
                    'jobid' => $glmast['jobid'],
                    'department' => $glmast['department'],
                    'allocated' => $glmast['allocated'],
                    'employee_id' => 'Admin555',
                    'transactiondate' => Carbon::now(),
                    'gldescription' => $glmast['gldescription']
                );
            }, $glmast);

            $insert_data = collect($insert_gltran); // Make a collection to use the chunk method
            // it will chunk the dataset in smaller collections containing 500 values each. 
            // Play with the value to get best result
            $chunks = $insert_data->chunk(500);

            foreach ($chunks as $chunk) {
                DB::table('gltran')->insert($chunk->toArray());
            }

            // delete data ingltran
            $deleteGLMast = "";

            for ($i = 0; $i < count($glmast); $i++) {
                $deleteGLMast = $deleteGLMast . $glmast[$i]['id'];

                if ($i < count($glmast) - 1) {
                    $deleteGLMast = $deleteGLMast . ",";
                }
            }

            $deleteGLMast = explode(",", $deleteGLMast);

            DB::table('gltran')
                ->whereIn('id', $deleteGLMast)
                ->delete();

            // update data in account
            foreach ($account as $item) {
                DB::table('account')->where('id', $item['id'])->update($item);
            }
            
            $this->countCancelPostPass = count($glmast);

        });
    }

    public function render()
    {
        return view('livewire.account.cancel-post-journal');
    }
}
