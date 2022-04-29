<?php

namespace App\Http\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Support\Collection;

class SalesOrder extends Component
{
    use WithPagination; // Require for Pagination
    protected $paginationTheme = 'bootstrap'; // Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $sortDirection = "desc";
    public $sortBy = "a.transactiondate";
    public $numberOfPage = 10;
    public $searchTerm = null;
    
    public $showEditModal = null;
    public $soHeader = [];
    public $soDetails = [];
    public $sumQuantity, $sumAmount = 0;
    public $itemNos_dd, $taxRates_dd, $salesAcs_dd, $customers_dd;
    public $sNumberDelete;
    
    public $serialDetails, $workingRow, $searchSN; //Modal Serial No
    public $listItem, $searchItem; //Modal Item
    public $lotNumbers, $searchLotNumber; //Modal Lot Number

    public function refreshData()
    {
        $this->resetPage();
    }

    //.Event Lot Modal
    public function showLotNumber($xindex)
    {   
        $this->workingRow = $xindex; //กำลังทำงานเป็น Row ไหน ของ soDetails
        $this->getLotNumber();
        $this->dispatchBrowserEvent('show-lotNumberOutForm'); 
    }

    public function selectedLotNumber($xLotNumber)
    {
        $this->soDetails[$this->workingRow]['lotnumber'] = $xLotNumber;
        $this->dispatchBrowserEvent('hide-lotNumberOutForm');
    }

    public function getLotNumber()
    {
        $this->reset(['lotNumbers']);
        $strsql = "select id, lotnumber, ponumber, podate, quantity-sold as instock
                    from purchasedetaillog
                    where quantity-sold > 0
                    and itemid='" . $this->soDetails[$this->workingRow]['itemid'] . "'
                    and (lotnumber ilike '%" . $this->searchLotNumber . "%'
                        or ponumber ilike '%" . $this->searchLotNumber . "%'
                        )";
        $this->lotNumbers = json_decode(json_encode(DB::select($strsql)), true);
    }

    public function updatedSearchLotNumber()
    {
        $this->getLotNumber();
    }
    //./Event Lot Modal

    //.Event Item Modal
    public function selectedItem($xindex, $xitemid) //หลังจากเลือก Item
    {
        $data = DB::table('inventory')
            ->select('description', 'stocktype')
            ->where('itemid', $xitemid) 
            ->first();
        $data = json_decode(json_encode($data), true);
        $this->soDetails[$xindex]['itemid'] = $xitemid;
        $this->soDetails[$xindex]['description'] = $data['description'];
        $this->soDetails[$xindex]['stocktype'] = $data['stocktype'];

        if ($data['stocktype'] == "4"){
            $this->soDetails[$xindex]['quantity'] = 1;
        }else{
            $this->soDetails[$xindex]['serialno'] = "";
        }

        $this->dispatchBrowserEvent('hide-itemListForm');
        $this->reset(['workingRow']);
    }

    public function updatedsearchItem() 
    {
        $this->getItemInModal();
    }

    public function getItemInModal()
    {
        $strsql = "select itemid, description, round(instock,2) as instock from inventory  
                where itemid ilike '%" . $this->searchItem . "%'
                or description ilike '%" . $this->searchItem . "%'
                order by itemid";
        $this->listItem = json_decode(json_encode(DB::select($strsql)), true);
    }

    public function showModalItem($xindex)
    {
        $this->reset(['searchItem']);
        $this->workingRow = $xindex; //กำลังทำงานเป็น Row ไหน ของ soDetails
        $this->getItemInModal();
        $this->dispatchBrowserEvent('show-itemListForm');
    }
    //./Event Item Modal

    //.Event SN Modal
    public function updatedSearchSN() 
    {
        $this->getItemSNInModal();
    }

    public function selectedSN($xserialno)
    {
        $this->dispatchBrowserEvent('hide-serialNoOutForm');
        $this->soDetails[$this->workingRow]['serialno'] = $xserialno;
        $this->reset(['workingRow']);
    }

    public function getItemSNInModal()
    {
        //ดึงข้อมูล inventoryserial ที่ยังไม่ได้ขาย และจอง
        $strsql = "select inv.serialno, loc.code || ' : ' || loc.other as location, round(inv.cost,2) as cost
                ,col.code || ' : ' || col.other as color, inv.reference1, inv.reference2, aa.snumber
                from inventoryserial inv
                left join misctable loc on inv.location=loc.code and loc.tabletype='LO'
                left join misctable col on inv.color=col.code and col.tabletype='CL'
                left join (select a.snumber,b.serialno from sales a
                    join salesdetail b on a.snumber=b.snumber
                    where a.posted=false) aa on inv.serialno=aa.serialno
                where inv.serialno ilike '%" . $this->searchSN . "%' 
                and inv.sold=false and aa.snumber is null
                and inv.itemid='" . $this->soDetails[$this->workingRow]['itemid'] . "'";
        $this->serialDetails = DB::select($strsql);
        $this->serialDetails = json_decode(json_encode($this->serialDetails), true);
    }

    public function showModalSN($xindex)
    {
        $this->reset(['searchSN']);
        $this->workingRow = $xindex; //กำลังทำงานเป็น Row ไหน ของ soDetails
        $this->getItemSNInModal();
        $this->dispatchBrowserEvent('show-serialNoOutForm');
    }
    //./Event SN Modal

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
        $this->showEditModal = FALSE;
        $this->reset(['soHeader', 'soDetails', 'sumQuantity', 'sumAmount', 'serialDetails', 'workingRow']);
        $this->soHeader = ([
            'snumber'=>'', 'sonumber'=>'', 'sodate'=>Carbon::now()->format('Y-m-d'), 'expirydate'=>Carbon::now()->addMonth()->format('Y-m-d')
            , 'deliveryno'=>'', 'deliverydate'=>Carbon::now()->addMonth()->format('Y-m-d'), 'refno'=>'', 'payby'=>'0'
            , 'exclusivetax'=>true, 'taxontotal'=>false, 'salesaccount'=>'', 'taxrate'=>getTaxRate()
            , 'salestax'=>0, 'discountamount'=>0, 'sototal'=>0, 'customerid'=>'', 'shipcost'=>0, 'shipname'=>'','full_address'=>''
            , 'closed'=>false, 'duedate'=>Carbon::now()->addMonth()->format('Y-m-d')
        ]);
        $xNumber = getDocNunber("SO");
        $this->soHeader['snumber'] = $xNumber;
        $this->soHeader['sonumber'] = $xNumber;

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
            'itemid'=>'','description'=>'','quantity'=>0,'salesac'=>'','unitprice'=>0,'amount'=>0,'discountamount'=>0,'netamount'=>0
            , 'taxamount'=>0, 'taxrate'=>getTaxRate(), 'stocktype'=>'', 'serialno'=>'', 'lotnumber'=>''
        ]);
    }

    public function createUpdateSalesOrder() //กดปุ่ม Save 
    {   
        if ($this->showEditModal == true){
            //===Edit===
            DB::transaction(function () {
                //Updaate Sales
                DB::statement("UPDATE sales SET sodate=?, customerid=?, deliverydate=?, expirydate=?, refno=?, sototal=?, salestax=?
                , exclusivetax=?, taxontotal=?, salesaccount=?, employee_id=?, transactiondate=?, closed=?, duedate=?
                where snumber=?" 
                , [$this->soHeader['sodate'], $this->soHeader['customerid'], $this->soHeader['deliverydate'], $this->soHeader['expirydate']
                , $this->soHeader['refno'], $this->soHeader['sototal'], $this->soHeader['salestax']
                , convertToBoolean($this->soHeader['exclusivetax']), convertToBoolean($this->soHeader['taxontotal']), $this->soHeader['salesaccount']
                , 'Admin', Carbon::now(), convertToBoolean($this->soHeader['closed']), $this->soHeader['duedate'], $this->soHeader['snumber']]);
            
                //SalesDetail 
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete(); //ลบออกไปก่อน
                foreach ($this->soDetails as $soDetails2)
                {
                    if ($soDetails2['itemid'])
                    {
                        if ($this->soHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                            $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                        }
    
                        $xquantity = $soDetails2['quantity'];
                        $xquantityord = $soDetails2['quantity'];
                        $xquantitydel = 0;
                        $xquantitybac = $soDetails2['quantity'];
    
                        DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount, quantity, quantityord
                        , quantitydel, quantitybac, serialno, lotnumber, taxrate, taxamount, discountamount, soreturn, salesac
                        , employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description'], $soDetails2['unitprice']
                        , $soDetails2['amount'], $xquantity, $xquantityord, $xquantitydel, $xquantitybac, $soDetails2['serialno'], $soDetails2['lotnumber']
                        , $soDetails2['taxrate'], $soDetails2['taxamount'], $soDetails2['discountamount'], 'N', $soDetails2['salesac']
                        , 'Admin', Carbon::now()]);
                    }                    
                }  
                $this->dispatchBrowserEvent('hide-SalesOrderForm',['message' => 'Save Successfully!']);
            });
        }else{
            //===New===
            $validateData = Validator::make($this->soHeader, [
                'snumber' => 'required|unique:sales,sonumber',
                ])->validate();

            DB::transaction(function () {
                //Sales
                DB::statement("INSERT INTO sales(snumber, sonumber, sodate, customerid, expirydate, deliverydate, refno
                , exclusivetax, taxontotal, salesaccount, sototal, salestax, closed, duedate, employee_id, transactiondate)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                , [$this->soHeader['snumber'], $this->soHeader['sonumber'], $this->soHeader['sodate'], $this->soHeader['customerid']
                , $this->soHeader['expirydate'], $this->soHeader['deliverydate'], $this->soHeader['refno']
                , convertToBoolean($this->soHeader['exclusivetax']), convertToBoolean($this->soHeader['taxontotal']), $this->soHeader['salesaccount']
                , $this->soHeader['sototal'], $this->soHeader['salestax'], convertToBoolean($this->soHeader['closed']), $this->soHeader['duedate']
                , 'Admin', Carbon::now()]);
                
                //SalesDetail
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();

                foreach ($this->soDetails as $soDetails2)
                {
                    if ($soDetails2['itemid']){
                        if ($this->soHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                            $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                        }
    
                        $xquantity = $soDetails2['quantity'];
                        $xquantityord = $soDetails2['quantity'];
                        $xquantitydel = 0;
                        $xquantitybac = $soDetails2['quantity'];
    
                        DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount, quantity, quantityord,
                         quantitydel, quantitybac, serialno, lotnumber, taxrate, taxamount, discountamount, soreturn, salesac
                         , employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description'], $soDetails2['unitprice']
                        , $soDetails2['amount'], $xquantity, $xquantityord, $xquantitydel, $xquantitybac, $soDetails2['serialno'], $soDetails2['lotnumber']
                        , $soDetails2['taxrate'], $soDetails2['taxamount'], $soDetails2['discountamount'], 'N', $soDetails2['salesac']
                        , 'Admin', Carbon::now()]);
                    }
                    
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
                    ->select('description', 'stocktype')
                    ->where('itemid', $this->soDetails[$index][$itemName]) 
                    ->first();
                $data = json_decode(json_encode($data), true); 
                $this->soDetails[$index]['description'] = $data['description'];
                $this->soDetails[$index]['stocktype'] = $data['stocktype'];
                $this->soDetails[$index]['serialno'] = "";

                if ($data['stocktype'] == "4"){
                    $this->soDetails[$index]['quantity'] = 1;
                }
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
        $this->reset(['soHeader','soDetails']);

        // soHeader
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
        
        // soDetails
        $data2 = DB::table('salesdetail')
            ->select('salesdetail.itemid','salesdetail.description','salesdetail.quantity','salesdetail.salesac','salesdetail.unitprice'
                    ,'salesdetail.discountamount','salesdetail.taxrate','salesdetail.taxamount','salesdetail.id','salesdetail.inventoryac'
                    ,'inventory.stocktype','salesdetail.serialno','salesdetail.lotnumber')
            ->join('inventory', 'salesdetail.itemid', '=', 'inventory.itemid')
            ->where('snumber', $sNumber)
            ->where('soreturn', 'N')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true);

        $this->reCalculateInGrid();
    
        $this->dispatchBrowserEvent('show-SalesOrderForm'); //แสดง Model Form

        //Bind Customer
        $newOption = "<option value=''>---โปรดเลือก---</option>";
        foreach ($this->customers_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['customerid'] . "' ";
            if ($row['customerid'] == $this->soHeader['customerid']) {
                $newOption = $newOption . "selected='selected'"; 
            }
            $newOption = $newOption . ">" . $row['customerid'] . " : " . $row['name'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect', ['newOption' => $newOption, 'selectName' => '#customer-select2']);
    }

    public function updatingSearchTerm() //Event นี้เกิดจากการ Key ที่ input wire:model.lazy="searchTerm"
    {
        $this->resetPage();
    }

    public function render()
    {
        // Summary grid     
        if($this->soDetails != Null)
        {            
            $this->reCalculateSummary();
        }else{
            $this->reset(['sumQuantity','sumAmount']);
            $this->soHeader['discountamount'] = 0;
            $this->soHeader['salestax'] = 0;
            $this->soHeader['sototal'] = 0;
            //$this->soHeader['customerid'] = "";
        }
        
        // Bind Data to Dropdown
        $this->itemNos_dd = DB::table('inventory')
        ->select('itemid','description')
        ->orderby('itemid')
        ->get();

        $strsql = "SELECT account, accnameother FROM account WHERE detail=true ORDER BY account";
        $this->salesAcs_dd = DB::select($strsql);

        $this->taxRates_dd = DB::table('taxtable')
        ->select('code','taxrate')
        ->where('taxtype','1')
        ->orderby('code')
        ->get();

        $strsql = "SELECT customerid, name, taxid FROM customer WHERE debtor=true ORDER BY customerid";
        $this->customers_dd = DB::select($strsql);


        // getSalesOrder
        $strsql = "SELECT a.id, a.snumber, a.sodate, a.sototal, a.refno, a.deliverydate, b.customerid || ' : ' || b.name as name, a.transactiondate
            FROM sales a
            LEFT JOIN customer b ON a.customerid=b.customerid
            WHERE a.posted=false AND a.soreturn='N' AND a.closed=false AND a.ram_sodeliverytax=false AND a.expirydate > NOW() - interval '1 month'
                AND (a.snumber ILIKE '%" . $this->searchTerm . "%'
                    OR b.name ILIKE '%" . $this->searchTerm . "%'
                    OR CAST(a.sototal AS TEXT) ILIKE '%" . $this->searchTerm . "%')
            ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $salesOrders = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.sales.sales-order',[
            'salesOrders' => $salesOrders
        ]);
    }
}