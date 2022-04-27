<?php

namespace App\Http\Livewire\Sales;

use App\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SoDeliveryTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete']; //For Popup Window

    public $sortDirection = "desc";
    public $sortBy = "a.transactiondate";
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
    public $errorValidate, $errorTaxNumber, $errorGLTran, $errorSoDetail = false;

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
        $strsql = "select lotnumber, sum(quantity-sold) as instock
                    from purchasedetaillog
                    where quantity-sold > 0
                    and itemid='" . $this->soDetails[$this->workingRow]['itemid'] . "'
                    and (lotnumber ilike '%" . $this->searchLotNumber . "%'
                        or ponumber ilike '%" . $this->searchLotNumber . "%')
                    group by lotnumber";
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

    public function showGL()
    {
        $this->generateGl();
        $this->dispatchBrowserEvent('show-myModal2'); //แสดง Model Form
    }

    public function generateGl($xgltran = '')
    {
        // Concept
            //Dr.ลูกหนี้การค้า
            //  Cr.ขายสินค้า
            //  Cr.ภาษีขาย
            //Dr.ต้นทุนขาย (Perpetual)
            //  Cr.สินค้าคงเหลือ (Perpetual)
        
        $this->genGLs = [];
        $this->sumDebit = 0;
        $this->sumCredit = 0;

        // 1.Dr.ลูกหนี้การค้า //account = buyer.account or controldef.account where id='AR' //gldebit = $soHeader['sototal']
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

        //ตรวจสอบว่า gldebit ว่าติดลบหรือไม่
        if ($this->soHeader['sototal'] >= 0){
            $xGLDebit = $this->soHeader['sototal'];
            $xGLCredit = 0;
        }else{
            $xGLDebit = 0;
            $xGLCredit = $this->soHeader['sototal'] * -1;
        }

        $this->genGLs[] = ([
            'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$buyAcc, 'glaccname'=>$buyAccName
            , 'gldescription'=>$this->soHeader['sonote'], 'gldebit'=>$xGLDebit, 'glcredit'=>$xGLCredit, 'jobid'=>''
            , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
            , 'transactiondate'=>Carbon::now()
        ]);

        // 2.Cr.ขายสินค้า //glcredit = $soDetails['netamount'] //glaccount = salesdetail.salesac or controldef.account where id='SA'
        $salesAcc = "";
        $salesAccName = "";

        for ($i = 0; $i < count($this->soDetails); $i++) {
            if ($this->soDetails[$i]['salesac']) {
                $salesAcc = $this->soDetails[$i]['salesac'];
            }

            if ($salesAcc == "") {
                $data = DB::table('controldef')
                    ->select("account")
                    ->where('id', 'SA')
                    ->get();
                if ($data->count() > 0) {
                    $salesAcc = $data[0]->account;
                }
            }

            if ($salesAcc != "") {
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $salesAcc)
                    ->where('detail', true)
                    ->get();
                if ($data->count() > 0) {
                    $salesAccName = $data[0]->accnameother;
                }
            }

            //ตรวจสอบว่า glcredit ว่าติดลบหรือไม่
            if ($this->soDetails[$i]['netamount'] - $this->soDetails[$i]['taxamount'] >= 0) {
                $xGLDebit = 0;
                $xGLCredit = $this->soDetails[$i]['netamount'] - $this->soDetails[$i]['taxamount'];
            } else {
                $xGLDebit = ($this->soDetails[$i]['netamount'] - $this->soDetails[$i]['taxamount']) * -1;
                $xGLCredit = 0;
            }

            $this->genGLs[] = ([
                'gjournal' => 'SO', 'gltran' => $xgltran, 'gjournaldt' => $this->soHeader['journaldate'], 'glaccount' => $salesAcc, 'glaccname' => $salesAccName
                , 'gldescription' => $this->soHeader['sonote'], 'gldebit' => $xGLDebit, 'glcredit' => $xGLCredit, 'jobid' => ''
                , 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => ''
                , 'employee_id' => '', 'transactiondate' => Carbon::now()
            ]);
        }
  
        // 3.Cr.ภาษีขาย // glcredit = $soHeader['salestax'] // glaccount = controldef.account where id='ST';     
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

        //ตรวจสอบว่า glcredit ว่าติดลบหรือไม่
        if ($this->soHeader['salestax'] >= 0){
            $xGLDebit = 0;
            $xGLCredit = $this->soHeader['salestax'];
        }else{
            $xGLDebit = $this->soHeader['salestax'] * -1;
            $xGLCredit = 0;
        }

        $this->genGLs[] = ([
            'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$taxAcc, 'glaccname'=>$taxAccName
            , 'gldescription'=> $this->soHeader['sonote'], 'gldebit'=>$xGLDebit, 'glcredit'=>$xGLCredit, 'jobid'=>''
            , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
            , 'transactiondate'=>Carbon::now()
        ]);


        // .Perpetual 
        $data = DB::table('company')
            ->select('perpetual')
            ->limit(1)
            ->get();

        if($data[0]->perpetual){ 
            // 4.Cr.สินค้าคงเหลือ // select salesdetail.inventoryac Or inventory.inventoryac
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

                //ตรวจสอบว่า glcredit ว่าติดลบหรือไม่
                if ($costAmt >= 0){
                    $xGLDebit = 0;
                    $xGLCredit = $costAmt;
                }else{
                    $xGLDebit = $costAmt * -1;
                    $xGLCredit = 0;
                }
        
                $this->genGLs[] = ([
                    'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$invAcc, 'glaccname'=>$invAccName
                    , 'gldescription'=> $this->soHeader['sonote'], 'gldebit'=>$xGLDebit, 'glcredit'=>$xGLCredit
                    , 'jobid'=>'', 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                    , 'transactiondate'=>Carbon::now()
                ]);  
            }

            // 5.Dr.ต้นทุนขาย controldef.account where id='CG'
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

            //ตรวจสอบว่า gldebit ว่าติดลบหรือไม่
            if ($totalCostAmt >= 0){
                $xGLDebit = $totalCostAmt;
                $xGLCredit = 0;
            }else{
                $xGLDebit = 0;
                $xGLCredit = $totalCostAmt * -1;
            }
    
            $this->genGLs[] = ([
                'gjournal'=>'SO', 'gltran'=>$xgltran, 'gjournaldt'=>$this->soHeader['journaldate'], 'glaccount'=>$costAcc, 'glaccname'=>$costAccName
                , 'gldescription'=>$this->soHeader['sonote'], 'gldebit'=>$xGLDebit, 'glcredit'=>$xGLCredit, 'jobid'=>''
                , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                , 'transactiondate'=>Carbon::now()
            ]);
        }

        // Summary Debit & Credit
        for($i=0; $i<count($this->genGLs);$i++)
        {
            $this->sumDebit = $this->sumDebit + $this->genGLs[$i]['gldebit'];
            $this->sumCredit = $this->sumCredit + $this->genGLs[$i]['glcredit'];
        }        

        //Sorting
        $gldebit = array_column($this->genGLs, 'gldebit');
        array_multisort($gldebit, SORT_DESC, $this->genGLs);
    }

    public function addNew() //กดปุ่ม สร้างข้อมูลใหม่
    {
        $this->showEditModal = FALSE;
            $this->errorGLTran = true;
        $this->reset(['soHeader', 'soDetails', 'sumQuantity', 'sumAmount', 'serialDetails', 'workingRow'
                    , 'errorTaxNumber', 'errorGLTran', 'errorSoDetail']);
        $xsnumber = getDocNunber("SO");
        $this->soHeader = ([
            'snumber'=>$xsnumber, 'sonumber'=>$xsnumber, 'sodate'=>Carbon::now()->format('Y-m-d')
            , 'invoiceno'=>getTaxNunber("SO"), 'invoicedate'=>Carbon::now()->format('Y-m-d')
            , 'deliveryno'=>getGlNunber("SO"), 'journaldate'=>Carbon::now()->format('Y-m-d'), 'deliverydate'=>Carbon::now()->addMonth()->format('Y-m-d')
            ,'payby'=>'0', 'duedate'=>Carbon::now()->addMonth()->format('Y-m-d')
            , 'exclusivetax'=>TRUE, 'taxontotal'=>FALSE, 'salesaccount'=>'', 'taxrate'=>getTaxRate()
            , 'salestax'=>0, 'discountamount'=>0, 'sototal'=>0, 'customerid'=>'', 'shipcost'=>0, 'shipname'=>''
            , 'posted'=>false, 'sonote' => ''
        ]); //ใบสำคัญ > deliveryno & journaldate , ใบกำกับ > invoiceno & invoicedate
        $this->soHeader['sonote'] = 'ขายสินค้าตามใบกำกับ-' . $this->soHeader['invoiceno'];
        $this->addRowInGrid();
        $this->dispatchBrowserEvent('show-soDeliveryTaxForm'); //แสดง Model Form
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

        //Re-gen Select2
        $this->dispatchBrowserEvent('regen-select2', [
            'name' => '#item-select2-' . count($this->soDetails) - 1 ,
            ]);
    }

    public function createUpdateSalesOrder() //กดปุ่ม Save 
    {
        //.ตรวจสอบเลขที่ใบสั่งขาย ใบกำกับ ใบสำคัญซ้ำหรือไม่ / ตรวจสอบอความถูกต้องให้ soDetail
        $strsql = "select count(*) as count from taxdata where purchase=false and iscancelled=false 
                and taxnumber='" . $this->soHeader['invoiceno'] . "'";
        $data = DB::select($strsql);
        if ($data[0]->count){
            $this->errorTaxNumber = true;
        }else{
            $this->errorTaxNumber = false;
        }

        $strsql = "select count(*) as count from gltran where gltran='" . $this->soHeader['deliveryno'] . "'";
        $data = DB::select($strsql);
        if ($data[0]->count){
            $this->errorGLTran = true;
        }else{
            $strsql = "select count(*) as count from glmast where gltran='" . $this->soHeader['deliveryno'] . "'";
            $data2 = DB::select($strsql);
            if ($data2[0]->count){
                $this->errorGLTran = true;
            }else{
                $this->errorGLTran = false;
            }
        }

        if($this->soHeader['posted'] == true){
            foreach ($this->soDetails as $soDetails2){
                //ถ้าเป็นสินค้ามี SN/Lot Number จะต้องเลือก SN/Lot Number แล้ว
                if ($soDetails2['stocktype'] == "4" and $soDetails2['serialno'] == ""){
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Serial No. ของสินค้า !',]);
                    return;
                }elseif ($soDetails2['stocktype'] == "9" and $soDetails2['lotnumber'] == ""){
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Lot Number ของสินค้า !',]);
                    return;
                }

                if ($soDetails2['stocktype'] == "9" ){
                    $strsql = "select sum(quantity-sold) as balance
                            from purchasedetaillog 
                            where itemid='" . $soDetails2['itemid'] . "'
                            and lotnumber='" . $soDetails2['lotnumber'] . "'";
                    $data1 = DB::select($strsql);
                    if ($data1[0]->balance < $soDetails2['quantity']){
                        $this->dispatchBrowserEvent('popup-alert', ['title' => 'สินค้าคงเหลือของ Lot ' . $soDetails2['lotnumber'] . ' ไม่พอ !',]);
                        return;
                    }
                }
            }  
        }

        if ($this->errorTaxNumber or $this->errorGLTran or $this->errorSoDetail){
            return;
        }
        //./ตรวจสอบเลขที่ใบสั่งขาย ใบกำกับ ใบสำคัญซ้ำหรือไม่ / ตรวจสอบอความถูกต้องให้ soDetail
        
        if ($this->showEditModal == true){
            //===Edit===
            DB::transaction(function () {
                //SalesDetail
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();
                foreach ($this->soDetails as $soDetails2)
                {
                    if ($soDetails2['itemid']){
                        if ($this->soHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                            $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                        }
    
                        //ปิดรายการหรือไม่
                        if($this->soHeader['posted'] == true){
                            $xquantity = 0;
                            $xquantityord = $soDetails2['quantity'];
                            $xquantitydel = $soDetails2['quantity'];
                            $xquantitybac = 0;
                        }else{
                            $xquantity = $soDetails2['quantity'];
                            $xquantityord = $soDetails2['quantity'];
                            $xquantitydel = 0;
                            $xquantitybac = $soDetails2['quantity'];
                        }
    
                        DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount, quantity, quantityord
                        , quantitydel, quantitybac, serialno, taxrate, taxamount, discountamount, soreturn, salesac, lotnumber
                        , employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description'], $soDetails2['unitprice']
                        , $soDetails2['amount'], $xquantity, $xquantityord, $xquantitydel, $xquantitybac, $soDetails2['serialno'], $soDetails2['taxrate']
                        , $soDetails2['taxamount'], $soDetails2['discountamount'], 'N', $soDetails2['salesac'], $soDetails2['lotnumber']
                        , 'Admin', Carbon::now()]);
                        
                        //ถ้าปิดรายการ
                        if ($this->soHeader['posted'] == true) {
                            // หาต้นทุนสินค้า
                            $costAmt = 0;
                            $xinventory = DB::table('inventory')
                                ->select('averagecost','instock','instockvalue')
                                ->where('itemid', $soDetails2['itemid'])
                                ->get();
                            if ($xinventory->count() > 0) {
                                $costAmt = round($soDetails2['quantity'] * $xinventory[0]->averagecost, 2);
                            }
                            
                            // Salesdetaillog
                            DB::statement("INSERT INTO salesdetaillog(snumber, sdate, deliveryno, itemid, description, quantity, unitprice, amount
                                , quantityord, quantitydel, quantitybac, taxrate, taxamount, taxnumber, discountamount, cost, soreturn
                                , journal, posted, serialno, lotnumber, ram_salesdetail_id, employee_id, transactiondate)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                , [$this->soHeader['snumber'], $this->soHeader['sodate'], $this->soHeader['deliveryno'], $soDetails2['itemid']
                                , $soDetails2['description'], $soDetails2['quantity'], $soDetails2['unitprice'], $soDetails2['amount']
                                , $soDetails2['quantity'] , $xquantitydel, $xquantitybac, $soDetails2['taxrate'], $soDetails2['taxamount']
                                , $this->soHeader['invoiceno'], $soDetails2['discountamount'], $costAmt, 'N', 'SO', true, $soDetails2['serialno']
                                , $soDetails2['lotnumber'], $soDetails2['id'], 'Admin', Carbon::now()]);
    
                            // Inventory
                            $xinstock = $xinventory[0]->instock - $soDetails2['quantity'];
                            $xinstockvalue = $xinventory[0]->instockvalue - round($soDetails2['quantity'] * $xinventory[0]->averagecost, 2);
    
                            DB::statement("UPDATE inventory SET instock=?, instockvalue=?, employee_id=?, transactiondate=?
                                where itemid=?" 
                                , [$xinstock, $xinstockvalue, 'Admin', Carbon::now(), $soDetails2['itemid']]);
    
                            // inventoryserial & purchasedetaillog
                            if($soDetails2['stocktype'] == "4"){
                                DB::statement("UPDATE inventoryserial SET snumber=?,solddate=?,sold=?,employee_id=?,transactiondate=?
                                        where itemid=? and serialno=?"
                                ,[$this->soHeader['snumber'],$this->soHeader['sodate'], true, 'Admin', Carbon::now()
                                ,$soDetails2['itemid'],$soDetails2['serialno']]);
                            }elseif($soDetails2['stocktype'] == "9"){
                                //Loop เพื่อตัดสินค้าออก
                                $xcount = 0;
                                while ($xcount < $soDetails2['quantity']) {
                                    $strsql = "select id,sold,quantity,quantity-sold as balance 
                                            from purchasedetaillog 
                                            where itemid='" . $soDetails2['itemid'] . "'
                                            and lotnumber='" . $soDetails2['lotnumber'] . "'
                                            and quantity-sold > 0
                                            order by id";
                                    $data1 = DB::select($strsql);
                                    if ($data1[0]->balance <= $soDetails2['quantity'] - $xcount) {   
                                        DB::statement("UPDATE purchasedetaillog SET sold=sold+?,employee_id=?,transactiondate=?
                                                    where id =" . $data1[0]->id
                                        ,[$data1[0]->balance, 'Admin', Carbon::now()]);                                
                                        $xcount = $xcount + $data1[0]->balance;
                                    }else{
                                        DB::statement("UPDATE purchasedetaillog SET sold=sold+?,employee_id=?,transactiondate=?
                                        where id =" . $data1[0]->id
                                        ,[$soDetails2['quantity'] - $xcount, 'Admin', Carbon::now()]);
                                        $xcount = $xcount + ($soDetails2['quantity'] - $xcount);
                                    }
                                }
                            }
                        }
                    }                    
                }

                // Sales
                DB::statement("UPDATE sales SET sodate=?, customerid=?, invoiceno=?, invoicedate=?, deliveryno=?, deliverydate=?, sototal=?, salestax=?
                        , payby=?, duedate=?, journaldate=?, exclusivetax=?, taxontotal=?, salesaccount=?, employee_id=?, transactiondate=?, posted=?
                        , sonote=?
                where snumber=?" 
                , [$this->soHeader['sodate'], $this->soHeader['customerid'], $this->soHeader['invoiceno'], $this->soHeader['invoicedate']
                , $this->soHeader['deliveryno'], $this->soHeader['deliverydate'], $this->soHeader['sototal'], $this->soHeader['salestax']
                , $this->soHeader['payby'], $this->soHeader['duedate'], $this->soHeader['journaldate'], $this->soHeader['exclusivetax']
                , $this->soHeader['taxontotal'], $this->soHeader['salesaccount'], 'Admin', Carbon::now(), $this->soHeader['posted']
                , $this->soHeader['sonote'], $this->soHeader['snumber']]);

                // ปิดรายการ
                if ($this->soHeader['posted']){
                    // Taxdata
                    DB::statement("INSERT INTO taxdata(taxnumber,taxdate,journaldate,reference,gltran,customerid
                            ,description,amountcur,amount,taxamount,duedate,purchase,posted
                            ,isinputtax,totalamount,employee_id,transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    , [$this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['journaldate'], $this->soHeader['snumber']
                    , $this->soHeader['deliveryno'], $this->soHeader['customerid'], 'ขายสินค้า-'.$this->soHeader['customerid'].'-'.$this->soHeader['snumber']
                    , $this->soHeader['sototal'], $this->soHeader['sototal'], $this->soHeader['salestax'], $this->soHeader['duedate'], FALSE, TRUE
                    , TRUE, $this->soHeader['sototal'], 'Admin', Carbon::now()]);

                    //gltran
                    $this->generateGl($this->soHeader['deliveryno']);
                    DB::table('gltran')->insert($this->genGLs);
                }

                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm',['message' => 'Save Successfully!']);
            });
        }else{
            //New
            //ตรวจสอบเลขที่เอกสารซ้ำหรือไม่
            $validateData = Validator::make($this->soHeader, [
              'snumber' => 'required|unique:sales,sonumber',
            ])->validate();

            DB::transaction(function () {
                // Sales
                DB::statement("INSERT INTO sales(snumber, sonumber, sodate, customerid, invoiceno, invoicedate, deliveryno, deliverydate, payby
                            , duedate, journaldate, exclusivetax, taxontotal, salesaccount, expirydate, sototal, salestax, closed, employee_id
                            , transactiondate, posted, sonote, ram_sodeliverytax) 
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                , [$this->soHeader['snumber'], $this->soHeader['snumber'], $this->soHeader['sodate'], $this->soHeader['customerid']
                , $this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['deliveryno'], $this->soHeader['deliverydate']
                , $this->soHeader['payby'], $this->soHeader['duedate'], $this->soHeader['journaldate'], $this->soHeader['exclusivetax']
                , $this->soHeader['taxontotal'], $this->soHeader['salesaccount'], Carbon::now()->addMonths(6), $this->soHeader['sototal']
                , $this->soHeader['salestax'], true, 'Admin', Carbon::now(), $this->soHeader['posted']
                , $this->soHeader['sonote'], true]); //ram_sodeliverytax > แยก Type ของ SO

                //SalesDetail
                DB::table('salesdetail')->where('snumber', $this->soHeader['snumber'])->delete();
                foreach ($this->soDetails as $soDetails2)
                {
                    if ($soDetails2['itemid']){
                        if ($this->soHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                            $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                        }
    
                        if($this->soHeader['posted'] == true){ //***ถ้า New จะไม่สามารถปิดรายการได้ทันที***
                        }else{
                            $xquantity = $soDetails2['quantity'];
                            $xquantityord = $soDetails2['quantity'];
                            $xquantitydel = 0;
                            $xquantitybac = $soDetails2['quantity'];
                        }
    
                        DB::statement("INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount, quantity, quantityord
                        , quantitydel, quantitybac, taxrate, taxamount, discountamount, soreturn, salesac, serialno, lotnumber
                        , employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$this->soHeader['snumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description']
                        , $soDetails2['unitprice'], $soDetails2['amount'], $xquantity, $xquantityord, $xquantitydel, $xquantitybac
                        , $soDetails2['taxrate'], $soDetails2['taxamount'], $soDetails2['discountamount'], 'N'
                        , $soDetails2['salesac'], $soDetails2['serialno'], $soDetails2['lotnumber']
                        , 'Admin', Carbon::now()]);
                    }                    
                }
                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm',['message' => 'Save Successfully!']);
            });
        }
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
        $this->reset(['soHeader','soDetails','errorValidate','errorTaxNumber','errorGLTran']);

        // soHeader
        $data = DB::table('sales')
            ->selectRaw("snumber,to_char(sodate,'YYYY-MM-DD') as sodate, invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate
                        , deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate, payby
                        , CONCAT(customer.customerid,': ', customer.name) as shipname
                        , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                        , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                        , taxontotal, posted, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost, sonote")
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
            ->orderBy('id')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true); 

        $this->reCalculateInGrid();

        $this->dispatchBrowserEvent('show-soDeliveryTaxForm'); //แสดง Model Form
        //$this->dispatchBrowserEvent('clear-select2');

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

        $strsql = "SELECT account, accnameother FROM account WHERE detail=true ORDER BY account";
        $this->account_dd = DB::select($strsql);

        $this->taxRates_dd = DB::table('taxtable')
        ->select('code','taxrate')
        ->where('taxtype','1')
        ->orderby('code')
        ->get();

        $strsql = "SELECT customerid, name, taxid FROM customer WHERE debtor=true ORDER BY customerid";
        $this->customers_dd = DB::select($strsql);
        // Bind Data to Dropdown End

        // .getSalesOrder
        $strsql = "SELECT a.id, a.snumber, a.sodate, a.sototal, b.customerid || ' : ' || b.name as name, a.transactiondate
        FROM sales a
        LEFT JOIN customer b ON a.customerid=b.customerid
        WHERE a.posted=false AND a.soreturn='N' AND a.ram_sodeliverytax=true
            AND (a.snumber ILIKE '%" . $this->searchTerm . "%'
                OR b.name ILIKE '%" . $this->searchTerm . "%'
                OR CAST(a.sototal AS TEXT) ILIKE '%" . $this->searchTerm . "%')
        ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $salesOrders = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);
        // getSalesOrder End

        return view('livewire.sales.so-delivery-tax',[
            'salesOrders' => $salesOrders
        ]);
    }
}