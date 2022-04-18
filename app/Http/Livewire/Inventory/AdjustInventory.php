<?php

namespace App\Http\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\AdjustInventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class AdjustInventory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "inventoryadjlog.transactiondate";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $inventorys_dd, $account_dd, $location_dd;
    public $adjInventory = [];    
    public $adjustType;
    public $isSerial, $isLotNumber;
    public $sumSerialQty, $sumSerialCost = 0;
    public $duplicate_sn, $listSN = [];
    public $selectedRows = [];

    public $genGLs = [];
    public $sumDebit, $sumCredit = 0;

    //Modal SN & Lot Number
    public $serialDetails, $lotNumbers = [];
    public $searchSN, $searchLotNumber;

    public function refreshData()
    {
        $this->resetPage();
    }
    
    //.Modal Lot Number
    public function showLotNumber()
    {   
        $this->getLotNumber();
        $this->dispatchBrowserEvent('show-lotNumberOutForm'); 
    }

    public function selectedLotNumber($xLotNumber) 
    {
        $this->adjInventory['lotnumber'] = $xLotNumber;
        $this->dispatchBrowserEvent('hide-lotNumberOutForm');
    }

    public function getLotNumber()
    {
        $this->reset(['lotNumbers']);
        $strsql = "select lotnumber, sum(quantity-sold) as instock
                    from purchasedetaillog
                    where quantity-sold > 0
                    and itemid='" . $this->adjInventory['itemid'] . "'
                    and (lotnumber ilike '%" . $this->searchLotNumber . "%'
                        or ponumber ilike '%" . $this->searchLotNumber . "%')
                    group by lotnumber";
        $this->lotNumbers = json_decode(json_encode(DB::select($strsql)), true);
    }

    public function updatedSearchLotNumber()
    {
        $this->getLotNumber();
    }

    //./Modal Lot Number

    // .Modal Serial No
    public function closedModalSerialNo()
    {
        if ($this->adjustType == "in"){
            //ตรวจสอบเลขที่ใบกำกับ & ใบสำคัญซ้ำหรือไม่
            $xCondition = "";
            for ($i = 0; $i < count($this->serialDetails); $i++) {
                $xCondition = $xCondition . "'" . $this->serialDetails[$i]['serialno'] . "'";
                if ($i <> count($this->serialDetails) - 1) {
                    $xCondition = $xCondition . ',';
                }
            }

            if ($xCondition){
                $strsql = "select serialno from inventoryserial where sold=false and serialno in (" . $xCondition . ")";
                $this->duplicate_sn = DB::select($strsql);
                if (count($this->duplicate_sn)){
                    return;
                }
            }

            $this->adjInventory['adjquantity'] = $this->sumSerialQty;
            $this->adjInventory['adjtotalvalue'] = round($this->sumSerialCost,2);
            $this->dispatchBrowserEvent('hide-serialNoForm');

        }else if ($this->adjustType == "out"){
            $this->adjInventory['adjquantity'] = count($this->selectedRows);
            $this->adjInventory['adjtotalvalue'] = round(DB::table('inventoryserial')
                ->whereIn('serialno', $this->selectedRows)
                ->sum('cost'),2);
            $this->dispatchBrowserEvent('hide-serialNoOutForm');
        }

    }

    public function updatedSerialDetails() //ตอนที่ Grid ของ Serial No มีการเปลี่ยนแปลง
    {
        $this->sumSerialQty = count($this->serialDetails);
        $this->sumSerialCost = array_sum(array_column($this->serialDetails,'cost'));
    }

    public function showSN()
    {   
        $this->reset(['serialDetails']);

        if ($this->adjustType == "in"){
            if (! $this->serialDetails) {
                $this->addRowInGrid();
            }
            $this->dispatchBrowserEvent('show-serialNoForm'); //แสดง Model Form
        }else if ($this->adjustType == "out"){
            $this->getSN();
            $this->dispatchBrowserEvent('show-serialNoOutForm'); 
        }
    }

    public function getSN()
    {
        //Ouery ข้อมูล SN ขึ้นมา
        $strsql = "select inv.serialno, loc.code || ' : ' || loc.other as location
                , round(inv.cost,2) as cost
                ,col.code || ' : ' || col.other as color, inv.reference1, inv.reference2
                from inventoryserial inv
                left join misctable loc on inv.location=loc.code and loc.tabletype='LO'
                left join misctable col on inv.color=col.code and col.tabletype='CL'
                where inv.sold=false 
                    and inv.itemid='" . $this->adjInventory['itemid'] . "'
                    and (inv.serialno ilike '%" . $this->searchSN . "%'
                        or loc.other ilike '%" . $this->searchSN . "%'
                        or col.other ilike '%" . $this->searchSN . "%'
                        )";
        $this->serialDetails = DB::select($strsql);
        $this->serialDetails = json_decode(json_encode($this->serialDetails), true);
    }

    public function updatedSearchSN()
    {
        $this->getSN();
    }
    // ./Modal Serial No


    public function removeRowInGrid($index) //กดปุ่มลบ Row ใน Grid
    {        
        unset($this->serialDetails[$index]);
        $this->reset(['duplicate_sn']);
        $this->updatedSerialDetails();
    }

    public function addRowInGrid() //กดปุ่มสร้าง Row ใน Grid
    {   
        //สร้าง Row ว่างๆ ใน Gird
        $this->serialDetails[] = ([
            'serialno'=>'','location'=>'','cost'=>0,'color'=>'','reference1'=>'','reference2'=>''
            ]);
    }



    public function showGL()
    {
        $this->generateGl();
        $this->dispatchBrowserEvent('show-myModal2'); //แสดง Model Form
    }

    public function generateGl($xgltran = '')
    {
        // ปรับปรุงเพิ่ม (in)
            //Dr.สินค้าคงคลัง
            //  Cr.เจ้าหนี้อื่นๆ
        // ปรับปรุงลด (out)
            //Dr.ลูกหนี้อื่นๆ
            //  Cr.สินค้าคงคลัง
        
        $this->genGLs = [];
        $this->sumDebit = 0;
        $this->sumCredit = 0;

        if ($this->adjustType == "in"){
            //===Dr.สินค้าคงคลัง (inventory.inventoryac OR controldef.id='IN')===
            $invAcc = "";
            $invAccName = "";

            $data = DB::table('inventory')
            ->select("inventoryac")
            ->where('itemid', $this->adjInventory['itemid'])
            ->get();
            if ($data->count() > 0) {
                $invAcc = $data[0]->inventoryac;
            }

            if ($invAcc == ""){
                $data = DB::table('controldef')
                ->select("account")
                ->where('id', 'IN')
                ->get();
                if ($data->count() > 0) {
                    $invAcc = $data[0]->account;
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
    
            //ตรวจสอบว่า gldebit ว่าติดลบหรือไม่
            if ($this->adjInventory['adjtotalvalue'] >= 0){
                $xGLDebit = $this->adjInventory['adjtotalvalue'];
                $xGLCredit = 0;
            }else{
                $xGLDebit = 0;
                $xGLCredit = $this->adjInventory['adjtotalvalue'] * -1;
            }

            $this->genGLs[] = ([
                'gjournal'=>'GL', 'gltran'=>$xgltran, 'gjournaldt'=>$this->adjInventory['adjustdate'], 'glaccount'=>$invAcc, 'glaccname'=>$invAccName
                , 'gldescription'=>'', 'gldebit'=>$xGLDebit, 'glcredit'=>$xGLCredit, 'jobid'=>''
                , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                , 'transactiondate'=>Carbon::now()
            ]);

            //===Cr.เจ้าหนี้อื่นๆ ($this->adjInventory['account'])===
            $apAcc = "";
            $apAccName = "";

            if ($this->adjInventory['account'] != " "){
                $apAcc = $this->adjInventory['account'];
            }

            if ($apAcc != ""){
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $apAcc)
                    ->where('detail', true)
                    ->get();            
                if ($data->count() > 0) {
                    $apAccName = $data[0]->accnameother;
                } 
            }
    
            //ตรวจสอบว่า glcredit ว่าติดลบหรือไม่
            if ($this->adjInventory['adjtotalvalue'] >= 0) {
                $xGLDebit = 0;
                $xGLCredit = $this->adjInventory['adjtotalvalue'];
            } else {
                $xGLDebit = ($this->adjInventory['adjtotalvalue']) * -1;
                $xGLCredit = 0;
            }

            $this->genGLs[] = ([
                'gjournal' => 'GL', 'gltran' => $xgltran, 'gjournaldt' => $this->adjInventory['adjustdate'], 'glaccount' => $apAcc, 'glaccname' => $apAccName
                , 'gldescription' => '', 'gldebit' => $xGLDebit, 'glcredit' => $xGLCredit, 'jobid' => ''
                , 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => ''
                , 'employee_id' => '', 'transactiondate' => Carbon::now()
            ]);

        }elseif($this->adjustType == "out"){
            //===Dr.ลูกหนี้อื่นๆ ($this->adjInventory['account'])===
            $arAcc = "";
            $arAccName = "";

            if ($this->adjInventory['account'] != " "){
                $arAcc = $this->adjInventory['account'];
            }

            if ($arAcc != ""){
                $data = DB::table('account')
                    ->select("accnameother")
                    ->where('account', $arAcc)
                    ->where('detail', true)
                    ->get();            
                if ($data->count() > 0) {
                    $arAccName = $data[0]->accnameother;
                } 
            }
    
            //ตรวจสอบว่า gldebit ว่าติดลบหรือไม่
            if ($this->adjInventory['adjtotalvalue'] >= 0){
                $xGLDebit = $this->adjInventory['adjtotalvalue'];
                $xGLCredit = 0;
            }else{
                $xGLDebit = 0;
                $xGLCredit = $this->adjInventory['adjtotalvalue'] * -1;
            }

            $this->genGLs[] = ([
                'gjournal'=>'GL', 'gltran'=>$xgltran, 'gjournaldt'=>$this->adjInventory['adjustdate'], 'glaccount'=>$arAcc, 'glaccname'=>$arAccName
                , 'gldescription'=>'', 'gldebit'=>$xGLDebit, 'glcredit'=>$xGLCredit, 'jobid'=>''
                , 'department'=>'', 'allocated'=>0, 'currencyid'=>'', 'posted'=>false, 'bookid'=>'', 'employee_id'=>''
                , 'transactiondate'=>Carbon::now()
            ]);

            //===Cr.สินค้าคงคลัง (inventory.inventoryac OR controldef.id='IN')===
            $invAcc = "";
            $invAccName = "";

            $data = DB::table('inventory')
            ->select("inventoryac")
            ->where('itemid', $this->adjInventory['itemid'])
            ->get();
            if ($data->count() > 0) {
                $invAcc = $data[0]->inventoryac;
            }

            if ($invAcc == ""){
                $data = DB::table('controldef')
                ->select("account")
                ->where('id', 'IN')
                ->get();
                if ($data->count() > 0) {
                    $invAcc = $data[0]->account;
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

            //ตรวจสอบว่า glcredit ว่าติดลบหรือไม่
            if ($this->adjInventory['adjtotalvalue'] >= 0) {
                $xGLDebit = 0;
                $xGLCredit = $this->adjInventory['adjtotalvalue'];
            } else {
                $xGLDebit = ($this->adjInventory['adjtotalvalue']) * -1;
                $xGLCredit = 0;
            }

            $this->genGLs[] = ([
                'gjournal' => 'GL', 'gltran' => $xgltran, 'gjournaldt' => $this->adjInventory['adjustdate'], 'glaccount' => $invAcc, 'glaccname' => $invAccName
                , 'gldescription' => '', 'gldebit' => $xGLDebit, 'glcredit' => $xGLCredit, 'jobid' => ''
                , 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => ''
                , 'employee_id' => '', 'transactiondate' => Carbon::now()
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

    public function exportExcel(){
        return Excel::download(new AdjustInventoryExport($this->searchTerm), 'AdjustInventory.xlsx');
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

    public function createAdjustInventory() //ตอน Save
    {
        //Validate Data
        if ($this->isSerial and $this->serialDetails == null) {
            $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Serial No. !',]);
            return;
        }

        if ($this->isLotNumber and $this->adjInventory['lotnumber'] == "") {
            $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Lot Number !',]);
            return;
        }

        $validateData = Validator::make($this->adjInventory, [
            'documentno' => 'required|unique:inventoryadjlog,documentno',
            'adjquantity' => 'required',
        ])->validate();
        
        if ($this->adjustType == "in"){ 
            //===ปรับปรุง-เข้า===
            //Check data last update?
            $data = DB::table('inventory')
                    ->select('itemid','instock','instockvalue')
                    ->where('itemid',$this->adjInventory['itemid'])
                    ->get();
            if (count($data) > 0)
            {
                $data = json_decode(json_encode($data[0]), true);

                //Check has changed in table inventory
                if ($data['instock'] != $this->adjInventory['instock'] or $data['instockvalue'] != $this->adjInventory['instockvalue']) {
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'กรุณาทำรายการใหม่ เนื่องจากมีข้อมูลเปลี่ยนแปลงระหว่างทำการ !',]);
                    return;
                }

                DB::transaction(function () use ($data) {
                    //Calulate New instock, instockvalue, averagecost
                    $newInstock = $data['instock'] + $this->adjInventory['adjquantity'];
                    $newInstockvalue = $data['instockvalue'] + $this->adjInventory['adjtotalvalue'];
                    $newAveragecost = $newInstockvalue / $newInstock;

                    //Update Inventory
                    DB::statement("UPDATE inventory SET cost=?,instock=?,instockvalue=?,averagecost=?,employee_id=?,transactiondate=?
                    where id=?" 
                    ,[$newAveragecost,$newInstock,$newInstockvalue,$newAveragecost,'Admin', Carbon::now(), $this->adjInventory['id']]);

                    //Insert inventoryadjlog
                    $isadjustin = true;
                    DB::statement("INSERT INTO inventoryadjlog(itemid,documentno,adjquantity,adjvalue,location,isadjustin,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?)"
                        ,[$this->adjInventory['itemid'],$this->adjInventory['documentno'],$this->adjInventory['adjquantity']
                        ,$this->adjInventory['adjvalue'],$this->adjInventory['location'],$isadjustin,'Admin',Carbon::now()]);

                    //Insert purchasedetaillog & inventoryserial
                    if ($this->isSerial) {
                        //สินค้ามี Serial No.
                        foreach ($this->serialDetails as $row){
                            if ($row['serialno']) {
                                //Insert purchasedetaillog
                                DB::statement("INSERT INTO purchasedetaillog(ponumber,podate,itemid,description,quantity,quantityg,unitprice,amount
                                ,taxref,receiveno,cost,location,unitofmeasure,stocktype,poreturn,goodsin,journal,posted,employee_id,transactiondate)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                                ,1,1,$row['cost'],$row['cost'],$this->adjInventory['documentno'],$this->adjInventory['documentno'],$row['cost'],$row['location']
                                ,$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype'],'N',true,'AI',true,'Admin',Carbon::now()]);

                                //Insert inventoryserial
                                DB::statement("INSERT INTO inventoryserial(itemid,serialno,category,brand,cost,location,color,ponumber,orderdate
                                        ,posted,taxref,employee_id,transactiondate)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                ,[$this->adjInventory['itemid'],$row['serialno'],$this->adjInventory['category'],$this->adjInventory['brand']
                                ,$row['cost'],$row['location'],$row['color'],$this->adjInventory['documentno'],$this->adjInventory['adjustdate']
                                ,true,$this->adjInventory['documentno'],'Admin',Carbon::now()]);
                            }                                
                        }
                    }else{
                            //สินค้าไม่มี Serial No.
                            //Insert purchasedetaillog
                            DB::statement("INSERT INTO purchasedetaillog(ponumber,podate,itemid,description,quantity,quantityg,unitprice,amount
                                ,taxref,receiveno,cost,location,unitofmeasure,stocktype,poreturn,goodsin,journal,lotnumber,posted
                                ,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                        ,$this->adjInventory['adjquantity'],$this->adjInventory['adjquantity'],$this->adjInventory['adjvalue'],$this->adjInventory['adjtotalvalue']
                        ,$this->adjInventory['documentno'],$this->adjInventory['documentno'],$this->adjInventory['adjvalue'],$this->adjInventory['location']
                        ,$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype'],'N',true,'AI',$this->adjInventory['lotnumber']
                        ,true,'Admin',Carbon::now()]);
                    }

                    //gltran
                    $this->generateGl($this->adjInventory['documentno']);
                    DB::table('gltran')->insert($this->genGLs);
                    
                    $this->dispatchBrowserEvent('hide-adjustInventoryForm',['message' => 'Create Successfully!']);
                });
            }
        }
        else if($this->adjustType == "out"){ 
            //===ปรับปรุง-ออก===
            //Check data last update?
            $data = DB::table('inventory')
                    ->select('itemid','instock','instockvalue')
                    ->where('itemid',$this->adjInventory['itemid'])
                    ->get();
            if (count($data) > 0)
            {
                $data = json_decode(json_encode($data[0]), true);

                //ตรวจสอบว่า คงเหลือมีพอให้ตัดออกหรือไม่
                if ($data['instock'] < $this->adjInventory['adjquantity']) {
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'สินค้าคงเหลือไม่พอปรับปรุงออก !',]);
                    return;
                }

                //Check has changed in table inventory
                if ($data['instock'] != $this->adjInventory['instock'] or $data['instockvalue'] != $this->adjInventory['instockvalue']) {
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'กรุณาทำรายการใหม่ เนื่องจากมีข้อมูลเปลี่ยนแปลงระหว่างทำการ !',]);
                    return;
                }

                DB::transaction(function () use ($data) {
                    //Calulate New instock, instockvalue, averagecost
                    $newInstock = $data['instock'] - $this->adjInventory['adjquantity'];
                    $newInstockvalue = $data['instockvalue'] - $this->adjInventory['adjtotalvalue'];

                    //1.Update Inventory
                    DB::statement("UPDATE inventory SET instock=?,instockvalue=?,employee_id=?,transactiondate=?
                    where id=?" 
                    ,[$newInstock, $newInstockvalue, 'Admin', Carbon::now(), $this->adjInventory['id']]);

                    //2.Insert inventoryadjlog
                    $isadjustin = false;

                    if ($this->isSerial) {
                        $this->adjInventory['averagecost'] = 0;
                    }

                    DB::statement("INSERT INTO inventoryadjlog(itemid,documentno,adjquantity,adjvalue,location,isadjustin,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?)"
                        ,[$this->adjInventory['itemid'],$this->adjInventory['documentno'],$this->adjInventory['adjquantity']
                        ,$this->adjInventory['averagecost'],$this->adjInventory['location'],$isadjustin,'Admin',Carbon::now()]);

                    //3.Insert salesdetaillog & inventoryserial & purchasedetaillog
                    if ($this->isSerial) {
                        //สินค้ามี Serial No.
                        foreach ($this->serialDetails as $row){
                            //Insert salesdetaillog
                            DB::statement("INSERT INTO salesdetaillog(snumber,sdate,itemid,description,quantity,amount,cost,location
                            ,unitofmeasure,stocktype,soreturn,goodsout,journal,posted,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                            ,1,$row['cost'],$row['cost'],$row['location'],$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype']
                            ,'N',true,'AO',true,'Admin',Carbon::now()]);
                        
                            //Update inventoryserial
                            $sn = "";
                            for ($i = 0; $i < count($this->selectedRows); $i++) {
                                $sn = $sn . "'" . $this->selectedRows[$i] . "'";
                                if ($i <> count($this->selectedRows) - 1) {
                                    $sn = $sn . ',';
                                }
                            }
                            DB::statement("UPDATE inventoryserial SET snumber=?,solddate=?,sold=?,employee_id=?,transactiondate=?
                            where serialno in (" . $sn . ")"
                            ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'], true, 'Admin', Carbon::now()]);
                        }
                    }elseif ($this->isLotNumber){
                        //สินค้ามี Lot Number                            
                        //Insert salesdetaillog
                        DB::statement("INSERT INTO salesdetaillog(snumber,sdate,itemid,description,quantity,amount,cost,location
                            ,unitofmeasure,stocktype,soreturn,goodsout,lotnumber,journal,posted,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                            ,$this->adjInventory['adjquantity'],$this->adjInventory['adjtotalvalue'],$this->adjInventory['adjtotalvalue'],$this->adjInventory['location']
                            ,$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype'],'N',true,$this->adjInventory['lotnumber'],'AO',true,'Admin',Carbon::now()]);

                        //Update purchasedetaillog
                        //ตรวจสอบว่า Lot นั้นมีสินค้าพอตัดหรือไม่
                        $strsql = "select sum(quantity-sold) as balance
                                from purchasedetaillog 
                                where itemid='" . $this->adjInventory['itemid'] . "'
                                and lotnumber='" . $this->adjInventory['lotnumber'] . "'";
                        $data1 = DB::select($strsql);
                        if ($data1[0]->balance < $this->adjInventory['adjquantity']){
                            $this->dispatchBrowserEvent('popup-alert', ['title' => 'สินค้าคงเหลือของ Lot ' . $this->adjInventory['lotnumber'] . ' ไม่พอปรับปรุงออก !',]);
                            return;
                        }

                        //Loop เพื่อตัดสินค้าออก
                        $xcount = 0;
                        while ($xcount < $this->adjInventory['adjquantity']) {
                            $strsql = "select id,sold,quantity,quantity-sold as balance 
                                    from purchasedetaillog 
                                    where itemid='" . $this->adjInventory['itemid'] . "'
                                    and lotnumber='" . $this->adjInventory['lotnumber'] . "'
                                    and quantity-sold > 0
                                    order by id";
                            $data1 = DB::select($strsql);
                            if ($data1[0]->balance <= $this->adjInventory['adjquantity'] - $xcount) {   
                                DB::statement("UPDATE purchasedetaillog SET sold=sold+?,employee_id=?,transactiondate=?
                                            where id =" . $data1[0]->id
                                ,[$data1[0]->balance, 'Admin', Carbon::now()]);                                
                                $xcount = $xcount + $data1[0]->balance;
                            }else{
                                DB::statement("UPDATE purchasedetaillog SET sold=sold+?,employee_id=?,transactiondate=?
                                where id =" . $data1[0]->id
                                ,[$this->adjInventory['adjquantity'] - $xcount, 'Admin', Carbon::now()]);
                                $xcount = $xcount + ($this->adjInventory['adjquantity'] - $xcount);
                            }
                        }
                    }else{
                        //สินค้าไม่มี Serial No.
                        //Insert salesdetaillog
                        DB::statement("INSERT INTO salesdetaillog(snumber,sdate,itemid,description,quantity,amount,cost,location
                        ,unitofmeasure,stocktype,soreturn,goodsout,journal,posted,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                        ,$this->adjInventory['adjquantity'],$this->adjInventory['adjtotalvalue'],$this->adjInventory['adjtotalvalue'],$this->adjInventory['location']
                        ,$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype'],'N',true,'AO',true,'Admin',Carbon::now()]);
                    }

                    $this->dispatchBrowserEvent('hide-adjustInventoryForm',['message' => 'Create Successfully!']);
                });
            }
        }
    }

    public function updatedAdjInventoryAdjquantity() //Calulate adjtotalvalue
    {
        if ($this->adjustType == "in"){
            try {
                $this->adjInventory['adjtotalvalue'] = $this->adjInventory['adjquantity'] * $this->adjInventory['adjvalue'];
            } catch (\Throwable $th) {
                $this->adjInventory['adjtotalvalue'] = 0;
                return false;
            }  
        }else if ($this->adjustType == "out"){
            try {
                $this->adjInventory['adjtotalvalue'] = $this->adjInventory['adjquantity'] * $this->adjInventory['averagecost'];
            } catch (\Throwable $th) {
                $this->adjInventory['adjtotalvalue'] = 0;
                return false;
            }  
        }

        $this->adjInventory['adjtotalvalue'] = round($this->adjInventory['adjtotalvalue'], 2);
    }

    public function updatedAdjInventoryAdjvalue() //Calulate adjtotalvalue
    {
        try {
            $this->adjInventory['adjtotalvalue'] = $this->adjInventory['adjquantity'] * $this->adjInventory['adjvalue'];
        } catch (\Throwable $th) {
            $this->adjInventory['adjtotalvalue'] = 0;
            return false;
        }
        $this->adjInventory['adjtotalvalue'] = round($this->adjInventory['adjtotalvalue'], 2);
    }

    public function updatedAdjInventoryItemid() //Start create new adjust
    {
        $this->reset(['isSerial','isLotNumber']);

        $data = DB::table('inventory')
        ->select('inventory.id','inventory.itemid','inventory.description','b.other as stocktypename','c.other as category','inventory.location'
                ,'d.other as locationname','inventory.unitofmeasure','inventory.instock','inventory.instockvalue','inventory.stocktype'
                ,'inventory.brand','inventory.isserial','inventory.averagecost')
        ->leftJoin('misctable as b', function ($join) {
            $join->on('inventory.stocktype', '=', 'b.code')
                    ->where('b.tabletype', 'I1');
                }) 
        ->leftJoin('misctable as c', function ($join) {
            $join->on('inventory.category', '=', 'c.code')
                    ->where('c.tabletype', 'CA');
                })
        ->leftJoin('misctable as d', function ($join) {
            $join->on('inventory.location', '=', 'd.code')
                    ->where('d.tabletype', 'LO');
                })
        ->where('inventory.itemid',$this->adjInventory['itemid'])
        ->get();

        if (count($data) > 0) //Check for clear value in itemid (select2)
        {
            $this->adjInventory = json_decode(json_encode($data[0]), true);

            $this->adjInventory['instock'] = round($this->adjInventory['instock'],2);
            $this->adjInventory['instockvalue'] = round($this->adjInventory['instockvalue'],2);
            $this->adjInventory['adjtotalvalue'] = 0;
            $this->adjInventory['documentno'] = getGlNunber("GL");
            $this->adjInventory['adjustdate'] = Carbon::now()->format('Y-m-d');
            $this->adjInventory['lotnumber'] = "";

            if ($this->adjInventory['stocktype'] == "4") {
                $this->isSerial = $this->adjInventory['isserial'];
                $this->adjInventory['adjvalue'] = 0;
            }
            if ($this->adjInventory['stocktype'] == "9"){
                $this->isLotNumber = true;
            }
            
        }
    }

    public function addNew()
    {
        $this->reset(['adjInventory','adjustType', 'genGLs', 'sumDebit', 'sumCredit','serialDetails','isSerial','isLotNumber'
                    ,'sumSerialQty','sumSerialCost','duplicate_sn','selectedRows']);
        $this->dispatchBrowserEvent('show-adjustInventoryForm');
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function render()
    {
        //Dropdown
        $this->inventorys_dd = DB::table('inventory')
        ->select('itemid','description')
        ->orderBy('itemid')
        ->get();

        $strsql = "SELECT account, accnameother FROM account WHERE detail=true ORDER BY account";
        $this->account_dd = DB::select($strsql);

        $this->location_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'LO')
        ->orderBy('code')
        ->get();

        $this->color_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'CL')
        ->orderBy('code')
        ->get();

        $adjlogs = DB::table('inventoryadjlog')
        ->select('inventoryadjlog.id','inventoryadjlog.documentno','inventoryadjlog.itemid','inventory.description as description'
                ,'inventoryadjlog.adjquantity','inventoryadjlog.adjvalue','misctable.other as location','inventoryadjlog.isadjustin'
                ,'inventoryadjlog.transactiondate')
        ->leftJoin('misctable', function ($join) {
            $join->on('inventoryadjlog.location', '=', 'misctable.code')
                 ->where('misctable.tabletype', 'LO');
                }) 
        ->leftJoin('inventory', 'inventoryadjlog.itemid', '=', 'inventory.itemid')
        ->Where(function($query) {
            $query->where('inventoryadjlog.documentno', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventoryadjlog.itemid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventory.description', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('misctable.other', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventoryadjlog.transactiondate', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.inventory.adjust-inventory',[
            'adjlogs' => $adjlogs,
        ]);
    }
}
