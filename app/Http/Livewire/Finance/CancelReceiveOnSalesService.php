<?php

namespace App\Http\Livewire\Finance;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelReceiveOnSalesService extends Component
{
    protected $listeners = ['deleteConfirmed' => 'delete'];
    
    public $deleteNumber;
    public $bankHeader =[];
    public $bankDetails =[];
    public $sumPlus, $sumDeduct, $sumBalance, $sumAR = 0;
    public $btnDelete;

    public function searchDoc()
    {
        // bankHeader
        $strsql = "select gltran, to_char(gjournaldt,'YYYY-MM-DD') as gjournaldt, customerid, customername, documentref, amount
            , t.code || ': ' || t.description as taxscheme, witholdamt, witholdtax, witholdtaxrate
            , t1.code || ': ' || t1.description as taxscheme1, witholdamt1, witholdtax1, witholdtaxrate1
            , payby, account, accountcus, accounttax, taxrunningno, posted
            , fincharge, findiscount, feeamt, accountcharge, accountdis, accountfee
            from bank 
            left join taxtable t on bank.taxscheme = t.code and t.taxtype='2'
            left join taxtable t1 on bank.taxscheme1 = t1.code and t1.taxtype='2'
            where posted=true and bookid='RV'
            and gltran='" . $this->deleteNumber . "'";
        $data = DB::select($strsql);

        if (count($data) > 0){
            $this->bankHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ

            $this->bankHeader['amount'] = round($this->bankHeader['amount'],2);
            $this->bankHeader['witholdamt'] = round($this->bankHeader['witholdamt'],2);
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdtax'],2);
            $this->bankHeader['witholdtaxrate'] = round($this->bankHeader['witholdtaxrate'],2);
            $this->bankHeader['witholdamt1'] = round($this->bankHeader['witholdamt1'],2);
            $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdtax1'],2);
            $this->bankHeader['witholdtaxrate1'] = round($this->bankHeader['witholdtaxrate1'],2);
            $this->bankHeader['fincharge'] = round($this->bankHeader['fincharge'],2);
            $this->bankHeader['findiscount'] = round($this->bankHeader['findiscount'],2);
            $this->bankHeader['feeamt'] = round($this->bankHeader['feeamt'],2);

            $this->calPlusDeduct();
            // bankDetails ???ถึงตรงนี้
            $strsql = "select bankdetail.id, bankdetail.taxref, bankdetail.description, bankdetail.balance, bankdetail.amount, bankdetail.tax
            , taxdata.amount as oriamount, taxdata.taxamount as oritax, bankdetail.taxdataid, bankdetail.gltran
            from bankdetail
            join bank on bankdetail.gltran = bank.gltran
            join taxdata on bank.documentref = taxdata.taxnumber and bank.customerid = taxdata.customerid
            where bank.bookid='RV' and bank.iscancelled=false
            and bankdetail.gltran = '" . $this->deleteNumber . "'";
            $data = DB::select($strsql);
            dd($strsql);

            if(count($data) > 0){
                $this->bankDetails = json_decode(json_encode($data), true); 

                for($i=0; $i<count($this->bankDetails);$i++)
                {
                    $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'],2);
                    $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'],2);
                }

            $this->btnDelete = true;
            }
        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบใบสำคัญรับ !',
            ]);

            $this->resetPara();
        }
    }

    public function pressCancel()
    {
        $this->resetPara();
    }

    public function confirmDelete() 
    {
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() 
    {   
        DB::transaction(function() 
        {
            // 1. Delete taxdata & Update taxdataservice
            foreach ($this->bankDetails as $bankDetails2) {
                DB::statement("UPDATE taxdata SET iscancelled=true, employee_id='Admin', transactiondate=Now() 
                        where purchase=false and iscancelled=false 
                        and reference='" . $bankDetails2['gltran'] . "'
                        and taxnumber ='" . $bankDetails2['taxref'] . "'");

                DB::statement("UPDATE taxdataservice SET paidamount = paidamount - " . $bankDetails2['amount'] . 
                        ", paiddate=null, paidtaxamount = paidtaxamount - " . $bankDetails2['tax'] . 
                        ", employee_id='Admin', transactiondate=Now() where id = " . $bankDetails2['taxdataid']
                );
            }

            // 2. Update Bank
            DB::statement("UPDATE bank SET iscancelled=true, employee_id='Admin', transactiondate=Now() 
                where gltran='" . $this->bankHeader['gltran'] . "'");
            
            // 3. Delete bankdetail
            DB::statement("DELETE from  bankdetail where gltran='" . $this->bankHeader['gltran'] . "'");

            // 4. ตรวจสอบว่า post gl หรือยัง
            $strsql = "select * from gltran where gltran='" . $this->bankHeader['gltran'] . "'";
            $data =  DB::select($strsql);
            if(count($data)){
                DB::statement("DELETE FROM gltran where gltran=? " 
                , [$this->bankHeader['gltran']]);
            }else{
                $strsql = "select * from glmast where gltran='" . $this->bankHeader['gltran'] . "'";
                $data =  DB::select($strsql);
                if(count($data)){
                    $data2 = json_decode(json_encode($data), true);
                    for($i=0; $i<count($data2);$i++){
                        //สลับ gldebit กับ glcredit
                        DB::statement("INSERT INTO gltran(gjournal,gltran,gjournaldt,glaccount,gldebit,glcredit
                            ,gldescription,department,jobid,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?)"
                            , [$data2[$i]['gjournal'],  $data2[0]['gltran'] . "R", Carbon::now()->format('Y-m-d'), $data2[$i]['glaccount']
                            , $data2[$i]['glcredit'], $data2[$i]['gldebit'], "ยกเลิก-" . $data2[$i]['gldescription']
                            , $data2[$i]['department'], $data2[$i]['jobid'], 'Admin', Carbon::now()]);
                    }
                }
            }
            
            $this->resetPara();
            $this->dispatchBrowserEvent('display-Message',['message' => 'Cancel Successfully!']);
        });        
    }

    public function resetPara()
    {
        $this->reset(['deleteNumber', 'bankHeader', 'bankDetails', 'btnDelete', 'sumPlus', 'sumDeduct', 'sumBalance', 'sumAR']);
        $this->bankHeader['amount'] = 0;
        $this->bankHeader['taxscheme'] = "";
        $this->bankHeader['witholdamt'] = 0;
        $this->bankHeader['witholdtax'] = 0;
        $this->bankHeader['taxscheme1'] = "";
        $this->bankHeader['witholdamt1'] = 0;
        $this->bankHeader['witholdtax1'] = 0;
    }

    public function calculateSummary() //ทำทุกครั้งที่มีการ Render
    {
        $this->bankHeader['witholdamt']= 0;
        $this->sumBalance = 0;
        $this->sumAR = 0;
        $sumReceieAmount = 0; //ยอดรับก่อนหัก W/H

        for($i=0; $i<count($this->bankDetails);$i++)
        {
            $this->bankHeader['witholdamt'] = round($this->bankHeader['witholdamt'] + ($this->bankDetails[$i]['amount'] - 
                        ($this->bankDetails[$i]['amount'] * $this->bankDetails[$i]['oritax'] / $this->bankDetails[$i]['oriamount'])),2);

            $sumReceieAmount = $sumReceieAmount + $this->bankDetails[$i]['amount'];

            $this->sumBalance = $this->sumBalance  + $this->bankDetails[$i]['balance'];

            $this->sumAR = $this->sumAR  + $this->bankDetails[$i]['amount'];
        }
        
        if ($this->bankHeader['witholdtaxrate']) {
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdamt'] * $this->bankHeader['witholdtaxrate'] / 100, 2);
        }  

        $this->bankHeader['amount'] = round($sumReceieAmount -  $this->bankHeader['witholdtax'] -  $this->bankHeader['witholdtax1'] 
                        + $this->sumPlus - $this->sumDeduct , 2);
    }

    public function calPlusDeduct()
    {
        $this->sumPlus = $this->bankHeader['fincharge'];
        $this->sumDeduct = $this->bankHeader['findiscount'] + $this->bankHeader['feeamt'];
    }

    public function mount()
    {
        $this->resetPara();
    }

    public function render()
    {
        // Summary grid     
        if($this->bankDetails != Null)
        {            
            $this->calculateSummary();
        }else{
            
        }

        return view('livewire.finance.cancel-receive-on-sales-service');
    }
}
