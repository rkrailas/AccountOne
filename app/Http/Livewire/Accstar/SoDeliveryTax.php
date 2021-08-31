<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SoDeliveryTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $searchTerm = null;
    public $showEditModal = null;
    public $soHeader = []; //sodate,invoiceno,invoicedate,deliveryno,deliverydate,payby,duedate,journaldate,exclusivetax
                           //,taxontotal,salesaccount,taxrate,salestax,discountamount,sototal,customerid,full_address,shipcost
    public $soDetails = []; //itemid,description,quantity,salesac,unitprice,amount,discountamount,netamount,taxrate,taxamount,id,inventoryac
    public $sumQuantity, $sumAmount, $sumDiscountAmount, $sumNetAmount, $sumDebit, $sumCredit = 0;
    public $itemNos_dd, $taxRates_dd, $salesAcs_dd, $customers_dd; //Dropdown
    public $sNumberDelete, $modelMessage;
    public $genGLs = []; //gltran, gjournaldt, glaccount, glaccname, gldescription, gldebit, glcredit, jobid, department
                        //, allcated, currencyid, posted, bookid, employee_id, transactiondate

    public function getGlNunber($bookid)
    {
        $newGlNo = "";
        $data = DB::table('misctable')
                ->select('lastglnumber', 'prefix_lastglnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->lastglnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->prefix_lastglnumber . date_format(now(),"ym")){
                $newGlNo = intval($data2[1]) + 1;
                $newGlNo = $data2[0] . "-" . sprintf("%06d", $newGlNo);

                DB::statement("UPDATE misctable SET lastglnumber=? where tabletype=? and code=?"
                , [$newGlNo,"JR",$bookid]);
            }else{
                $newGlNo = $data[0]->prefix_lastglnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET lastglnumber=? where tabletype=? and code=?"
                , [$newGlNo,"JR",$bookid]);
            }
        }
        return $newGlNo;
    }

    public function getDocNunber($bookid)
    {
        $newDocNo = "";
        $data = DB::table('misctable')
                ->select('lastdocnumber', 'prefix_lastdocnumber')
                ->where('tabletype', 'JR')
                ->where('code', $bookid)
                ->get();
        $data2 = explode("-" , $data[0]->lastdocnumber);        

        if (count($data2)){
            if ($data2[0] == $data[0]->prefix_lastdocnumber . date_format(now(),"ym")){
                $newDocNo = intval($data2[1]) + 1;
                $newDocNo = $data2[0] . "-" . sprintf("%06d", $newDocNo);

                DB::statement("UPDATE misctable SET lastdocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }else{
                $newDocNo = $data[0]->prefix_lastdocnumber . date_format(now(),"ym") . "-000001";

                DB::statement("UPDATE misctable SET lastdocnumber=? where tabletype=? and code=?"
                , [$newDocNo,"JR",$bookid]);
            }
        }
        return $newDocNo;
    }

    public function generateGl()
    {
        // .Concept
            //---Periodic---
            //Dr.ลูกหนี้การค้า
            //  Cr.ขายสินค้า
            //  Cr.ภาษีขาย

            //---Perpetual---
            //Dr.ต้นทุนขาย
            //  Cr.สินค้าคงเหลือ
        // /.Concept
        
        $this->genGLs = [];

        // .Dr.ลูกหนี้การค้า //account = buyer.account or controldef.account where id='AR' //gldebit = $soHeader['sototal']
        $buyAcc = "";
        $buyAccName = "";

        $data = DB::table('buyer')
        ->select("account")
        ->where('customerid', $this->soHeader['customerid'])
        ->get();
        $buyAcc = $data[0]->account;

        if ($buyAcc == ""){
            $data = DB::table('controldef')
            ->select("account")
            ->where('id', 'AR')
            ->get();
            $buyAcc = $data[0]->account;
        }

        if ($buyAcc != ""){          
            $data = DB::table('account')
                ->select("accnameother")
                ->where('account', $buyAcc)
                ->where('detail', true)
                ->get();
            $buyAccName = $data[0]->accnameother;
        }

        $this->genGLs[] = ([
            'gjournal'=>'SO', 'gltran'=>'', 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$buyAcc, 'glaccname'=>$buyAccName
            , 'gldescription'=>'ขายสินค้า' . '-' . $this->soHeader['snumber'], 'gldebit'=>$this->soHeader['sototal'], 'glcredit'=>0, 'jobid'=>''
            , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
            , 'transactiondate'=>Carbon::now()
        ]);
        // /.Dr.ลูกหนี้การค้า 


        // .Cr.ขายสินค้า //glcredit = $soDetails['netamount'] //glaccount = salesdetail.salesac or controldef.account where id='SA'
        $salesAcc = "";
        $salesAccName = "";

        for($i=0; $i<count($this->soDetails);$i++)
        {
            $data = DB::table('salesdetail')
            ->select("salesac")
            ->where('id', $this->soDetails[$i]['id'])
            ->get();
            $salesAcc = $data[0]->salesac;
    
            if ($salesAcc == ""){
                $data = DB::table('controldef')
                ->select("account")
                ->where('id', 'SA')
                ->get();
                $salesAcc = $data[0]->salesac;
            }
    
            if ($salesAcc != ""){
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $salesAcc)
                    ->where('detail', true)
                    ->get();
                $salesAccName = $data[0]->accnameother;
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'SO', 'gltran'=>'', 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$salesAcc, 'glaccname'=>$salesAccName
                , 'gldescription'=>'ขายสินค้า' . '-' . $this->soHeader['snumber'], 'gldebit'=>0, 'glcredit'=>$this->soDetails[$i]['netamount']
                , 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                , 'transactiondate'=>Carbon::now()
            ]);            
        }
        // /.Cr.ขายสินค้า
  
        // .Cr.ภาษีขาย // glcredit = $soHeader['salestax'] // glaccount = controldef.account where id='ST';     
        $taxAcc = "";
        $taxAccName = "";
        
        $data = DB::table('controldef')
        ->select("account")
        ->where('id', 'ST')
        ->get();
        $taxAcc = $data[0]->account;

        if ($taxAcc != ""){          
            $data = DB::table('account')
                ->select("accnameother")
                ->where('account', $taxAcc)
                ->where('detail', true)
                ->get();
            $taxAccName = $data[0]->accnameother;
        }

        $this->genGLs[] = ([
            'gjournal'=>'SO', 'gltran'=>'', 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$taxAcc, 'glaccname'=>$taxAccName
            , 'gldescription'=>'ขายสินค้า' . '-' . $this->soHeader['snumber'], 'gldebit'=>0, 'glcredit'=>$this->soHeader['salestax'], 'jobid'=>''
            , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
            , 'transactiondate'=>Carbon::now()
        ]);
        // /.Cr.ภาษีขาย

        // .Perpetual 
        $data = DB::table('company')
            ->select('perpetual')
            ->limit(1)
            ->get();

        if($data[0]->perpetual){ 
            // .Cr.สินค้าคงเหลือ // select salesdetail.inventoryac Or inventory.inventoryac
            $totalCostAmt = 0;

            for($i=0; $i<count($this->soDetails);$i++)
            {
                $invAcc = "";
                $invAccName = "";

                $invAcc = $this->soDetails[$i]['inventoryac'];

                if ($invAcc == ""){
                    $data = DB::table('inventory')
                    ->select("inventoryac")
                    ->where('itemid', $this->soDetails[$i]['itemid'])
                    ->get();
                    $invAcc = $data[0]->inventoryac;
                }
        
                if ($invAcc != ""){
                    $data = DB::table('account')
                        ->select("accnameother")
                        ->where('account', $invAcc)
                        ->where('detail', true)
                        ->get();
                    $invAccName = $data[0]->accnameother;
                }

                // หาต้นทุนสินค้า
                $data = DB::table('inventory')
                ->select("averagecost")
                ->where('itemid', $this->soDetails[$i]['itemid'])
                ->get();
                $costAmt = round($this->soDetails[$i]['quantity'] * $data[0]->averagecost, 2);
                $totalCostAmt = $totalCostAmt + $costAmt;
        
                $this->genGLs[] = ([
                    'gjournal'=>'SO', 'gltran'=>'', 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$invAcc, 'glaccname'=>$invAccName
                    , 'gldescription'=>'ขายสินค้า' . '-' . $this->soHeader['snumber'], 'gldebit'=>0, 'glcredit'=>$costAmt
                    , 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                    , 'transactiondate'=>Carbon::now()
                ]);  
            }
            // /.Cr.สินค้าคงเหลือ

            // .Dr.ต้นทุนขาย controldef.account where id='CG'
            $costAcc = "";
            $costAccName = "";

            $data = DB::table('controldef')
            ->select("account")
            ->where('id', 'CG')
            ->get();
            $costAcc = $data[0]->account;
    
            if ($costAcc != ""){          
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $costAcc)
                    ->where('detail', true)
                    ->get();
                $costAccName = $data[0]->accnameother;
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'SO', 'gltran'=>'', 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$costAcc, 'glaccname'=>$costAccName
                , 'gldescription'=>'ขายสินค้า' . '-' . $this->soHeader['snumber'], 'gldebit'=>$totalCostAmt, 'glcredit'=>0, 'jobid'=>''
                , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                , 'transactiondate'=>Carbon::now()
            ]);
            // /.Dr.ต้นทุนขาย 
        }
        // /.Perpetual 

        // Summary Debit & Credit
        for($i=0; $i<count($this->genGLs);$i++)
        {
            $this->sumDebit = $this->sumDebit + $this->genGLs[$i]['gldebit'];
            $this->sumCredit = $this->sumCredit + $this->genGLs[$i]['glcredit'];
        }

        $this->dispatchBrowserEvent('show-myModal2'); //แสดง Model Form
    }

    public function addNew() //จากการกดปุ่ม สร้างข้อมูลใหม่
    {
        $this->showEditModal = FALSE;
        $this->soHeader = [];
        $this->soHeader = ([
            'snumber'=>'', 'sonumber'=>'', 'sodate'=>Carbon::now()->format('Y-m-d'), 'invoiceno'=>'', 'invoicedate'=>Carbon::now()->format('Y-m-d')
            , 'deliveryno'=>'', 'deliverydate'=>Carbon::now()->addMonth()->format('Y-m-d'),'payby'=>'0', 'duedate'=>Carbon::now()->addMonth()->format('Y-m-d')
            , 'journaldate'=>Carbon::now()->format('Y-m-d'), 'exclusivetax'=>TRUE, 'taxontotal'=>FALSE, 'salesaccount'=>'', 'taxrate'=>7
            , 'salestax'=>0, 'discountamount'=>0, 'sototal'=>0, 'customerid'=>'', 'shipcost'=>0, 'shipname'=>''
        ]); //เป็น Array 1 มิติ

        $this->soDetails =[];
        $this->addRowInGrid();
        $this->dispatchBrowserEvent('show-soDeliveryTaxForm'); //แสดง Model Form
    }

    public function removeRowInGrid($index) //กดปุ่มลบ Row ใน Grid
    {        
        unset($this->soDetails[$index]);
    }

    public function addRowInGrid() //กดปุ่มสร้าง Row ใน Grid
    {   
        //สร้าง Row ว่างๆ ใน Gird
        $this->soDetails[] = ([
            'itemid'=>'','description'=>'','quantity'=>0,'salesac'=>'','unitprice'=>0
            ,'amount'=>0,'discountamount'=>0,'netamount'=>0, 'taxamount'=>0
        ]);
    }

    public function createUpdateSalesOrder() //Event จากปุ่ม Save 
    {   
        //??? สิ่งที่ยังขาด
        //- ยังไม่รองรับ Posted=true พร้อมทั้งบันทึกบัญชี
        if ($this->showEditModal == true){
            DB::transaction(function () {
                // Table "Sales"
                DB::statement("UPDATE sales SET sodate=?, invoiceno=?, invoicedate=?, deliveryno=?, deliverydate=?, sototal=?, salestax=?
                , payby=?, duedate=?, journaldate=?, exclusivetax=?, taxontotal=?, salesaccount=?, employee_id=?, transactiondate=?
                where snumber=?" 
                , [$this->soHeader['sodate'], $this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['deliveryno']
                , $this->soHeader['deliverydate'], $this->soHeader['sototal'], $this->soHeader['salestax'], $this->soHeader['payby']
                , $this->soHeader['duedate'], $this->soHeader['journaldate'], $this->soHeader['exclusivetax'], $this->soHeader['taxontotal']
                , $this->soHeader['salesaccount'], 'Admin', Carbon::now(), $this->soHeader['snumber']]);
            
                // Table "SalesDetail" 
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();
                foreach ($this->soDetails as $soDetails2)
                {
                    DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, quantity, unitprice, amount, quantityord, quantitydel
                    , quantitybac, taxrate, taxamount, discountamount, soreturn, salesac, employee_id, transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description']
                        , $soDetails2['quantity'], $soDetails2['unitprice'], $soDetails2['amount'], $soDetails2['quantity']
                        , 0, $soDetails2['quantity'], $this->soHeader['taxrate'], $this->soHeader['salestax'], $this->soHeader['discountamount'], 'N'
                        , $soDetails2['salesac'], 'Admin', Carbon::now()]);
                }

                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm');
                $this->dispatchBrowserEvent('alert',['message' => 'Save Successfully!']);
            });
        }else{
            $this->soHeader['snumber'] = $this->getDocNunber("SO");
            $this->soHeader['sonumber'] = $this->soHeader['snumber'];
            $this->soHeader['invoiceno'] = $this->getGLNunber("SO");
            
            DB::transaction(function () {
                // Table "Sales"
                DB::statement("INSERT INTO sales(snumber, sonumber, sdate, customerid, invoiceno, invoicedate
                            , deliveryno, deliverydate, payby, duedate, journaldate, exclusivetax, taxontotal
                            , salesaccount, expirydate, sototal, salestax, employee_id, transactiondate)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                , [$this->soHeader['snumber'], $this->soHeader['sonumber'], $this->soHeader['sodate'], $this->soHeader['customerid']
                , $this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['deliveryno'], $this->soHeader['deliverydate']
                , $this->soHeader['payby'], $this->soHeader['duedate'], $this->soHeader['journaldate'], $this->soHeader['exclusivetax']
                , $this->soHeader['taxontotal'], $this->soHeader['salesaccount'], Carbon::now()->addMonths(6), $this->soHeader['sototal']
                , $this->soHeader['salestax'], 'Admin', Carbon::now()]);

                // Table "SalesDetail" 
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();
                foreach ($this->soDetails as $soDetails2)
                {
                    DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, quantity, unitprice, amount, quantityord, quantitydel
                    , quantitybac, taxrate, taxamount, discountamount, soreturn, salesac, employee_id, transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description']
                        , $soDetails2['quantity'], $soDetails2['unitprice'], $soDetails2['amount'], $soDetails2['quantity']
                        , 0, $soDetails2['quantity'], $this->soHeader['taxrate'], $this->soHeader['salestax'], $this->soHeader['discountamount'], 'N'
                        , $soDetails2['salesac'], 'Admin', Carbon::now()]);
                }

                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm');
                $this->dispatchBrowserEvent('alert',['message' => 'Save Successfully!']);
            });
        };
    }

    public function updated($item) //Event จากการ Update Property ของ Livewire มันจะส่ง Property หรือตัวแปรที่มีการ update มาให้ เช่น $soHeader, $soDetails
    {
        $xxx = explode(".",$item); //$item = soHeader.sodate หรือ soDetails.0.quantity

        //ตรวจสอบว่าเป็นการ Update Dropdown ของลูกค้าหรือไม่ ถ้าใช่จะเอาที่อยู่มาใส่ให้
        if ($item == "soHeader.customerid") {
            $data = DB::table('customer')
            ->selectRaw("COALESCE(address11,'') || ' ' || COALESCE(address12,'') || ' ' ||
                        COALESCE(city1,'') || ' ' || COALESCE(state1,'') || ' ' || COALESCE(zipcode1,'') as full_address")
            ->where('customerid', $this->soHeader['customerid'])
            ->get();
            $this->soHeader['full_address'] = $data[0]->full_address;
        }

        //ตรวจสอบว่าเป็นการแก้ไขข้อมูลที่ Grid หรือไม่
        if($xxx[0] == "soDetails") 
        {
            $index = $xxx[1];
            $itemName = $xxx[2];
    
            //Get new item description
            if ($itemName == "itemid")
            {
                $data = DB::table('inventory')
                    ->select('description')
                    ->where('itemid', $this->soDetails[$index][$itemName]) 
                    ->first();
                $data = json_decode(json_encode($data), true); 
                $this->soDetails[$index]['description'] = $data['description'];
            }

            //ตรวจสอบว่าเป้นการแก้ไข quantity หรือ unitprice หรือ discountamount
            if ($itemName == "quantity" || $itemName == "unitprice" || $itemName == "discountamount")
                {
                    try {
                        $this->soDetails[$index]['amount'] = round($this->soDetails[$index]['quantity'] * $this->soDetails[$index]['unitprice'],2);
                        $this->soDetails[$index]['netamount'] = round($this->soDetails[$index]['amount'] - $this->soDetails[$index]['discountamount'],2);
                        if ($this->soHeader['exclusivetax'] == TRUE) 
                        {
                            $this->soDetails[$index]['taxamount'] = round($this->soDetails[$index]['netamount'] * $this->soHeader['taxrate'] / 100,2);
                            
                        }else{
                            $this->soDetails[$index]['taxamount'] = round($this->soDetails[$index]['netamount'] * ($this->soHeader['taxrate'] / 
                                                                            (100 + $this->soHeader['taxrate'])) ,2);
                        }
                        //หลังจาก Re-Cal รายบรรทัดเสร็จ มันจะไปเข้า function reCalculateSummary ที่ render                        
                    } catch (\Throwable $th) {
                        return false;
                    }          
                }
        }        
    }

    public function checkExclusiveTax()
    {
        //ReCal Vat รายบรรทัดใหม่
        for($i=0; $i<count($this->soDetails);$i++)
        {
            if ($this->soHeader['exclusivetax']==TRUE) //VAT นอก
            {
                $this->soDetails[$i]['taxamount'] = round($this->soDetails[$i]['netamount'] * $this->soDetails[$i]['taxrate'] / 100,2);
            }else{
                $this->soDetails[$i]['taxamount'] = round($this->soDetails[$i]['netamount'] * ($this->soDetails[$i]['taxrate'] / 
                                                    (100 + $this->soDetails[$i]['taxrate'])) ,2);
            }
        }
    }

    public function checkTaxOnTotal()
    {
        //ไม่ต้องทำอะไร มันจะไปเข้า Function render ต่อไป
    }

    public function reCalculateSummary()
    {
        // Summary Gird
        $this->sumQuantity = array_sum(array_column($this->soDetails,'quantity'));
        $this->sumAmount = array_sum(array_column($this->soDetails,'amount'));
        $this->sumDiscountAmount = array_sum(array_column($this->soDetails,'discountamount'));
        $this->sumNetAmount = array_sum(array_column($this->soDetails,'netamount'));

        // .Summary Page
        $this->soHeader['discountamount'] =  round(array_sum(array_column($this->soDetails,'discountamount')),2);
        
        // soHeader['salestax']
        if ($this->soHeader['taxontotal'] == TRUE) //ภาษีจากยอดรวม
        {
            if($this->soHeader['exclusivetax']==TRUE){
                $this->soHeader['salestax'] = round(array_sum(array_column($this->soDetails,'netamount')) * $this->soHeader['taxrate'] / 100,2);
            }else{
                $this->soHeader['salestax'] = round(array_sum(array_column($this->soDetails,'netamount')) * ($this->soHeader['taxrate'] / 
                                                (100 + $this->soHeader['taxrate'])) ,2);                
            }            
        }else{
            $this->soHeader['salestax'] = round(array_sum(array_column($this->soDetails,'taxamount')),2);
        }
        
        // soHeader['sototal']
        if($this->soHeader['exclusivetax']==TRUE)
        {
            //VAT นอก
            $this->soHeader['sototal'] = round(array_sum(array_column($this->soDetails,'netamount')) + $this->soHeader['salestax'], 2);            
        }else{
            //VAT ใน
            $this->soHeader['sototal'] = round(array_sum(array_column($this->soDetails,'netamount')));   
        }
        // /.Summary in Page
    }

    public function confirmDelete($snumber) //แสดง Modal ยืนยันการลบใบสั่งขาย
    {
        $this->modelMessage = "คุณต้องการลบใบสั่งขายเลขที่: " . $snumber;
        $this->sNumberDelete = $snumber;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function delete() //Event จากการกดปุ่ม Delete ที่ List รายการ
    {   
        // $this->dispatchBrowserEvent('hide-delete-modal');
        // $this->modelMessage = "555555555";
        // $this->dispatchBrowserEvent('show-infor-modal');

        DB::transaction(function() 
        {
            DB::table('sales')->where('snumber', $this->sNumberDelete)->delete();
            DB::table('salesdetail')->where('snumber', $this->sNumberDelete)->delete();
            $this->dispatchBrowserEvent('hide-delete-modal', ['message' => 'Deleted successfully!']);
        });
    }

    public function edit($sNumber) //Event จากการกดปุ่ม Edit ที่ List รายการ
    {
        $this->showEditModal = TRUE;

        // .soHeader
        $data = DB::table('sales')
            ->selectRaw("snumber,to_char(sodate,'YYYY-MM-DD') as sodate, invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate
                        , deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate, payby
                        , CONCAT(customer.customerid,': ', customer.name) as shipname
                        , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                        , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                        , taxontotal, posted, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost")
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('snumber', $sNumber)
            ->where('soreturn', 'N')
            ->get();
        $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
        $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
        $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
        $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);  
        // ./soHeader
        
        // .soDetails
        $data2 = DB::table('salesdetail')
            ->select('itemid','description','quantity','salesac','unitprice','discountamount','taxrate','taxamount','id','inventoryac')
            ->where('snumber', $sNumber)
            ->where('soreturn', 'N')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true); 

        for($i=0; $i<count($this->soDetails);$i++)
        {
            $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity'] * $this->soDetails[$i]['unitprice'],2);
            $this->soDetails[$i]['quantity'] = round($this->soDetails[$i]['quantity'],2);
            $this->soDetails[$i]['unitprice'] = round($this->soDetails[$i]['unitprice'],2);
            $this->soDetails[$i]['discountamount'] = round($this->soDetails[$i]['discountamount'],2);
            $this->soDetails[$i]['netamount'] = round($this->soDetails[$i]['amount'] - $this->soDetails[$i]['discountamount'],2);
        }
        // ./soDetails

    
        $this->dispatchBrowserEvent('show-soDeliveryTaxForm'); //แสดง Model Form
    }

    public function updatingSearchTerm() //Event นี้เกิดจากการ Key ที่ input wire:model.lazy="searchTerm"
    {
        $this->resetPage();
    }

    public function render()
    {
        // .Summary grid     
        if($this->soDetails != Null)
        {            
            $this->reCalculateSummary();
        }else{
            $this->sumQuantity = 0;
            $this->sumAmount = 0;
            $this->sumDiscountAmount = 0;
            $this->sumNetAmount = 0;
            $this->soHeader['discountamount'] = 0;
            $this->soHeader['shipcost'] = 0;
            $this->soHeader['salestax'] = 0;
            $this->soHeader['sototal'] = 0;  
        }
        // ./Summary grid 
        
        // .Bind Data to Dropdown
        $this->itemNos_dd = DB::table('inventory')
        ->select('itemid','description')
        ->orderby('itemid')
        ->get();

        $this->salesAcs_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail',TRUE)
        ->orderby('account')
        ->get();

        $this->taxRates_dd = DB::table('taxtable')
        ->select('code','taxrate')
        ->where('taxtype','1')
        ->orderby('code')
        ->get();

        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();
        // ./Bind Data to Dropdown

        // .getSalesOrder
        $salesOrders = DB::table('sales')
            ->select('sales.id','snumber','sodate','name','sototal', 'sales.sodate')
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('posted', FALSE)            
            ->where('soreturn','N')
            ->whereIn('snumber',function ($query) {
                $query->select('snumber')->from('salesdetail')
                ->Where('quantitybac', '>' , 0);
            })        
            ->Where(function($query) 
                {
                    $query->where('snumber', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('sodate', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('name', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('sototal', 'like', '%'.$this->searchTerm.'%');
                })
            ->orderBy('sales.sodate','desc')
            ->paginate(10);
        // /.getSalesOrder

        return view('livewire.accstar.so-delivery-tax',[
            'salesOrders' => $salesOrders
        ]);
    }
}