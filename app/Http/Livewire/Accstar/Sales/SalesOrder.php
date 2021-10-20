<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalesOrder extends Component
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

    public function addNew()
    {
        getGlNunber("SO");

        $this->showEditModal = FALSE;
        $this->soHeader = [];
        $this->soHeader = ([
            'snumber'=>'', 'sonumber'=>'', 'sodate'=>Carbon::now()->format('Y-m-d'), 'expirydate'=>Carbon::now()->addMonth()->format('Y-m-d')
            , 'deliveryno'=>'', 'deliverydate'=>Carbon::now()->addMonth()->format('Y-m-d'), 'refno'=>'', 'payby'=>'0'
            , 'exclusivetax'=>TRUE, 'taxontotal'=>FALSE, 'salesaccount'=>'', 'taxrate'=>7
            , 'salestax'=>0, 'discountamount'=>0, 'sototal'=>0, 'customerid'=>'', 'shipcost'=>0, 'shipname'=>'', 'closed'=>false
            , 'duedate'=>Carbon::now()->addMonth()->format('Y-m-d')
        ]);
        $this->soHeader['snumber'] = getDocNunber("SO");
        $this->soHeader['sonumber'] = getDocNunber("SO");

        $this->soDetails =[];
        $this->addRowInGrid();
        $this->dispatchBrowserEvent('show-SalesOrderForm'); //แสดง Model Form
        $this->dispatchBrowserEvent('clear-select2');
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
            ,'amount'=>0,'discountamount'=>0,'netamount'=>0, 'taxamount'=>0, 'taxrate'=>$this->soHeader['taxrate']
        ]);
    }

    public function createUpdateSalesOrder() //กดปุ่ม Save 
    {   
        if ($this->showEditModal == true){
            DB::transaction(function () {
                // .Table "Sales"
                DB::statement("UPDATE sales SET sodate=?, deliverydate=?, expirydate=?, refno=?, sototal=?, salestax=?
                , exclusivetax=?, taxontotal=?, salesaccount=?, employee_id=?, transactiondate=?, closed=?, duedate=?
                where snumber=?" 
                , [$this->soHeader['sodate'], $this->soHeader['deliverydate'], $this->soHeader['expirydate']
                , $this->soHeader['refno'], $this->soHeader['sototal'], $this->soHeader['salestax']
                , $this->soHeader['exclusivetax'], $this->soHeader['taxontotal'], $this->soHeader['salesaccount']
                , 'Admin', Carbon::now(), $this->soHeader['closed'], $this->soHeader['duedate'], $this->soHeader['snumber']]);
            
                // Table "SalesDetail" 
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();
                foreach ($this->soDetails as $soDetails2)
                {
                    if ($this->soHeader['exclusivetax'] == true) //แปลงค่าก่อนบันทึก
                    {
                        $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                    }

                    $xquantity = $soDetails2['quantity'];
                    $xquantityord = $soDetails2['quantity'];
                    $xquantitydel = 0;
                    $xquantitybac = $soDetails2['quantity'];

                    DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount
                    , quantity, quantityord, quantitydel, quantitybac
                    , taxrate, taxamount, discountamount, soreturn, salesac, employee_id, transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description'], $soDetails2['unitprice'], $soDetails2['amount']
                    , $xquantity, $xquantityord, $xquantitydel, $xquantitybac
                    , $soDetails2['taxrate'], $soDetails2['taxamount'], $soDetails2['discountamount'], 'N'
                    , $soDetails2['salesac'], 'Admin', Carbon::now()]);
                }
  
                $this->dispatchBrowserEvent('hide-SalesOrderForm',['message' => 'Save Successfully!']);
            });
        }else{
            $this->soHeader['snumber'] = getDocNunber('SO');
            $this->soHeader['sonumber'] = $this->soHeader['snumber'];

            DB::transaction(function () {
                // Table "Sales"
                DB::statement("INSERT INTO sales(snumber, sonumber, sodate, customerid, expirydate, deliverydate, refno
                            , exclusivetax, taxontotal, salesaccount, sototal, salestax, closed, duedate, employee_id, transactiondate)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                , [$this->soHeader['snumber'], $this->soHeader['sonumber'], $this->soHeader['sodate'], $this->soHeader['customerid']
                , $this->soHeader['expirydate'], $this->soHeader['deliverydate'], $this->soHeader['refno']
                , $this->soHeader['exclusivetax'], $this->soHeader['taxontotal'], $this->soHeader['salesaccount']
                , $this->soHeader['sototal'], $this->soHeader['salestax'], $this->soHeader['closed'], $this->soHeader['duedate']
                , 'Admin', Carbon::now()]);

                // Table "SalesDetail"
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();

                foreach ($this->soDetails as $soDetails2)
                {
                    if ($this->soHeader['exclusivetax'] == true) //แปลงค่าก่อนบันทึก
                    {
                        $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                    }

                    $xquantity = $soDetails2['quantity'];
                    $xquantityord = $soDetails2['quantity'];
                    $xquantitydel = 0;
                    $xquantitybac = $soDetails2['quantity'];

                    DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount
                    , quantity, quantityord, quantitydel, quantitybac
                    , taxrate, taxamount, discountamount, soreturn, salesac, employee_id, transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description'], $soDetails2['unitprice'], $soDetails2['amount']
                    , $xquantity, $xquantityord, $xquantitydel, $xquantitybac
                    , $soDetails2['taxrate'], $soDetails2['taxamount'], $soDetails2['discountamount'], 'N'
                    , $soDetails2['salesac'], 'Admin', Carbon::now()]);
                }

                $this->dispatchBrowserEvent('hide-SalesOrderForm',['message' => 'Save Successfully!']);
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

        $data = DB::table('sales')
            ->selectRaw("snumber,to_char(sodate,'YYYY-MM-DD') as sodate, deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate
                    , to_char(expirydate,'YYYY-MM-DD') as expirydate, refno, payby
                    , CONCAT(customer.customerid,': ', customer.name) as shipname
                    , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                    , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                    , taxontotal, posted, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost, closed
                    , to_char(duedate,'YYYY-MM-DD') as dueydate")
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

        $this->reCalculateInGrid();
        // ./soDetails

    
        $this->dispatchBrowserEvent('show-SalesOrderForm'); //แสดง Model Form
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
            ->select('sales.id','snumber','sodate','deliverydate','name','sototal')
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('posted', FALSE)
            ->where('soreturn','N')
            ->where('closed',FALSE)
            ->where('expirydate', '>', Carbon::now()->addDays(-1))
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

        return view('livewire.accstar.sales.sales-order',[
            'salesOrders' => $salesOrders
        ]);
    }
}