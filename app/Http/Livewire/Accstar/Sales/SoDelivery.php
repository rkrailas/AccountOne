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
    public $soHeader = [];
    public $soDetails = [];
    public $sumQuantity, $sumAmount = 0;
    public $itemNos_dd, $taxRates_dd, $salesAcs_dd, $customers_dd; //Dropdown
    public $sNumberDelete;
    public $genGLs = [];
    public $sumDebit, $sumCredit = 0;
    public $closed = false;

    //Modal Serial No
    public $serialDetails;
    public $workingRow;
    public $searchSN = "";

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

    public function showGL()
    {
        $this->generateGl();
        $this->dispatchBrowserEvent('show-myModal2'); //Display Model
    }

    public function createUpdateSalesOrder() //Save Button 
    {   
        if ($this->showEditModal == true){
            DB::transaction(function () {
                // Sales
                if($this->closed == true){ //ถ้าปิดรายการให้ Reset deliveryno, deliverydate ให้ว่าง
                    DB::statement("UPDATE sales SET deliveryno=?, deliverydate=?, employee_id=?, transactiondate=?
                    where snumber=?" 
                    , [null, null, 'Admin', Carbon::now(), $this->soHeader['snumber']]);
                }else{
                    DB::statement("UPDATE sales SET deliveryno=?, deliverydate=?, employee_id=?, transactiondate=?
                    where snumber=?" 
                    , [$this->soHeader['deliveryno'], $this->soHeader['deliverydate'], 'Admin', Carbon::now()
                    , $this->soHeader['snumber']]);
                }
        
                // SalesDetail & SalesDetaillog
                foreach ($this->soDetails as $soDetails2)
                {
                    if ($this->soHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                        $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                    }

                    //ปิดรายการ
                    if($this->closed == true){

                        //ตรวจสอบถ้าเป็นสินค้ามี SN จะต้องเลือก SN แล้ว
                        if ($soDetails2['stocktype'] == 4 and $soDetails2['serialno'] == ""){
                            $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Serial No. ของสินค้า!',]);
                            return;
                        }

                        if ($soDetails2['quantity'] > 0) { //รายการที่ส่งสินค้า
                            $xquantity = $soDetails2['quantitybac'] - $soDetails2['quantity'];
                            $xquantitydel = $soDetails2['quantitydel'] + $soDetails2['quantity'];
                            $quantitybac = $soDetails2['quantityord'] - $xquantitydel;

                            //SalesDetail
                            DB::statement("UPDATE salesdetail SET quantity=?, quantitydel=?, quantitybac=?, serialno=?, employee_id=?, transactiondate=?
                                where id=?" 
                                , [$xquantity, $xquantitydel, $quantitybac, $soDetails2['serialno'], 'Admin', Carbon::now(), $soDetails2['id']]);
    
                            //Product Cost
                            $costAmt = 0;
                            $xinventory = DB::table('inventory')
                                ->select('instock','instockvalue','averagecost')
                                ->where('itemid', $soDetails2['itemid'])
                                ->get();
                            if ($xinventory->count() > 0) {
                                $costAmt = round($soDetails2['quantity'] * $xinventory[0]->averagecost, 2);
                            }
    
                            //SalesDetailLog
                            DB::statement("INSERT INTO salesdetaillog(snumber, sdate, deliveryno, itemid, description, quantity, unitprice
                                , amount, taxrate, taxamount, discountamount, cost, soreturn, journal, posted, ram_salesdetail_id, serialno
                                , employee_id, transactiondate)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                , [$this->soHeader['snumber'], $this->soHeader['sodate'], $this->soHeader['deliveryno'], $soDetails2['itemid'], $soDetails2['description']
                                , $soDetails2['quantity'], $soDetails2['unitprice'], $soDetails2['amount'], $soDetails2['taxrate'], $soDetails2['taxamount']
                                , $soDetails2['discountamount'], $costAmt, 'G', 'SO', true, $soDetails2['id'], $soDetails2['serialno'], 'Admin', Carbon::now()]);
    
                            //Inventory
                            $xinstock = $xinventory[0]->instock - $soDetails2['quantity'];
                            $xinstockvalue = $xinventory[0]->instockvalue - $costAmt;
    
                            DB::statement("UPDATE inventory SET instock=?, instockvalue=?, employee_id=?, transactiondate=?
                                where itemid=?" 
                                , [$xinstock, $xinstockvalue, 'Admin', Carbon::now(), $soDetails2['itemid']]);

                            // inventoryserial
                            DB::statement("UPDATE inventoryserial SET snumber=?,solddate=?,sold=?,employee_id=?,transactiondate=?
                                where itemid=? and serialno=?"
                                ,[$this->soHeader['snumber'],$this->soHeader['sodate'], true, 'Admin', Carbon::now(),$soDetails2['itemid']
                                ,$soDetails2['serialno']]);

                        }else{ //รายการที่ไม่ได้ส่ง Update SalesDetail Reset quantity ของรายการที่่ไม่ได้ส่งกลับมาให้เท่ากับ quantitybac
                            DB::statement("UPDATE salesdetail SET quantity=?, employee_id=?, transactiondate=?
                                where id=?" 
                                , [$soDetails2['quantitybac'], 'Admin', Carbon::now(), $soDetails2['id']]);
                        }                        
                    }else{
                        //Update Salesdetail
                        DB::statement("UPDATE salesdetail SET quantity=?, amount=?, taxamount=?, soreturn=?, serialno=?, employee_id=?, transactiondate=?
                        where id=?" 
                        , [$soDetails2['quantity'], $soDetails2['amount'], $soDetails2['taxamount'], 'G', $soDetails2['taxamount']
                        , 'Admin', Carbon::now(), $soDetails2['id']]);
                    }
                }

                $this->closed = false; //Reset ค่า
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
                    if($this->soDetails[$index]['quantity'] > $this->soDetails[$index]['quantitybac']){
                        $this->dispatchBrowserEvent('popup-alert', [
                            'title' => 'ไม่สามารถป้อนมากกว่าจำนวนคงค้างได้ !',
                        ]);

                        $this->soDetails[$index]['quantity'] = $this->soDetails[$index]['quantitybac'];
                    }
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
                $this->soDetails[$i]['quantitybac'] = round($this->soDetails[$i]['quantitybac'],2);
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
        //??? ตรวจสอบว่า quantitybac <> quantityord
        $strsql = "select * from salesdetail where snumber='" . $snumber . "' and quantitybac <> quantityord";
        $data = DB::select($strsql);
        if (count($data)){
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่สามารถลบได้ เพราะมีการส่งสินค้าแล้ว !',
            ]);
        }else{
            $this->sNumberDelete = $snumber;
            $this->dispatchBrowserEvent('delete-confirmation');
        }
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
        $data2 = DB::table('salesdetail as sd')
            ->select('sd.itemid','sd.description','sd.quantity','sd.quantitybac','sd.quantitydel','sd.quantityord','sd.salesac','sd.unitprice'
                    ,'sd.discountamount','sd.taxrate','sd.taxamount','sd.id','sd.inventoryac','sd.serialno','inv.stocktype')
            ->leftJoin('inventory as inv', 'sd.itemid', '=', 'inv.itemid')
            ->where('sd.snumber', $sNumber)
            ->where('sd.quantitybac', '>', 0)
            ->whereIn('sd.soreturn', ['N','G'])
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
            $this->reset(['sumQuantity','sumAmount']);
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
            ->selectRaw("sales.id, snumber, sodate, sototal, refno
                        , customer.customerid || ' : ' || name as name")
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('posted', FALSE)
            ->where('soreturn','N')
            ->where('closed',TRUE) 
            ->where('ram_sodeliverytax',false)
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