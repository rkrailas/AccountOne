<?php

namespace App\Http\Livewire\Purchase;

use App\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PoReceiveTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "a.transactiondate";
    public $numberOfPage = 10;
    public $searchTerm = null;

    protected $listeners = ['deleteConfirmed' => 'delete']; //For Popup Window
    
    public $showEditModal = null;
    public $poHeader = [];
    public $poDetails = [];
    public $sumQuantity, $sumAmount = 0;
    public $itemNos_dd, $taxRates_dd, $inventoryacs_dd, $customers_dd; //Dropdown
    public $poNumberDelete;
    public $genGLs = [];
    public $sumDebit, $sumCredit = 0;
    public $errorValidate, $errorGLTran, $errorTaxNumber = false;

    public $serialDetails, $workingRow, $searchSN; //Modal Serial No
    public $listItem, $searchItem; //Modal Item
    public $lotNumbers, $searchLotNumber; //Modal Lot Number

    public function edit($poNumber) //กดปุ่ม Edit ที่ List รายการ
    {
        // $this->showEditModal = TRUE;
        // $this->reset(['poHeader','poDetails','errorValidate','errorTaxNumber','errorGLTran']);

        // // poHeader
        // $data = DB::table('sales')
        //     ->selectRaw("ponumber,to_char(sodate,'YYYY-MM-DD') as sodate, invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate
        //                 , deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate, payby
        //                 , CONCAT(customer.customerid,': ', customer.name) as shipname
        //                 , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
        //                 , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
        //                 , taxontotal, posted, inventoryaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost, sonote")
        //     ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
        //     ->where('ponumber', $poNumber)
        //     ->where('poreturn', 'N')
        //     ->get();
        // $this->poHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        // $this->poHeader['discountamount'] = round($this->poHeader['discountamount'],2);
        // $this->poHeader['shipcost'] = round($this->poHeader['shipcost'],2);
        // $this->poHeader['salestax'] = round($this->poHeader['salestax'],2);
        // $this->poHeader['sototal'] = round($this->poHeader['sototal'],2);
        
        // // poDetails
        // $data2 = DB::table('purchasedetail')
        //     ->select('purchasedetail.itemid','purchasedetail.description','purchasedetail.quantity','purchasedetail.inventoryac','purchasedetail.unitprice'
        //             ,'purchasedetail.discountamount','purchasedetail.taxrate','purchasedetail.taxamount','purchasedetail.id','purchasedetail.inventoryac'
        //             ,'inventory.stocktype','purchasedetail.serialno','purchasedetail.lotnumber')
        //     ->join('inventory', 'purchasedetail.itemid', '=', 'inventory.itemid')
        //     ->where('ponumber', $poNumber)
        //     ->where('poreturn', 'N')
        //     ->orderBy('id')
        //     ->get();
        // $this->poDetails = json_decode(json_encode($data2), true); 

        // $this->reCalculateInGrid();

        $this->dispatchBrowserEvent('show-poReceiveTaxForm'); //แสดง Model Form
        // //$this->dispatchBrowserEvent('clear-select2');

        // //Bind Customer
        // $newOption = "<option value=''>---โปรดเลือก---</option>";
        // foreach ($this->customers_dd as $row) {
        //     $newOption = $newOption . "<option value='" . $row['customerid'] . "' ";
        //     if ($row['customerid'] == $this->poHeader['customerid']) {
        //         $newOption = $newOption . "selected='selected'"; 
        //     }
        //     $newOption = $newOption . ">" . $row['customerid'] . " : " . $row['name'] . "</option>";
        // }

        //$this->dispatchBrowserEvent('bindToSelect', ['newOption' => $newOption, 'selectName' => '#customer-select2']);
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
        $this->workingRow = $xindex; //กำลังทำงานเป็น Row ไหน ของ poDetails
        $this->getItemInModal();
        $this->dispatchBrowserEvent('show-itemListForm');
    }

    public function addRowInGrid() //กดปุ่มสร้าง Row ใน Grid
    {   
        //สร้าง Row ว่างๆ ใน Gird
        $this->poDetails[] = ([
            'itemid'=>'','description'=>'','quantity'=>0,'inventoryac'=>'','unitprice'=>0,'amount'=>0,'discountamount'=>0,'netamount'=>0
            , 'taxamount'=>0, 'taxrate'=>getTaxRate(), 'stocktype'=>'', 'serialno'=>'', 'lotnumber'=>''
        ]);

        //Re-gen Select2
        $this->dispatchBrowserEvent('regen-select2', [
            'name' => '#item-select2-' . count($this->poDetails) - 1 ,
            ]);
    }

    public function createUpdatePO() //กดปุ่ม Save 
    {
        //ตรวจสอบเลขที่ใบสั่งซื้อ ใบสำคัญซ้ำหรือไม่ / ตรวจสอบอความถูกต้องให้ poDetail
        $strsql = "select count(*) as count from gltran where gltran='" . $this->poHeader['receiveno'] . "'";
        $data = DB::select($strsql);
        if ($data[0]->count){
            $this->errorGLTran = true;
        }else{
            $strsql = "select count(*) as count from glmast where gltran='" . $this->poHeader['deliveryno'] . "'";
            $data2 = DB::select($strsql);
            if ($data2[0]->count){
                $this->errorGLTran = true;
            }else{
                $this->errorGLTran = false;
            }
        }

        if($this->poHeader['posted'] == true){
            foreach ($this->poDetails as $poDetails2){
                //ถ้าเป็นสินค้ามี SN/Lot Number จะต้องเลือก SN/Lot Number แล้ว
                if ($poDetails2['stocktype'] == "4" and $poDetails2['serialno'] == ""){
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Serial No. ของสินค้า !',]);
                    return;
                }elseif ($poDetails2['stocktype'] == "9" and $poDetails2['lotnumber'] == ""){
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'คุณยังไม่ระบุ Lot Number ของสินค้า !',]);
                    return;
                }
            }  
        }

        if ($this->errorGLTran){
            return;
        }
        
        if ($this->showEditModal == true){
            //===Edit===
            DB::transaction(function () {
                //purchaseDetail
                DB::table('purchasedetail')->where('ponumber', $this->poHeader['ponumber'])->delete();

                foreach ($this->poDetails as $poDetails2)
                {
                    if ($poDetails2['itemid']){
                        if ($this->poHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                            $poDetails2['amount'] = $poDetails2['amount'] + $poDetails2['taxamount'];
                        }
    
                        //ปิดรายการหรือไม่
                        if($this->poHeader['posted'] == true){
                            $xquantity = 0;
                            $xquantityord = $poDetails2['quantity'];
                            $xquantityrec = $poDetails2['quantity'];
                            $xquantitybac = 0;
                        }else{
                            $xquantity = $poDetails2['quantity'];
                            $xquantityord = $poDetails2['quantity'];
                            $xquantityrec = 0;
                            $xquantitybac = $poDetails2['quantity'];
                        }
    
                        DB::statement("INSERT INTO purchasedetail(ponumber, podate, itemid, description, unitprice, amount, quantity, quantityord
                        , quantityrec, quantitybac, serialno, taxrate, taxamount, discountamount, poreturn, inventoryac, lotnumber
                        , employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$this->poHeader['ponumber'], $this->poHeader['sodate'], $poDetails2['itemid'], $poDetails2['description'], $poDetails2['unitprice']
                        , $poDetails2['amount'], $xquantity, $xquantityord, $xquantityrec, $xquantitybac, $poDetails2['serialno'], $poDetails2['taxrate']
                        , $poDetails2['taxamount'], $poDetails2['discountamount'], 'N', $poDetails2['inventoryac'], $poDetails2['lotnumber']
                        , 'Admin', Carbon::now()]);
                        
                        //ถ้าปิดรายการ 
                        if ($this->poHeader['posted'] == true) {
                            
                            // ??? 16-06-22 คำนวณ averagecost
                            $strsql = "SELECT instockvalue, instock FROM inventory WHERE itemid='" . $poDetails2['itemid'] . "'";
                            $data = DB::select($strsql);
                            $xInstockvalue= 0;
                            $xInstock = 0;
                            if ($data) {
                                $xInstockvalue = $data[0]->instockvalue;
                                $xInstock = $data[0]->instock;
                            }

                            $xinventory = DB::table('inventory')
                                ->select('averagecost','instock','instockvalue')
                                ->where('itemid', $poDetails2['itemid'])
                                ->get();
                            if ($xinventory->count() > 0) {
                                $costAmt = round($poDetails2['quantity'] * $xinventory[0]->averagecost, 2);
                            }
                            
                            // purchasedetaillog
                            DB::statement("INSERT INTO purchasedetaillog(ponumber, podate, deliveryno, itemid, description, quantity, unitprice, amount
                                , quantityord, quantityrec, quantitybac, taxrate, taxamount, taxnumber, discountamount, cost, poreturn
                                , journal, posted, serialno, lotnumber, ram_purchasedetail_id, employee_id, transactiondate)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                , [$this->poHeader['ponumber'], $this->poHeader['sodate'], $this->poHeader['deliveryno'], $poDetails2['itemid']
                                , $poDetails2['description'], $poDetails2['quantity'], $poDetails2['unitprice'], $poDetails2['amount']
                                , $poDetails2['quantity'] , $xquantityrec, $xquantitybac, $poDetails2['taxrate'], $poDetails2['taxamount']
                                , $this->poHeader['invoiceno'], $poDetails2['discountamount'], $costAmt, 'N', 'SO', 'true', $poDetails2['serialno']
                                , $poDetails2['lotnumber'], $poDetails2['id'], 'Admin', Carbon::now()]);
    
                            // Inventory
                            $xinstock = $xinventory[0]->instock - $poDetails2['quantity'];
                            $xinstockvalue = $xinventory[0]->instockvalue - round($poDetails2['quantity'] * $xinventory[0]->averagecost, 2);
    
                            DB::statement("UPDATE inventory SET instock=?, instockvalue=?, employee_id=?, transactiondate=?
                                where itemid=?" 
                                , [$xinstock, $xinstockvalue, 'Admin', Carbon::now(), $poDetails2['itemid']]);
    
                            // inventoryserial & purchasedetaillog
                            if($poDetails2['stocktype'] == "4"){
                                DB::statement("UPDATE inventoryserial SET ponumber=?,solddate=?,sold=?,employee_id=?,transactiondate=?
                                        where itemid=? and serialno=?"
                                ,[$this->poHeader['ponumber'],$this->poHeader['sodate'], 'true', 'Admin', Carbon::now()
                                ,$poDetails2['itemid'],$poDetails2['serialno']]);
                            }elseif($poDetails2['stocktype'] == "9"){
                                //Loop เพื่อตัดสินค้าออก
                                $xcount = 0;
                                while ($xcount < $poDetails2['quantity']) {
                                    $strsql = "select id,sold,quantity,quantity-sold as balance 
                                            from purchasedetaillog 
                                            where itemid='" . $poDetails2['itemid'] . "'
                                            and lotnumber='" . $poDetails2['lotnumber'] . "'
                                            and quantity-sold > 0
                                            order by id";
                                    $data1 = DB::select($strsql);
                                    if ($data1[0]->balance <= $poDetails2['quantity'] - $xcount) {   
                                        DB::statement("UPDATE purchasedetaillog SET sold=sold+?,employee_id=?,transactiondate=?
                                                    where id =" . $data1[0]->id
                                        ,[$data1[0]->balance, 'Admin', Carbon::now()]);                                
                                        $xcount = $xcount + $data1[0]->balance;
                                    }else{
                                        DB::statement("UPDATE purchasedetaillog SET sold=sold+?,employee_id=?,transactiondate=?
                                        where id =" . $data1[0]->id
                                        ,[$poDetails2['quantity'] - $xcount, 'Admin', Carbon::now()]);
                                        $xcount = $xcount + ($poDetails2['quantity'] - $xcount);
                                    }
                                }
                            }
                        }
                    }                    
                }

                // Sales
                DB::statement("UPDATE sales SET sodate=?, customerid=?, invoiceno=?, invoicedate=?, deliveryno=?, deliverydate=?, sototal=?, salestax=?
                        , payby=?, duedate=?, journaldate=?, exclusivetax=?, taxontotal=?, inventoryaccount=?, employee_id=?, transactiondate=?, posted=?
                        , sonote=?
                where ponumber=?" 
                , [$this->poHeader['sodate'], $this->poHeader['customerid'], $this->poHeader['invoiceno'], $this->poHeader['invoicedate']
                , $this->poHeader['deliveryno'], $this->poHeader['deliverydate'], $this->poHeader['sototal'], $this->poHeader['salestax']
                , $this->poHeader['payby'], $this->poHeader['duedate'], $this->poHeader['journaldate'], convertToBoolean($this->poHeader['exclusivetax'])
                , convertToBoolean($this->poHeader['taxontotal']), $this->poHeader['inventoryaccount'], 'Admin', Carbon::now()
                , convertToBoolean($this->poHeader['posted']), $this->poHeader['sonote'], $this->poHeader['ponumber']]);

                // ปิดรายการ
                if ($this->poHeader['posted']){
                    // Taxdata
                    DB::statement("INSERT INTO taxdata(taxnumber,taxdate,journaldate,reference,gltran,customerid
                            ,description,amountcur,amount,taxamount,duedate,purchase,posted
                            ,isinputtax,totalamount,employee_id,transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    , [$this->poHeader['invoiceno'], $this->poHeader['invoicedate'], $this->poHeader['journaldate'], $this->poHeader['ponumber']
                    , $this->poHeader['deliveryno'], $this->poHeader['customerid'], 'ขายสินค้า-'.$this->poHeader['customerid'].'-'.$this->poHeader['ponumber']
                    , $this->poHeader['sototal'], $this->poHeader['sototal'], $this->poHeader['salestax'], $this->poHeader['duedate'], 'FALSE', 'TRUE'
                    , 'TRUE', $this->poHeader['sototal'], 'Admin', Carbon::now()]);

                    //gltran
                    $this->generateGl($this->poHeader['deliveryno']);
                    DB::table('gltran')->insert($this->genGLs);
                }

                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm',['message' => 'Save Successfully!']);
            });
        }else{
            //New
            //ตรวจสอบเลขที่เอกสารซ้ำหรือไม่
            $validateData = Validator::make($this->poHeader, [
              'ponumber' => 'required|unique:sales,sonumber',
            ])->validate();

            DB::transaction(function () {
                // Sales
                DB::statement("INSERT INTO sales(ponumber, sonumber, sodate, customerid, invoiceno, invoicedate, deliveryno, deliverydate, payby
                            , duedate, journaldate, exclusivetax, taxontotal, inventoryaccount, expirydate, sototal, salestax, closed, employee_id
                            , transactiondate, posted, sonote, ram_sodeliverytax) 
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                , [$this->poHeader['ponumber'], $this->poHeader['ponumber'], $this->poHeader['sodate'], $this->poHeader['customerid']
                , $this->poHeader['invoiceno'], $this->poHeader['invoicedate'], $this->poHeader['deliveryno'], $this->poHeader['deliverydate']
                , $this->poHeader['payby'], $this->poHeader['duedate'], $this->poHeader['journaldate'], convertToBoolean($this->poHeader['exclusivetax'])
                , convertToBoolean($this->poHeader['taxontotal']), $this->poHeader['inventoryaccount'], Carbon::now()->addMonths(6), $this->poHeader['sototal']
                , $this->poHeader['salestax'], 'true', 'Admin', Carbon::now(), convertToBoolean($this->poHeader['posted'])
                , $this->poHeader['sonote'], 'true']); //ram_sodeliverytax > แยก Type ของ SO

                //purchaseDetail
                DB::table('purchasedetail')->where('ponumber', $this->poHeader['ponumber'])->delete();
                foreach ($this->poDetails as $poDetails2)
                {
                    if ($poDetails2['itemid']){
                        if ($this->poHeader['exclusivetax'] == true){ //แปลงค่าก่อนบันทึก
                            $poDetails2['amount'] = $poDetails2['amount'] + $poDetails2['taxamount'];
                        }
    
                        if($this->poHeader['posted'] == true){ //***ถ้า New จะไม่สามารถปิดรายการได้ทันที***
                        }else{
                            $xquantity = $poDetails2['quantity'];
                            $xquantityord = $poDetails2['quantity'];
                            $xquantityrec = 0;
                            $xquantitybac = $poDetails2['quantity'];
                        }
    
                        DB::statement("INSERT INTO purchasedetail(ponumber, podate, itemid, description, unitprice, amount, quantity, quantityord
                        , quantityrec, quantitybac, taxrate, taxamount, discountamount, poreturn, inventoryac, serialno, lotnumber
                        , employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        , [$this->poHeader['ponumber'], $this->poHeader['sodate'], $poDetails2['itemid'], $poDetails2['description']
                        , $poDetails2['unitprice'], $poDetails2['amount'], $xquantity, $xquantityord, $xquantityrec, $xquantitybac
                        , $poDetails2['taxrate'], $poDetails2['taxamount'], $poDetails2['discountamount'], 'N'
                        , $poDetails2['inventoryac'], $poDetails2['serialno'], $poDetails2['lotnumber']
                        , 'Admin', Carbon::now()]);
                    }                    
                }
                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm',['message' => 'Save Successfully!']);
            });
        }
    }

    public function addNew() //กดปุ่ม สร้างข้อมูลใหม่
    {
        $this->showEditModal = FALSE;
        $this->errorGLTran = true;
        $this->reset(['poHeader', 'poDetails', 'sumQuantity', 'sumAmount', 'serialDetails', 'workingRow', 'errorGLTran']);
        $xponumber = getDocNunber("PO");

        //เลขที่ใบกำกับ=taxref / invoiceno, วันที่ในกำกับ=paiddate, เลขที่ใบสำคัญ=receiveno, วันที่ใบสำคัญ=journaldate / receivedate, คำอธิบายรายการ=gldescription
        $this->poHeader = ([
            'ponumber'=>$xponumber, 'podate'=>Carbon::now()->format('Y-m-d')
            , 'taxref'=>'', 'paiddate'=>Carbon::now()->format('Y-m-d')
            , 'receiveno'=>getGlNunber("PO"), 'journaldate'=>Carbon::now()->format('Y-m-d')
            , 'payby'=>'0', 'duedate'=>Carbon::now()->addMonth()->format('Y-m-d')
            , 'exclusivetax'=>TRUE, 'taxontotal'=>FALSE, 'poaccount'=>'', 'taxrate'=>getTaxRate()
            , 'purchasetax'=>0, 'pototal'=>0, 'customerid'=>'', 'shipcost'=>0, 'shipname'=>''
            , 'posted'=>false, 'ponote' => '', 'gldescription' => ''
        ]);
        // $this->poHeader['gldescription'] = 'ซื้อสินค้าตามใบกำกับ-' . $this->poHeader['taxref'];
        $this->addRowInGrid();
        $this->dispatchBrowserEvent('show-poReceiveTaxForm'); //แสดง Model Form
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function render()
    {
        //Bind Data to Dropdown
        // $this->itemNos_dd = DB::table('inventory')
        // ->select('itemid','description')
        // ->orderby('itemid')
        // ->get();

        // $strsql = "SELECT account, accnameother FROM account WHERE detail=true ORDER BY account";
        // $this->account_dd = DB::select($strsql);

        // $this->taxRates_dd = DB::table('taxtable')
        // ->select('code','taxrate')
        // ->where('taxtype','1')
        // ->orderby('code')
        // ->get();

        $strsql = "SELECT customerid, name, taxid FROM customer WHERE debtor=true ORDER BY customerid";
        $this->customers_dd = DB::select($strsql);

        // Get Purchase Order 23-05-2022 ยังไม่ได้ where AND a.ram_poreceivetax=true
        $strsql = "SELECT a.id, a.ponumber, a.podate, a.pototal, b.customerid || ' : ' || b.name as name, a.transactiondate
        FROM purchase a
        LEFT JOIN customer b ON a.customerid=b.customerid
        WHERE a.posted=false AND a.poreturn='N' 
            AND (a.ponumber ILIKE '%" . $this->searchTerm . "%'
                OR b.name ILIKE '%" . $this->searchTerm . "%'
                OR CAST(a.pototal AS TEXT) ILIKE '%" . $this->searchTerm . "%')
        ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $purchaseOrders = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.purchase.po-receive-tax',[
            'purchaseOrders' => $purchaseOrders,
        ]);
    }
}
