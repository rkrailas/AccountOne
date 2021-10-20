<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SoDelivery extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $sortDirection = "desc";
    public $sortBy = "sales.snumber";
    public $numberOfPage = 10;
    public $searchTerm = null;
    
    public $showEditModal = null;
    public $soHeader = []; //snumber,sonumber,sodate,expirydate,deliverydate,refno,exclusivetax
                        //,taxontotal,salesaccount,taxrate,salestax,discountamount,sototal,customerid,full_address,shipcost,posted
    public $soDetails = []; //itemid,description,quantity,salesac,unitprice,amount,discountamount,netamount,taxrate,taxamount,id,inventoryac
    public $sumQuantity, $sumAmount = 0;
    public $itemNos_dd, $taxRates_dd, $salesAcs_dd, $customers_dd; //Dropdown
    public $sNumberDelete;
    public $genGLs = []; //gltran, gjournaldt, glaccount, glaccname, gldescription, gldebit, glcredit, jobid, department
                        //, allcated, currencyid, posted, bookid, employee_id, transactiondate
    public $sumDebit, $sumCredit = 0;

    public $closed = false;

    public function updatingNumberOfPage()
    {
        $this->resetPage();
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

    public function showGL()
    {
        $this->generateGl();
        $this->dispatchBrowserEvent('show-myModal2'); //Display Model
    }

    public function generateGl($xgltran = '')
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
        $this->sumDebit = 0;
        $this->sumCredit = 0;

        // .Dr.ลูกหนี้การค้า //account = buyer.account or controldef.account where id='AR' //gldebit = $soHeader['sototal']
        $buyAcc = "";
        $buyAccName = "";

        $data = DB::table('buyer')
        ->select("account")
        ->where('customerid', $this->soHeader['customerid'])
        ->get();
        if ($data->count() > 0) {
            $buyAcc = $data[0]->account;
        }         

        if ($buyAcc == ""){
            $data = DB::table('controldef')
            ->select("account")
            ->where('id', 'AR')
            ->get();            
            if ($data->count() > 0) {
                $buyAcc = $data[0]->account;
            } 
        }

        if ($buyAcc != ""){          
            $data = DB::table('account')
                ->select("accnameother")
                ->where('account', $buyAcc)
                ->where('detail', true)
                ->get();            
            if ($data->count() > 0) {
                $buyAccName = $data[0]->accnameother;
            } 
        }

        $this->genGLs[] = ([
            'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$buyAcc, 'glaccname'=>$buyAccName
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
            if ($data->count() > 0) {
                $salesAcc = $data[0]->salesac;
            } 
    
            if ($salesAcc == ""){
                $data = DB::table('controldef')
                ->select("account")
                ->where('id', 'SA')
                ->get();                
                if ($data->count() > 0) {
                    $salesAcc = $data[0]->account;
                }  
            }
    
            if ($salesAcc != ""){
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $salesAcc)
                    ->where('detail', true)
                    ->get();                
                if ($data->count() > 0) {
                    $salesAccName = $data[0]->accnameother;
                }  
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$salesAcc, 'glaccname'=>$salesAccName
                , 'gldescription'=>'ขายสินค้า' . '-' . $this->soHeader['snumber'], 'gldebit'=>0
                , 'glcredit'=>$this->soDetails[$i]['netamount']-$this->soDetails[$i]['taxamount']
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
        if ($data->count() > 0) {
            $taxAcc = $data[0]->account;
        }          

        if ($taxAcc != ""){          
            $data = DB::table('account')
                ->select("accnameother")
                ->where('account', $taxAcc)
                ->where('detail', true)
                ->get();
            if ($data->count() > 0) {
                $taxAccName = $data[0]->accnameother;
            }            
        }

        $this->genGLs[] = ([
            'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$taxAcc, 'glaccname'=>$taxAccName
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
                    if ($data->count() > 0) {
                        $invAcc = $data[0]->inventoryac;
                    }                    
                }
        
                if ($invAcc != ""){
                    $data = DB::table('account')
                        ->select("accnameother")
                        ->where('account', $invAcc)
                        ->where('detail', true)
                        ->get();
                    if ($data->count() > 0) {
                        $invAccName = $data[0]->accnameother;
                    }
                }

                // หาต้นทุนสินค้า
                $data = DB::table('inventory')
                ->select("averagecost")
                ->where('itemid', $this->soDetails[$i]['itemid'])
                ->get();
                if ($data->count() > 0) {
                    $costAmt = round($this->soDetails[$i]['quantity'] * $data[0]->averagecost, 2);
                }else{
                    $costAmt = 0;
                }                
                $totalCostAmt = $totalCostAmt + $costAmt;
        
                $this->genGLs[] = ([
                    'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$invAcc, 'glaccname'=>$invAccName
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
            if ($data->count() > 0) {
                $costAcc = $data[0]->account;
            }              
    
            if ($costAcc != ""){          
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $costAcc)
                    ->where('detail', true)
                    ->get();
                if ($data->count() > 0) {
                    $costAccName = $data[0]->accnameother;
                }                  
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$costAcc, 'glaccname'=>$costAccName
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
    }

    public function createUpdateSalesOrder() //Save Button 
    {   
        if ($this->showEditModal == true){
            DB::transaction(function () {
                // Update Sales (I think that shouldn't change)
                // DB::statement("UPDATE sales SET sototal=?, salestax=?, employee_id=?, transactiondate=?
                // where snumber=?" 
                // , [$this->soHeader['sototal'], $this->soHeader['salestax'], 'Admin', Carbon::now()
                // , $this->soHeader['snumber']]);
        
                // SalesDetail & SalesDetaillog
                foreach ($this->soDetails as $soDetails2)
                {
                    if ($this->soHeader['exclusivetax'] == true) //แปลงค่าก่อนบันทึก
                    {
                        $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                    }

                    //ปิดรายการ
                    if($this->closed == true){
                        $xquantity = $soDetails2['quantitybac'] - $soDetails2['quantity'];
                        $xquantitydel = $soDetails2['quantitydel'] + $soDetails2['quantity'];
                        $quantitybac = $soDetails2['quantityord'] - $xquantitydel;
                        
                        //Updaate SalesDetail
                        DB::statement("UPDATE salesdetail SET quantity=?, quantitydel=?, quantitybac=?, employee_id=?, transactiondate=?
                            where id=?" 
                            , [$xquantity, $xquantitydel, $quantitybac, 'Admin', Carbon::now(), $soDetails2['id']]);

                        //Insert SalesDetailLog
                        //Product Cost
                        $costAmt = 0;
                        $xinventory = DB::table('inventory')
                            ->select('instock','instockvalue','averagecost')
                            ->where('itemid', $soDetails2['itemid'])
                            ->get();
                        if ($xinventory->count() > 0) {
                            $costAmt = round($soDetails2['quantity'] * $xinventory[0]->averagecost, 2);
                        }

                        DB::statement("INSERT INTO salesdetaillog(snumber, sdate, deliveryno, itemid, description
                            , quantity, unitprice, amount, quantityord, quantitydel, quantitybac, taxrate, taxamount
                            , discountamount, cost, soreturn, journal, posted, employee_id, transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            , [$this->soHeader['snumber'], $this->soHeader['sodate'], $this->soHeader['deliveryno'], $soDetails2['itemid'], $soDetails2['description']
                            , $soDetails2['quantity'], $soDetails2['unitprice'], $soDetails2['amount'],0 , 0, 0, $soDetails2['taxrate'], $soDetails2['taxamount']
                            , $soDetails2['discountamount'], $costAmt, 'G', 'SO', true, 'Admin', Carbon::now()]);

                        // Update Inventory
                        $xinstock = $xinventory[0]->instock - $soDetails2['quantity'];
                        $xinstockvalue = $xinventory[0]->instockvalue - round($soDetails2['quantity'] * $xinventory[0]->averagecost, 2);

                        DB::statement("UPDATE inventory SET instock=?, instockvalue=?, employee_id=?, transactiondate=?
                            where itemid=?" 
                            , [$xinstock, $xinstockvalue, 'Admin', Carbon::now(), $soDetails2['itemid']]);

                        // gltran ไป Insert ตอนใบกำกับภาษีเลย
                        // $this->generateGl(getGlNunber('SO'));
                        // DB::table('gltran')->insert($this->genGLs);
                    }else{
                        //Update Salesdetail
                        DB::statement("UPDATE salesdetail SET quantity=?, amount=?, taxamount=?, soreturn=?, employee_id=?, transactiondate=?
                        where id=?" 
                        , [$soDetails2['quantity'], $soDetails2['amount'], $soDetails2['taxamount']
                        , 'G', 'Admin', Carbon::now(), $soDetails2['id']]);
                    }
                }

                $this->dispatchBrowserEvent('hide-soDeliveryForm',['message' => 'Save Successfully!']);
            });
        }else{
            //This event does not exist
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
            if(count($data) > 0){
                $this->soHeader['full_address'] = $data[0]->full_address;
            }
            
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
            if ($itemName == "quantity" || $itemName == "unitprice" || $itemName == "discountamount" || $itemName == "taxrate")
                {
                    $this->reCalculateInGrid();    
                }
        }        
    }

    public function reCalculateInGrid()
    {
        for($i=0; $i<count($this->soDetails);$i++)
        {
            try {
                //$this->soDetails[$index]['amount'] ยอดก่อน VAT และส่วนลด
                //$this->soDetails[$index]['netamount'] ยอดรวม VAT หักส่วนลด
                if ($this->soHeader['exclusivetax'] == TRUE) 
                {
                    $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity'] * $this->soDetails[$i]['unitprice'],2);
                    
                }else{
                    $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity'] * 
                                ($this->soDetails[$i]['unitprice']-($this->soDetails[$i]['unitprice'] * 7 / 107)),2);
                }

                $this->soDetails[$i]['taxamount'] = round(($this->soDetails[$i]['amount'] - $this->soDetails[$i]['discountamount'])
                                            * $this->soDetails[$i]['taxrate'] / 100,2);

                $this->soDetails[$i]['netamount'] = round($this->soDetails[$i]['amount'] + $this->soDetails[$i]['taxamount']
                                                    - $this->soDetails[$i]['discountamount'],2);
                $this->soDetails[$i]['quantity'] = round($this->soDetails[$i]['quantity'],2);
                $this->soDetails[$i]['unitprice'] = round($this->soDetails[$i]['unitprice'],2);
                $this->soDetails[$i]['discountamount'] = round($this->soDetails[$i]['discountamount'],2);
                $this->soDetails[$i]['taxrate'] = round($this->soDetails[$i]['taxrate'],2);

                //หลังจาก Re-Cal รายบรรทัดเสร็จ มันจะไปเข้า function reCalculateSummary ที่ render                        
            } catch (\Throwable $th) {
                return false;
            }          
        }
    }

    public function checkExclusiveTax()
    {
        $this->reCalculateInGrid();
    }

    public function reCalculateSummary()
    {
        // Summary Gird
        $this->sumQuantity = array_sum(array_column($this->soDetails,'quantity'));
        $this->sumAmount = array_sum(array_column($this->soDetails,'amount'));
        $this->soHeader['discountamount'] = array_sum(array_column($this->soDetails,'discountamount'));
        $this->soHeader['sototal'] = array_sum(array_column($this->soDetails,'netamount'));
        $this->soHeader['salestax'] = round(array_sum(array_column($this->soDetails,'taxamount')),2);
    }

    public function confirmDelete($snumber) //แสดง Modal ยืนยันการลบใบสั่งขาย
    {
        $this->sNumberDelete = $snumber;
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() //กดปุ่ม Delete ที่ List รายการ
    {   
        DB::transaction(function() 
        {
            DB::table('sales')->where('snumber', $this->sNumberDelete)->delete();
            DB::table('salesdetail')->where('snumber', $this->sNumberDelete)->delete();
        });
    }

    public function edit($sNumber) //กดปุ่ม Edit ที่ List รายการ
    {
        $this->showEditModal = TRUE;

        // soHeader
        $data = DB::table('sales')
            ->selectRaw("snumber,to_char(sodate,'YYYY-MM-DD') as sodate, invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate
                        , deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate, payby, refno
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
        $this->soHeader['duedate'] = Carbon::now()->format('Y-m-d');
        $this->soHeader['journaldate'] = Carbon::now()->format('Y-m-d');
        
        // .soDetails
        $data2 = DB::table('salesdetail')
            ->select('itemid','description','quantity','quantitybac','quantitydel','quantityord','salesac','unitprice'
                    ,'discountamount','taxrate','taxamount','id','inventoryac')
            ->where('snumber', $sNumber)
            ->whereIn('soreturn', ['N','G'])
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true); 

        $this->reCalculateInGrid();
        // ./soDetails

    
        $this->dispatchBrowserEvent('show-soDeliveryForm'); //แสดง Model Form
        $this->dispatchBrowserEvent('clear-select2');
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
            $this->soHeader['discountamount'] = 0;
            $this->soHeader['salestax'] = 0;
            $this->soHeader['sototal'] = 0;
            $this->soHeader['customerid'] = "";
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
            ->select('sales.id','snumber','sodate','name','sototal')
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('posted', FALSE)
            ->where('soreturn','N')
            ->where('closed',TRUE)
            ->where('expirydate', '>', Carbon::now()->addMonth(-1))
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
            ->orderBy($this->sortBy,$this->sortDirection)
            ->paginate($this->numberOfPage);
        // /.getSalesOrder

        return view('livewire.accstar.sales.so-delivery',[
            'salesOrders' => $salesOrders
        ]);
    }
}