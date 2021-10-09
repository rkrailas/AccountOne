<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceiveOnSales extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $sortDirection = "desc";
    public $sortBy = "gjournaldt";
    public $numberOfPage = 10;
    public $searchTerm = null;
    
    public $showEditModal = null;
    public $customers_dd, $taxTypes_dd, $accountNos_dd; //Dropdown
    public $sNumberDelete;
    public $bankHeader = []; //gltran,gjournaldt,documentref,customerid,customername,addressl1,addressl2,addressl3,amount,findiscount,fincharge,feeamt,payby
                            //,journal,bookid,account,accountcus,accounttax,accountcharge,accountdis,accountfee,taxscheme,witholdamt,witholdtax,witholdtaxrate
                            //,taxscheme1,witholdamt1,witholdtax1,witholdtaxrate1,taxtype,taxrunningno,posted,department,notes  
    public $bankDetails = []; //gltran,description,balance,findiscount,amount,journal(B1),taxref,tax,taxdate,bookid(R1),taxdataid,totalamount
    public $sumPlus, $sumDeduct, $sumBalance, $sumAR = 0;
    public $genGLs = []; //gltran, gjournaldt, glaccount, glaccname, gldescription, gldebit, glcredit, jobid, department
                        //, allcated, currencyid, posted, bookid, employee_id, transactiondate
    public $sumDebit, $sumCredit = 0;

    public function getBalance($index)
    {
        $this->bankDetails[$index]['amount'] = $this->bankDetails[$index]['balance'];
    }

    public function showGL()
    {
        $this->generateGl();
        $this->dispatchBrowserEvent('show-myModal2'); //แสดง Model Form
    }

    public function generateGl()
    {
        // .Concept
            //Dr.เงินสดหรือเงินฝากธนาคาร ($bankHeader['amount'])
            //Dr.ภาษีถูกหัก ณ ที่จ่าย ($bankHeader['witholdtax'])
            //Dr.ส่วนลดจ่าย ($bankHeader['findiscount'])
            //Dr.ค่าธรรมเนีนม ($bankHeader['feeamt'])
            //  Cr.ลูกหนี้การค้า ($sumAR) 
            //  Cr.รายได้เบ็ดเตล็ด ($bankHeader['fincharge'])
        // /.Concept
        
        $this->genGLs = [];
        $this->sumDebit = 0;
        $this->sumCredit = 0;

        // .Dr เงินสดหรือเงินฝากธนาคาร 
        if ($this->bankHeader['amount']) {
            $accountCode = "";
            $accountName = "";
    
            if ($this->bankHeader['account'])
            {
                $accountCode = $this->bankHeader['account'];
    
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $accountCode)
                    ->where('detail', true)
                    ->get();     
    
                if ($data->count() > 0) {
                    $accountName = $data[0]->accnameother;
                } 
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'JR', 'gltran'=>$this->bankHeader['gltran'], 'gjournaldt'=>$this->bankHeader['gjournaldt'], 'glaccount'=>$accountCode
                , 'glaccname'=>$accountName, 'gldescription'=>'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref']
                , 'gldebit'=>$this->bankHeader['amount'], 'glcredit'=>0, 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false
                , 'bookid'=>'', 'employee_id'=>'Admin', 'transactiondate'=>Carbon::now()
            ]);
        }        
        // /.Dr เงินสดหรือเงินฝากธนาคาร 

        // .Dr.ภาษีถูกหัก ณ ที่จ่าย 
        if ($this->bankHeader['witholdtax']) {
            $accountCode = "";
            $accountName = "";
    
            if ($this->bankHeader['accounttax'])
            {
                $accountCode = $this->bankHeader['accounttax'];
    
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $accountCode)
                    ->where('detail', true)
                    ->get();     
    
                if ($data->count() > 0) {
                    $accountName = $data[0]->accnameother;
                } 
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'JR', 'gltran'=>$this->bankHeader['gltran'], 'gjournaldt'=>$this->bankHeader['gjournaldt'], 'glaccount'=>$accountCode
                , 'glaccname'=>$accountName, 'gldescription'=>'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref']
                , 'gldebit'=>$this->bankHeader['witholdtax'], 'glcredit'=>0, 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false
                , 'bookid'=>'', 'employee_id'=>'Admin', 'transactiondate'=>Carbon::now()
            ]);
        }        
        // /.Dr.ภาษีถูกหัก ณ ที่จ่าย 

        // .Dr.ส่วนลดจ่าย
        if ($this->bankHeader['findiscount']) {
            $accountCode = "";
            $accountName = "";
    
            if ($this->bankHeader['accountdis'])
            {
                $accountCode = $this->bankHeader['accountdis'];
    
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $accountCode)
                    ->where('detail', true)
                    ->get();     
    
                if ($data->count() > 0) {
                    $accountName = $data[0]->accnameother;
                } 
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'JR', 'gltran'=>$this->bankHeader['gltran'], 'gjournaldt'=>$this->bankHeader['gjournaldt'], 'glaccount'=>$accountCode
                , 'glaccname'=>$accountName, 'gldescription'=>'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref']
                , 'gldebit'=>$this->bankHeader['findiscount'], 'glcredit'=>0, 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false
                , 'bookid'=>'', 'employee_id'=>'Admin', 'transactiondate'=>Carbon::now()
            ]);
        }        
        // /.Dr.ส่วนลดจ่าย

        // .Dr.ค่าธรรมเนียม
        if ($this->bankHeader['feeamt']) {
            $accountCode = "";
            $accountName = "";
    
            if ($this->bankHeader['accountfee'])
            {
                $accountCode = $this->bankHeader['accountfee'];
    
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $accountCode)
                    ->where('detail', true)
                    ->get();     
    
                if ($data->count() > 0) {
                    $accountName = $data[0]->accnameother;
                } 
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'JR', 'gltran'=>$this->bankHeader['gltran'], 'gjournaldt'=>$this->bankHeader['gjournaldt'], 'glaccount'=>$accountCode
                , 'glaccname'=>$accountName, 'gldescription'=>'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref']
                , 'gldebit'=>$this->bankHeader['feeamt'], 'glcredit'=>0, 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false
                , 'bookid'=>'', 'employee_id'=>'Admin', 'transactiondate'=>Carbon::now()
            ]);
        }        
        // /.Dr.ค่าธรรมเนียม

        // .Cr.ค่าปรับ (รายได้เบ็ดเตล็ด)
        if ($this->bankHeader['fincharge']) {
            $accountCode = "";
            $accountName = "";
    
            if ($this->bankHeader['accountcharge'])
            {
                $accountCode = $this->bankHeader['accountcharge'];
    
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $accountCode)
                    ->where('detail', true)
                    ->get();     
    
                if ($data->count() > 0) {
                    $accountName = $data[0]->accnameother;
                } 
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'JR', 'gltran'=>$this->bankHeader['gltran'], 'gjournaldt'=>$this->bankHeader['gjournaldt'], 'glaccount'=>$accountCode
                , 'glaccname'=>$accountName, 'gldescription'=>'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref']
                , 'gldebit'=>0, 'glcredit'=>$this->bankHeader['fincharge'], 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false
                , 'bookid'=>'', 'employee_id'=>'Admin', 'transactiondate'=>Carbon::now()
            ]);
        }        
        // /.Cr.ค่าปรับ (รายได้เบ็ดเตล็ด)

        // .Cr.ลูกหนี้การค้า //account = $bankHeader['accountcus'] or buyer.account or controldef.account where id='AR'
        if ($this->sumAR) {
            $accountCode = "";
            $accountName = "";
    
            if ($this->bankHeader['accountcus']){
                $accountCode = $this->bankHeader['accountcus'];
    
            }else{
                $data = DB::table('buyer')
                ->select("account")
                ->where('customerid', $this->bankHeader['customerid'])
                ->get();
    
                if ($data->count() > 0) {
                    $accountCode = $data[0]->account;
                }else{
                    $data = DB::table('controldef')
                    ->select("account")
                    ->where('id', 'AR')
                    ->get();     
    
                    if ($data->count() > 0) {
                        $accountCode = $data[0]->account;
                    }
                }
            }
    
            if ($accountCode != ""){          
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $accountCode)
                    ->where('detail', true)
                    ->get();            
                if ($data->count() > 0) {
                    $accountName = $data[0]->accnameother;
                } 
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'JR', 'gltran'=>$this->bankHeader['gltran'], 'gjournaldt'=>$this->bankHeader['gjournaldt'], 'glaccount'=>$accountCode
                , 'glaccname'=>$accountName, 'gldescription'=>'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref']
                , 'gldebit'=>0, 'glcredit'=>$this->sumAR, 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false
                , 'bookid'=>'', 'employee_id'=>'Admin', 'transactiondate'=>Carbon::now()
            ]);
        }        
        //.Dr.ลูกหนี้การค้า 

        // Summary Debit & Credit
        for($i=0; $i<count($this->genGLs);$i++)
        {
            $this->sumDebit = $this->sumDebit + $this->genGLs[$i]['gldebit'];
            $this->sumCredit = $this->sumCredit + $this->genGLs[$i]['glcredit'];
        }  
    }

    public function clearValue()
    {
        $this->bankHeader = [];
        $this->bankDetails =[];
        $this->sumPlus = 0;
        $this->sumDeduct = 0;
        $this->sumBalance = 0;
        $this->sumAR = 0;
        $this->bankHeader['amount'] = 0;
    }

    public function updatedBankHeaderCustomerid()
    {
        $xxx = $this->bankHeader['customerid'];
        $this->clearValue();
        
        // .bankHeader
        $this->bankHeader = ([
            'gjournaldt'=>Carbon::now()->format('Y-m-d'),'documentref'=>'','customerid'=>$xxx,'customername'=>''
            ,'addressl1'=>'','addressl2'=>'','addressl3'=>'','amount'=>0,'findiscount'=>0,'fincharge'=>0,'feeamt'=>0,'payby'=>''
            ,'journal'=>'JR','bookid'=>'R1','account'=>'','accountcus'=>'','accounttax'=>'','accountcharge'=>'','accountdis'=>'','accountfee'=>''
            ,'taxscheme'=>'','witholdamt'=>0,'witholdtax'=>0,'witholdtaxrate'=>0,'taxscheme1'=>'','witholdamt1'=>0,'witholdtax1'=>0,'witholdtaxrate1'=>0
            ,'taxtype'=>'2','taxrunningno'=>'','posted'=>false,'department'=>'','notes'=>''      
        ]);

        $data = DB::table('customer')
        ->selectRaw("name, address11, address12, city1 || ' ' || state1 || ' ' || zipcode1 as address3, buyer.account")
        ->Join('buyer', 'customer.customerid', '=', 'buyer.customerid')
        ->where('customer.customerid', $this->bankHeader['customerid'])
        ->get();
        $data = json_decode(json_encode($data), true);

        if (count($data) > 0) {
            $this->bankHeader['customername'] = $data[0]['name'];
            $this->bankHeader['address1'] = $data[0]['address11'];;
            $this->bankHeader['address2'] = $data[0]['address12'];;
            $this->bankHeader['address3'] = $data[0]['address3'];;
            $this->bankHeader['accountcus'] = $data[0]['account'];;
        }
        // /.bankHeader
        
        // .babkDetails
        $data = DB::table('taxdata')
            ->selectRaw("'' as gltran, description, amount-paidamount as balance, 0 as findiscount, 0 as amount, 'B1' as journal
                        , taxnumber as taxref, taxamount as tax, taxdate, 'R1' as bookid, id as taxdataid, amount as totalamount
                        , amount as oriamount, taxamount as oritax")
            ->where('customerid', $this->bankHeader['customerid'])
            ->where('iscancelled', false)
            ->where('purchase', false)
            ->whereRaw('amount > paidamount')
            ->get();
        $this->bankDetails = json_decode(json_encode($data), true);

        for($i=0; $i<count($this->bankDetails);$i++)
        {
            $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'],2);
            $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'],2);
        }
        // /.babkDetails
    }

    public function createUpdateReceiveOnSales()
    {
        if ($this->showEditModal == true){
            DB::transaction(function () {

                // .Table Bank
                DB::statement("UPDATE bank SET gjournaldt=?, payby=?, documentref=?, taxrunningno=?
                            , taxscheme=?, witholdamt=?, witholdtax=?, witholdtaxrate=?
                            , taxscheme1=?, witholdamt1=?, witholdtax1=?, witholdtaxrate1=?
                            , account=?, accountcus=?, accounttax=?, accountcharge=?, accountdis=?, accountfee=?
                            , employee_id=?, transactiondate=?, posted=?
                            where gltran=?" 
                , [$this->bankHeader['gjournaldt'], $this->bankHeader['payby'], $this->bankHeader['documentref'], $this->bankHeader['taxrunningno']
                , $this->bankHeader['taxscheme'], $this->bankHeader['witholdamt'], $this->bankHeader['witholdtax'], $this->bankHeader['witholdtaxrate']
                , $this->bankHeader['taxscheme1'], $this->bankHeader['witholdamt1'], $this->bankHeader['witholdtax1'], $this->bankHeader['witholdtaxrate1']
                , $this->bankHeader['account'], $this->bankHeader['accountcus'], $this->bankHeader['accounttax'], $this->bankHeader['accountcharge']
                , $this->bankHeader['accountdis'], $this->bankHeader['accountfee']
                , 'Admin', Carbon::now(), $this->bankHeader['posted'], $this->bankHeader['gltran']]);
                // /.Table Bank

                // .Table Bankdetail
                foreach ($this->bankDetails as $row)
                {
                    DB::statement("UPDATE bankdetail SET amount=?, employee_id=?, transactiondate=?
                    where id=?" 
                    , [$row['amount'], 'Admin', Carbon::now(), $row['id']]);
                }
                // /.Table Bankdetail

                $this->dispatchBrowserEvent('hide-receiveOnSalesForm');
                $this->dispatchBrowserEvent('alert',['message' => 'Save Successfully!']);
            });

        }else{
            DB::transaction(function () {

                // .Table Bank
                $xGLNumber = getGlNunber('JR');
                DB::statement("INSERT INTO bank(gltran, gjournaldt, documentref, customerid, customername, addressl1, addressl2, addressl3
                , amount, findiscount, fincharge, feeamt, payby, journal, bookid, account, accountcus, accounttax, accountcharge, accountdis
                , accountfee, taxscheme, witholdamt, witholdtax, witholdtaxrate, employee_id, transactiondate)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                , [$xGLNumber, $this->bankHeader['gjournaldt'], $this->bankHeader['documentref'], $this->bankHeader['customerid']
                , $this->bankHeader['customername'], $this->bankHeader['address1'], $this->bankHeader['address2'], $this->bankHeader['address3']
                , $this->bankHeader['amount'], $this->bankHeader['findiscount'], $this->bankHeader['fincharge'], $this->bankHeader['feeamt']
                , $this->bankHeader['payby'], $this->bankHeader['journal'], $this->bankHeader['bookid'], $this->bankHeader['account']
                , $this->bankHeader['accountcus'], $this->bankHeader['accounttax'], $this->bankHeader['accountcharge'], $this->bankHeader['accountdis']
                , $this->bankHeader['accountfee'], $this->bankHeader['taxscheme'], $this->bankHeader['witholdamt'], $this->bankHeader['witholdtax']
                , $this->bankHeader['witholdtaxrate'], 'Admin', Carbon::now()]); 
                // /.Table Bank

                // .Table Bankdetail
                //gltran,description,balance,findiscount,amount,journal(B1),taxref,tax,taxdate,bookid(R1),taxdataid,totalamount
                
                for($i=0; $i<count($this->bankDetails);$i++)
                {
                    if ($this->bankDetails[$i]['amount'] > 0 )
                    {
                        DB::statement("INSERT INTO bankdetail(gltran, description, balance, findiscount, amount, journal, taxref, tax, taxdate
                        , bookid, taxdataid, totalamount, employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$xGLNumber, $this->bankDetails[$i]['description'], $this->bankDetails[$i]['balance']
                        , $this->bankDetails[$i]['findiscount'], $this->bankDetails[$i]['amount'], $this->bankDetails[$i]['journal']
                        , $this->bankDetails[$i]['taxref'], $this->bankDetails[$i]['tax'], $this->bankDetails[$i]['taxdate']
                        , $this->bankDetails[$i]['bookid'], $this->bankDetails[$i]['taxdataid'], $this->bankDetails[$i]['totalamount']
                        , 'Admin', Carbon::now()]); 
                    }                    
                }  
                // /.Table Bankdetail

                $this->dispatchBrowserEvent('hide-receiveOnSalesForm');
                $this->dispatchBrowserEvent('alert',['message' => 'Save Successfully!']);
            });
        }
    }

    public function calPlusDeduct()
    {
        $this->sumPlus = $this->bankHeader['fincharge'];
        $this->sumDeduct = $this->bankHeader['findiscount'] + $this->bankHeader['feeamt'];
    }

    public function updatedBankHeaderFincharge()
    {
        $this->calPlusDeduct();
    }

    public function updatedBankHeaderFindiscount()
    {
        $this->calPlusDeduct();
    }

    public function updatedBankHeaderFeeamt()
    {
        $this->calPlusDeduct();
    }

    public function updatedBankHeaderTaxscheme()
    {
        $this->bankHeader['witholdtaxrate'] = 0;
        $this->bankHeader['witholdtax'] = 0;

        $data = DB::table('taxtable')
        ->select("taxrate")
        ->where('code', $this->bankHeader['taxscheme'])
        ->get();

        if ($data->count() > 0) {
            $this->bankHeader['witholdtaxrate'] = $data[0]->taxrate;
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdamt'] * $this->bankHeader['witholdtaxrate'] / 100, 2);
        }
    }

    public function updatedBankHeaderTaxscheme1()
    {
        $this->bankHeader['witholdtaxrate1'] = 0;
        $this->bankHeader['witholdtax1'] = 0;

        $data = DB::table('taxtable')
        ->select("taxrate")
        ->where('code', $this->bankHeader['taxscheme1'])
        ->get();

        if ($data->count() > 0) {
            $this->bankHeader['witholdtaxrate1'] = $data[0]->taxrate;
            $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdamt1'] * $this->bankHeader['witholdtaxrate1'] / 100, 2);
        }        
    }

    public function updatedBankHeaderWitholdamt1()
    {
        $this->bankHeader['witholdtax1'] = 0;

        if ($this->bankHeader['witholdtaxrate1']) {
            $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdamt1'] * $this->bankHeader['witholdtaxrate1'] / 100, 2);
        }        
    }

    public function updatedBankDetails() //เอาไปรวมกับ calculateSummary แล้ว
    {
        // $this->bankHeader['witholdamt']= 0;
        
        // for($i=0; $i<count($this->bankDetails);$i++)
        // {
        //     $this->bankHeader['witholdamt'] = $this->bankHeader['witholdamt'] + round($this->bankDetails[$i]['amount'] - 
        //                                         ($this->bankDetails[$i]['amount'] * $this->bankDetails[$i]['oritax'] / $this->bankDetails[$i]['oriamount']),2);
        // }

        // if ($this->bankHeader['witholdtaxrate']) {
        //     $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdamt'] * $this->bankHeader['witholdtaxrate'] / 100, 2);
        // }  
    }

    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;
        if ($this->sortDirection == "asc"){
            $this->sortDirection = "desc";
        }else{
            $this->sortDirection = "asc";
        }
    }

    public function addNew()
    {
        $this->showEditModal = FALSE;
        $this->bankHeader = [];
        $this->bankDetails =[];
        
        // $this->bankHeader = ([
        //     'gltran'=>getGlNunber('JR'),'gjournaldt'=>Carbon::now()->format('Y-m-d'),'documentref'=>'','customerid'=>'','customername'=>''
        //     ,'addressl1'=>'','addressl2'=>'','addressl3'=>'','amount'=>0,'findiscount'=>0,'fincharge'=>0,'feeamt'=>0,'payby'=>''
        //     ,'journal'=>'JR','bookid'=>'R1','account'=>'','accountcus'=>'','accounttax'=>'','accountcharge'=>'','accountdis'=>'','accountfee'=>''
        //     ,'taxscheme'=>'','witholdamt'=>0,'witholdtax'=>0,'witholdtaxrate'=>0,'taxscheme1'=>'','witholdamt1'=>0,'witholdtax1'=>0,'witholdtaxrate1'=>0
        //     ,'taxtype'=>'2','taxrunningno'=>'','posted'=>false,'department'=>'','notes'=>''      
        // ]);
        
        $this->addRowInGrid();
        $this->dispatchBrowserEvent('show-receiveOnSalesForm'); //แสดง Model Form
    }

    public function addRowInGrid() //กดปุ่มสร้าง Row ใน Grid
    {   
        //สร้าง Row ว่างๆ ใน Gird
        $this->bankDetails= [];
        // $this->bankDetails[] = ([
        //     'taxref'=>'', 'description'=>'', 'balance'=>0, 'amount'=>0, 'tax'=>0,'oriamount'=>0, 'oritax'=>0
        // ]);
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

    public function edit($gltran)
    {
        $this->showEditModal = TRUE;
        $this->clearValue();

        // .bankHeader
        $data = DB::table('bank')
            ->selectRaw("gltran, to_char(gjournaldt,'YYYY-MM-DD') as gjournaldt, customerid, customername, documentref, amount
                        , taxscheme, witholdamt, witholdtax, witholdtaxrate, taxscheme1, witholdamt1, witholdtax1, witholdtaxrate1
                        , payby, account, accountcus, accounttax, taxrunningno, posted
                        , fincharge, findiscount, feeamt, accountcharge, accountdis, accountfee")
            ->where('gltran', $gltran)
            ->get();
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
        // /.bankHeader

        // .bankDetails
        $data = DB::table('bankdetail')
            ->select('bankdetail.id', 'bankdetail.taxref', 'bankdetail.description', 'bankdetail.balance', 'bankdetail.amount', 'bankdetail.tax'
                    , 'taxdata.amount as oriamount', 'taxdata.taxamount as oritax')
            ->Join('taxdata', 'bankdetail.taxdataid', '=', 'taxdata.id')
            ->where('bankdetail.gltran', $gltran)
            ->get();
        $this->bankDetails = json_decode(json_encode($data), true); 

        for($i=0; $i<count($this->bankDetails);$i++)
        {
            $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'],2);
            $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'],2);
        }
        // /.bankDetails

        $this->dispatchBrowserEvent('show-receiveOnSalesForm'); //แสดง Model Form
    }

    public function confirmDelete($gltran) //แสดง Modal ยืนยันการลบ
    {
        $this->sNumberDelete = $gltran;
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() //กดปุ่ม Delete ที่ List รายการ
    {   
        DB::transaction(function() 
        {
            DB::table('bank')->where('gltran', $this->sNumberDelete)->delete();
            DB::table('bankdetail')->where('gltran', $this->sNumberDelete)->delete();
        });
    }
    
    public function render()
    {
        // .Summary grid     
        if($this->bankDetails != Null)
        {
            $this->calculateSummary();            
        }else{
            $this->clearValue();
        }


        // .Bind Data to Dropdown
        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();
        
        $this->taxTypes_dd = DB::table('taxtable')
        ->select('code','description','taxrate')
        ->where('taxtype','2')
        ->orderBy('code')
        ->get();

        $this->accountNos_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail',TRUE)
        ->orderby('account')
        ->get();
        // /.Bind Data to Dropdown

        // .ใบสำคัญรับเงินที่ยังไม่ ปิดรายการ
        $recieptJournals = DB::table('bank')
        ->select('gltran','gjournaldt','customername','amount')
        ->where('posted', FALSE)            
        ->where('bookid','R1')
        ->Where(function($query) 
        {
            $query->where('gltran', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('gjournaldt', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('customername', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('amount', 'like', '%'.$this->searchTerm.'%');
        })    
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);
        // /.ใบสำคัญรับเงินที่ยังไม่ ปิดรายการ

        return view('livewire.accstar.receive-on-sales',[
            'recieptJournals' => $recieptJournals
        ]);
    }
}
