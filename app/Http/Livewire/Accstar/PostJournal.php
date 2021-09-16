<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostJournal extends Component
{
    public $journalNoFrom, $journalNoTo;
    public $journalDateFrom, $journalDateTo;
    public $journalType;
    public $totalFailed, $totalPass = 0;
    public $listFailed, $listPass = [];

    public function clearValue()
    {
        $this->totalFailed = 0;
        $this->totalPass = 0;
        $this->listFailed = [];
        $this->listPass = [];
    }

    public function postJournal()
    {
        // Query Journal ตามเงื่อนที่ยังไม่ Post เรียงตาม gltran
        // Loop แล้วทำตาม Step ด้านล่าง
        // Update currentbal ต้องตรวจสอบด้วยว่าจะลบหรือบวก (Dr.หมวด 1,5 Cr.หมวด 2,3,4) 
        // Update debitp1-24 หรือ creditp1-24 ต้องตรวจสอบว่าอยู่ในงวดบัญชีไหน'
        // Update currentdebit หรือ currentcredit

        // .จัดการเงื่อนไขการดึงข้อมูล
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

        if ($this->journalType != "") {
            $xWhere = $xWhere . " and account.acctype = '" . $this->journalType . "'";
        }
        // /.จัดการเงื่อนไขการดึงข้อมูล

        // .Post Process
        $gltranHeader = DB::table('gltran')
            ->select('gltran')
            ->whereRaw($xWhere)
            ->Join('account', 'gltran.glaccount', '=', 'account.account')
            ->groupBy('gltran')
            ->get();
        $gltranHeader = json_decode(json_encode($gltranHeader), true);

        $this->clearValue();

        for ($i = 0; $i < count($gltranHeader); $i++) {

            $gltranDetail = DB::table('gltran')
                ->select('gltran.*', 'account.acctype', 'perioddetail.period')
                ->where('gltran', $gltranHeader[$i]['gltran'])
                ->leftJoin('account', 'gltran.glaccount', '=', 'account.account')
                ->leftJoin('perioddetail', function ($join) {
                    $join->on('perioddetail.startdate', '<=', 'gltran.gjournaldt');
                    $join->on('perioddetail.enddate', '>=', 'gltran.gjournaldt');
                })
                ->orderBy('id')
                ->get();
            $gltranDetail = json_decode(json_encode($gltranDetail), true);

            DB::beginTransaction();

            for ($o = 0; $o < count($gltranDetail); $o++) {
                if ($gltranDetail[$o]['acctype'] == null or $gltranDetail[$o]['period'] == null) {
                    $this->totalFailed = $this->totalFailed + 1;
                    $this->listFailed[] = $gltranHeader[$i]['gltran'];
                    DB::rollBack();
                    break;
                } else {
                    $account = DB::table('account')
                        ->select('*')
                        ->where('account', $gltranDetail[$o]['glaccount'])
                        ->get();
                    $account = json_decode(json_encode($account[0]), true);

                    // Update currentbal
                    if (($gltranDetail[$o]['acctype'] == '1') or ($gltranDetail[$o]['acctype'] == '5')) {
                        $account['currentbal'] = $account['currentbal'] + $gltranDetail[$o]['gldebit'];
                        $account['currentbal'] = $account['currentbal'] - $gltranDetail[$o]['glcredit'];
                    } else {
                        $account['currentbal'] = $account['currentbal'] - $gltranDetail[$o]['gldebit'];
                        $account['currentbal'] = $account['currentbal'] + $gltranDetail[$o]['glcredit'];
                    }

                    // Update debitp1-24 หรือ creditp1-24            
                    $xDebitpField  = "debitp" . $gltranDetail[$o]['period'];
                    $xCreditpField  = "creditp" . $gltranDetail[$o]['period'];

                    $account[$xDebitpField] = $account[$xDebitpField] + $gltranDetail[$o]['gldebit'];
                    $account[$xCreditpField] = $account[$xCreditpField] + $gltranDetail[$o]['glcredit'];

                    // Update currentdebit หรือ currentcredit
                    $account['currentdebit'] = $account['currentdebit'] + $gltranDetail[$o]['gldebit'];
                    $account['currentcredit'] = $account['currentcredit'] + $gltranDetail[$o]['glcredit'];

                    $account['employee_id'] = 'Admin';
                    $account['transactiondate'] = Carbon::now();

                    DB::table('account')->where('id', $account['id'])->update($account);
                    DB::statement(
                        "INSERT INTO glmast(bookid, gjournal, gltran, gjournaldt, glaccount
                        , gldebit, glcredit, invoiceno, jobid, department
                        , allocated, employee_id, transactiondate, gldescription)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        [
                            $gltranDetail[$o]['bookid'], $gltranDetail[$o]['gjournal'], $gltranDetail[$o]['gltran'], $gltranDetail[$o]['gjournaldt']
                            , $gltranDetail[$o]['glaccount'], $gltranDetail[$o]['gldebit'], $gltranDetail[$o]['glcredit'], $gltranDetail[$o]['currencyid']
                            , $gltranDetail[$o]['jobid'], $gltranDetail[$o]['department'], $gltranDetail[$o]['allocated'], 'Admin', Carbon::now()
                            , $gltranDetail[$o]['gldescription']
                        ]
                    );
                    DB::table('gltran')->where('gltran', $gltranDetail[$o]['gltran'])->delete();

                    if ($o == count($gltranDetail) - 1)
                    {
                        $this->totalPass = $this->totalPass + 1;
                        $this->listPass[] = $gltranHeader[$i]['gltran'];
                        DB::commit();
                    }
                }
            }
        }
    }

    public function mount()
    {
        $this->clearValue();
    }

    public function render()
    {
        // .Bind Data to Dropdown
        $journalTypes_dd = DB::table('misctable')
            ->select('code', 'other')
            ->where('tabletype', 'JR')
            ->get();

        return view('livewire.accstar.post-journal', [
            'journalTypes_dd' => $journalTypes_dd,
        ]);
    }
}
