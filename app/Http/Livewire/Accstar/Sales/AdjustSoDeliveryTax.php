<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdjustSoDeliveryTax extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $sortDirection = "desc";
    public $sortBy = "sales.transactiondate";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $showEditModal = null;
    public $soHeader = [];
    public $soDetails = [];
    public $sumQuantity = 0;
    public $salesAcs_dd;
    public $sNumberDelete;
    public $genGLs = [];
    public $sumDebit, $sumCredit = 0;
    public $taxNumber;
    public $errorValidate, $errorTaxNumber, $errorGLTran = false;

    public function resetValuesInModel()
    {
        $this->taxNumber = "";
        $this->soHeader = [];
        $this->soHeader = ([
            'snumber' => '', 'sonumber' => '', 'sodate' => Carbon::now()->format('Y-m-d'), 'invoiceno' => '', 'invoicedate' => Carbon::now()->format('Y-m-d')
            , 'deliveryno' => '', 'deliverydate' => Carbon::now()->addMonth()->format('Y-m-d'), 'journaldate' => Carbon::now()->format('Y-m-d')
            , 'exclusivetax' => TRUE, 'salesaccount' => '', 'taxrate' => getTaxRate(), 'salestax' => 0, 'discountamount' => 0, 'sototal' => 0
            , 'customerid' => '', 'shipcost' => 0, 'shipname' => '', 'posted' => false, 'sonote' => ''
            , 'refno' => ''
        ]);

        $this->soDetails = [];
        $this->addRowInGrid();
    }

    public function searchDoc()
    {
        //ตรวจสอบว่าปรับปรุงหรือยัง
        if ($this->taxNumber) {
            $strsql = "select sonumber from sales where soreturn='D' and refno='" . $this->taxNumber . "'";
            $data =  DB::select($strsql);
            if (count($data)) {
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'ใบกำกับภาษีนำไปปรับปรุงแล้ว !',
                ]);
                $this->resetValuesInModel();
            } else {
                //หา Taxdata > sale + saledetail 
                $strsql = "select taxnumber, reference from taxdata
                where iscancelled=false and purchase=false 
                and taxnumber='" . $this->taxNumber . "'";
                $data =  DB::select($strsql);

                if (count($data)) {
                    $data = json_decode(json_encode($data[0]), true);

                    $xSoNumber = $data['reference'];

                    //soHeader
                    $data = DB::table('sales')
                        ->selectRaw("sonumber,snumber,to_char(sodate,'YYYY-MM-DD') as sodate, invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate
                                , deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate, payby
                                , CONCAT(customer.customerid,': ', customer.name) as shipname
                                , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                                , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                                , taxontotal, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid
                                , shipcost, refno")
                        ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
                        ->where('sonumber', $xSoNumber)
                        ->where('soreturn', 'N')
                        ->get();
                    $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
                    $this->soHeader['discountamount'] = round($this->soHeader['discountamount'], 2);
                    $this->soHeader['shipcost'] = round($this->soHeader['shipcost'], 2);
                    $this->soHeader['salestax'] = round($this->soHeader['salestax'], 2);
                    $this->soHeader['sototal'] = round($this->soHeader['sototal'], 2);
                    $this->soHeader['sonumber'] = getCnDocNunber('SO');
                    $this->soHeader['sodate'] = Carbon::now()->format('Y-m-d');
                    $this->soHeader['deliveryno'] = getGlNunber('SO');
                    $this->soHeader['deliverydate'] = Carbon::now()->format('Y-m-d');
                    $this->soHeader['invoiceno'] = getCnTaxNunber('SO');
                    $this->soHeader['invoicedate'] = Carbon::now()->format('Y-m-d');
                    $this->soHeader['closed'] = false;
                    $this->soHeader['sonote'] = "ปรับปรุงราคาขายใบกำกับ " . $this->taxNumber;

                    //ค้นหา Account Code ที่บันทึกไว้ตอนขาย
                    $strsql = "select sa.salesaccount from taxdata tx
                                join sales sa on tx.reference=sa.sonumber and tx.customerid=sa.customerid
                                where tx.taxnumber='" . $this->taxNumber . "'";
                    $data2 =  DB::select($strsql);
                    if (count($data2)) {
                         $this->soHeader['salesaccount'] = $data2[0]->salesaccount;
                    }

                    //Bind salesaccount
                    $newOption = "<option value=''>---โปรดเลือก---</option>";
                    foreach ($this->salesAcs_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                        if ($row['account'] == $this->soHeader['salesaccount']) { 
                            $newOption = $newOption . "selected='selected'"; 
                        }
                        $newOption = $newOption . ">" . $row['account'] . " : " . $row['accnameother'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect', ['newOption' => $newOption, 'selectName' => '#salesaccount-select2']);

                    //soDetails
                    $data2 = DB::table('salesdetaillog')
                        ->selectRaw('itemid,description,quantity,salesac,unitprice,taxrate,taxamount,id,inventoryac,0 as cost')
                        ->where('snumber', $xSoNumber)
                        ->where('soreturn', 'N')
                        ->get();
                    $this->soDetails = json_decode(json_encode($data2), true);

                    $this->reCalculateInGrid();

                    //$this->dispatchBrowserEvent('show-soDeliveryTaxForm'); //แสดง Model Form
                } else {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => 'ไม่พบใบกำกับภาษี !',
                    ]);
                    $this->resetValuesInModel();
                }
            }
        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'กรุณาป้อนเลขที่ใบกำกับ !',
            ]);
        }
        
    }

    public function updatingNumberOfPage()
    {
        $this->resetPage();
    }

    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;
        if ($this->sortDirection == "asc") {
            $this->sortDirection = "desc";
        } else {
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

        $this->genGLs = [];
        $this->sumDebit = 0;
        $this->sumCredit = 0;

        //1.Dr.ลูกหนี้การค้า //account = buyer.account or controldef.account where id='AR' //gldebit = $soHeader['sototal']
        $buyAcc = "";
        $buyAccName = "";

        $data = DB::table('buyer')
            ->select("account")
            ->where('customerid', $this->soHeader['customerid'])
            ->get();
        if ($data->count() > 0) {
            $buyAcc = $data[0]->account;
        }

        if ($buyAcc == "") {
            $data = DB::table('controldef')
                ->select("account")
                ->where('id', 'AR')
                ->get();
            if ($data->count() > 0) {
                $buyAcc = $data[0]->account;
            }
        }

        if ($buyAcc != "") {
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
        if ($this->soHeader['sototal'] >= 0) {
            $xGLDebit = $this->soHeader['sototal'];
            $xGLCredit = 0;
        } else {
            $xGLDebit = 0;
            $xGLCredit = $this->soHeader['sototal'] * -1;
        }

        $this->genGLs[] = ([
            'gjournal' => 'SO', 'gltran' => $xgltran, 'gjournaldt' => $this->soHeader['journaldate'], 'glaccount' => $buyAcc, 'glaccname' => $buyAccName
            , 'gldescription' => $this->soHeader['sonote'], 'gldebit' => $xGLDebit, 'glcredit' => $xGLCredit, 'jobid' => ''
            , 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => ''
            , 'employee_id' => '', 'transactiondate' => Carbon::now()
        ]);


        //2.Cr.ขายสินค้า //glcredit = $soDetails['netamount'] //glaccount = salesdetail.salesac or controldef.account where id='SA'
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
                'gjournal' => 'SO', 'gltran' => $xgltran, 'gjournaldt' => $this->soHeader['journaldate'], 'glaccount' => $salesAcc
                , 'glaccname' => $salesAccName, 'gldescription' => $this->soHeader['sonote'], 'gldebit' => $xGLDebit
                , 'glcredit' => $xGLCredit, 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => ''
                , 'employee_id' => '', 'transactiondate' => Carbon::now()
            ]);
        }

        //3.Cr.ภาษีขาย // glcredit = $soHeader['salestax'] // glaccount = controldef.account where id='ST';     
        $taxAcc = "";
        $taxAccName = "";

        $data = DB::table('controldef')
            ->select("account")
            ->where('id', 'ST')
            ->get();
        if ($data->count() > 0) {
            $taxAcc = $data[0]->account;
        }

        if ($taxAcc != "") {
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
        if ($this->soHeader['salestax'] >= 0) {
            $xGLDebit = 0;
            $xGLCredit = $this->soHeader['salestax'];
        } else {
            $xGLDebit = $this->soHeader['salestax'] * -1;
            $xGLCredit = 0;
        }

        $this->genGLs[] = ([
            'gjournal' => 'SO', 'gltran' => $xgltran, 'gjournaldt' => $this->soHeader['journaldate'], 'glaccount' => $taxAcc, 'glaccname' => $taxAccName
            , 'gldescription' => $this->soHeader['sonote'], 'gldebit' => $xGLDebit, 'glcredit' => $xGLCredit, 'jobid' => ''
            , 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => ''
            , 'transactiondate' => Carbon::now()
        ]);
        
        // Summary Debit & Credit
        for ($i = 0; $i < count($this->genGLs); $i++) {
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
        $this->resetValuesInModel();
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
            'itemid' => '', 'description' => '', 'quantity' => 0, 'salesac' => '', 'unitprice' => 0, 'amount' => 0, 'discountamount' => 0, 'netamount' => 0, 'taxamount' => 0, 'taxrate' => getTaxRate()
        ]);
    }

    public function createUpdateSalesOrder() //กดปุ่ม Save 
    {
        //ตรวจสอบเลขที่ใบสั่งขาย ใบกำกับ ใบสำคัญซ้ำหรือไม่
        $strsql = "select count(*) as count from taxdata where purchase=false and taxnumber='" . $this->soHeader['invoiceno'] . "'";
        $data = DB::select($strsql);
        if ($data[0]->count){
            $this->errorInvoiceNo = true;
            $this->errorValidate = true;
        }

        $strsql = "select count(*) as count from gltran where gltran='" . $this->soHeader['deliveryno'] . "'";
        $data = DB::select($strsql);
        if ($data[0]->count){
            $this->errorGLTran = true;
            $this->errorValidate = true;
        }

        $strsql = "select count(*) as count from glmast where gltran='" . $this->soHeader['deliveryno'] . "'";
        $data = DB::select($strsql);
        if ($data[0]->count){
            $this->errorGLTran = true;
            $this->errorValidate = true;
        }

        if ($this->errorValidate){
            return;
        }
        
        if ($this->showEditModal == true) {
            //===Edit===
            DB::transaction(function () {
                //Update Sales
                DB::statement(
                    "UPDATE sales SET sodate=?, invoiceno=?, invoicedate=?, deliveryno=?, deliverydate=?, sototal=?, salestax=?
                , journaldate=?, exclusivetax=?, taxontotal=?, salesaccount=?, employee_id=?, transactiondate=?, closed=?, refno=?, sonote=?
                where sonumber=?",
                    [
                        $this->soHeader['sodate'], $this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['deliveryno']
                        , $this->soHeader['deliverydate'], $this->soHeader['sototal'], $this->soHeader['salestax'], $this->soHeader['journaldate']
                        , $this->soHeader['exclusivetax'], $this->soHeader['taxontotal'], $this->soHeader['salesaccount'], 'Admin', Carbon::now()
                        , $this->soHeader['closed'], $this->soHeader['refno'], $this->soHeader['sonote'], $this->soHeader['sonumber']
                    ]
                );

                //Closed = True Insert Taxdata & GLTran
                if ($this->soHeader['closed']) {
                    DB::statement(
                        "INSERT INTO taxdata(taxnumber,taxdate,journaldate,reference,gltran,customerid
                            ,description,amountcur,amount,taxamount,duedate,purchase,posted
                            ,isinputtax,totalamount,employee_id,transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        [
                            $this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['journaldate'], $this->soHeader['snumber']
                            , $this->soHeader['deliveryno'], $this->soHeader['customerid'], $this->soHeader['sonote'], $this->soHeader['sototal']
                            , $this->soHeader['sototal'], $this->soHeader['salestax'], $this->soHeader['duedate'], FALSE, TRUE, TRUE
                            , $this->soHeader['sototal'], 'Admin', Carbon::now()
                        ]
                    );

                    //gltran
                    $this->generateGl($this->soHeader['deliveryno']);
                    DB::table('gltran')->insert($this->genGLs);
                }

                //SalesDetail
                DB::table('salesdetail')->where('snumber', $this->soHeader['sonumber'])->delete();
                foreach ($this->soDetails as $soDetails2) {
                    if ($this->soHeader['exclusivetax'] == true) //แปลงค่าก่อนบันทึก
                    {
                        $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                    }

                    //Closed = True (update salesdetail > quantitydel=quantityord, quantity=0, quantitybac=0)
                    if ($this->soHeader['closed'] == true) {
                        $xquantity = 0;
                        $xquantityord = $soDetails2['quantity'];
                        $xquantitydel = $soDetails2['quantity'];
                        $xquantitybac = 0;
                    } else {
                        $xquantity = $soDetails2['quantity'];
                        $xquantityord = $soDetails2['quantity'];
                        $xquantitydel = 0;
                        $xquantitybac = $soDetails2['quantity'];
                    }

                    DB::statement(
                        "INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount
                    , quantity, quantityord, quantitydel, quantitybac, taxrate, taxamount, soreturn, cost
                    , salesac, employee_id, transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        [
                            $this->soHeader['sonumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description']
                            , $soDetails2['unitprice'], $soDetails2['amount'], $xquantity, $xquantityord, $xquantitydel, $xquantitybac
                            , $soDetails2['taxrate'], $soDetails2['taxamount'], 'D', $soDetails2['cost'], $soDetails2['salesac']
                            , 'Admin', Carbon::now()
                        ]
                    );
                }

                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm', ['message' => 'Save Successfully!']);
            });
        } else {
            //===New===

            //ตรวจสอบเลขที่เอกสารซ้ำหรือไม่
            $validateData = Validator::make($this->soHeader, [
                'sonumber' => 'required|unique:sales,sonumber',
              ])->validate();

            DB::transaction(function () {
                //Insert Sales
                DB::statement(
                    "INSERT INTO sales(snumber, sonumber, sodate, customerid, invoiceno, invoicedate
                            , deliveryno, deliverydate, payby, duedate, journaldate, exclusivetax, taxontotal
                            , salesaccount, expirydate, sototal, salestax, closed, refno, soreturn, sonote
                            , employee_id, transactiondate) 
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [
                        $this->soHeader['snumber'], $this->soHeader['sonumber'], $this->soHeader['sodate'], $this->soHeader['customerid']
                        , $this->soHeader['invoiceno'], $this->soHeader['invoicedate'], $this->soHeader['deliveryno'], $this->soHeader['deliverydate']
                        , $this->soHeader['payby'], $this->soHeader['duedate'], $this->soHeader['journaldate'], $this->soHeader['exclusivetax']
                        , $this->soHeader['taxontotal'], $this->soHeader['salesaccount'], Carbon::now()->addMonths(6), $this->soHeader['sototal']
                        , $this->soHeader['salestax'], $this->soHeader['closed'], $this->taxNumber, 'D', $this->soHeader['sonote'], 'Admin', Carbon::now()
                    ]
                );

                //SalesDetail
                foreach ($this->soDetails as $soDetails2) {
                    if ($this->soHeader['exclusivetax'] == true) //แปลงค่าก่อนบันทึก
                    {
                        $soDetails2['amount'] = $soDetails2['amount'] + $soDetails2['taxamount'];
                    }

                    $xquantity = $soDetails2['quantity'];
                    $xquantityord = $soDetails2['quantity'];
                    $xquantitydel = 0;
                    $xquantitybac = $soDetails2['quantity'];

                    DB::statement(
                        "INSERT INTO salesdetail(snumber, sdate, itemid, description, unitprice, amount
                    , quantity, quantityord, quantitydel, quantitybac, taxrate, taxamount, soreturn, cost, salesac
                    , employee_id, transactiondate)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        [
                            $this->soHeader['sonumber'], $this->soHeader['sodate'], $soDetails2['itemid'], $soDetails2['description'], $soDetails2['unitprice']
                            , $soDetails2['amount'], $xquantity, $xquantityord, $xquantitydel, $xquantitybac, $soDetails2['taxrate'], $soDetails2['taxamount']
                            , 'D', $soDetails2['cost'], $soDetails2['salesac'], 'Admin', Carbon::now()
                        ]
                    );
                }

                $this->dispatchBrowserEvent('hide-soDeliveryTaxForm', ['message' => 'Save Successfully!']);
            });
        };
    }

    public function updated($item) //Event จากการ Update Property ของ Livewire มันจะส่ง Property หรือตัวแปรที่มีการ update มาให้ เช่น $soHeader, $soDetails
    {
        $xxx = explode(".", $item); //$item = soHeader.sodate หรือ soDetails.0.quantity

        //ตรวจสอบว่าเป็นการ Update Dropdown ของลูกค้าหรือไม่ ถ้าใช่จะเอาที่อยู่มาใส่ให้
        if ($item == "soHeader.customerid") {
            $data = DB::table('customer')
                ->selectRaw("COALESCE(address11,'') || ' ' || COALESCE(address12,'') || ' ' ||
                        COALESCE(city1,'') || ' ' || COALESCE(state1,'') || ' ' || COALESCE(zipcode1,'') as full_address")
                ->where('customerid', $this->soHeader['customerid'])
                ->get();
            if (count($data) > 0) {
                $this->soHeader['full_address'] = $data[0]->full_address;
            }
        }

        //ตรวจสอบว่าเป็นการแก้ไขข้อมูลที่ Grid หรือไม่
        if ($xxx[0] == "soDetails") {
            $index = $xxx[1];
            $itemName = $xxx[2];

            //Get new item description
            if ($itemName == "itemid") {
                $data = DB::table('inventory')
                    ->select('description')
                    ->where('itemid', $this->soDetails[$index][$itemName])
                    ->first();
                $data = json_decode(json_encode($data), true);
                $this->soDetails[$index]['description'] = $data['description'];
            }

            //ตรวจสอบว่าเป้นการแก้ไข quantity หรือ unitprice หรือ discountamount
            if ($itemName == "quantity" || $itemName == "unitprice" || $itemName == "cost" || $itemName == "taxrate") {
                $this->reCalculateInGrid();
            }
        }
    }

    public function reCalculateInGrid()
    {
        for ($i = 0; $i < count($this->soDetails); $i++) {
            //$this->soDetails[$index]['amount'] ยอดก่อน VAT และส่วนลด
            //$this->soDetails[$index]['netamount'] ยอดรวม VAT หักส่วนลด
            if ($this->soHeader['exclusivetax'] == TRUE) {
                $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity']
                    * ($this->soDetails[$i]['cost'] - $this->soDetails[$i]['unitprice']), 2);
            } else {
                $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity'] *
                    (($this->soDetails[$i]['cost'] - $this->soDetails[$i]['unitprice'])
                        - ($this->soDetails[$i]['cost'] - $this->soDetails[$i]['unitprice']) * 7 / 107), 2);
            }

            //ตรวจสอบ Taxrate เป็น Null หรือไม่
            if ($this->soDetails[$i]['taxrate'] == null) {
                if ($this->soHeader['taxrate']) {
                    $this->soDetails[$i]['taxrate'] = $this->soHeader['taxrate'];
                } else {
                    $this->soDetails[$i]['taxrate'] = 0;
                }
            }

            //ตรวจสอบ Amount เป็น Null หรือไม่
            if ($this->soDetails[$i]['amount'] == null) {
                $this->soDetails[$i]['amount'] = 0;
            }

            //ตรวจสอบว่า soDetails.taxamount = -0 หรือไม่ 
            $this->soDetails[$i]['taxamount'] = round(($this->soDetails[$i]['amount'] * $this->soDetails[$i]['taxrate']) / 100, 2);
            if ($this->soDetails[$i]['taxamount'] == -0) {
                $this->soDetails[$i]['taxamount'] = 0;
            }

            $this->soDetails[$i]['netamount'] = round($this->soDetails[$i]['amount'] + $this->soDetails[$i]['taxamount'], 2);
            $this->soDetails[$i]['quantity'] = round($this->soDetails[$i]['quantity'], 2);
            $this->soDetails[$i]['unitprice'] = round($this->soDetails[$i]['unitprice'], 2);
            $this->soDetails[$i]['taxrate'] = round($this->soDetails[$i]['taxrate'], 2);
            $this->soDetails[$i]['cost'] = round($this->soDetails[$i]['cost'], 2);

            //หลังจาก Re-Cal รายบรรทัดเสร็จ มันจะไปเข้า function reCalculateSummary ที่ render
        }
    }

    public function checkExclusiveTax()
    {
        $this->reCalculateInGrid();
    }

    public function reCalculateSummary()
    {
        // Summary Gird
        $this->sumQuantity = array_sum(array_column($this->soDetails, 'quantity'));
        $this->sumAmount = array_sum(array_column($this->soDetails, 'amount'));
        $this->soHeader['discountamount'] = array_sum(array_column($this->soDetails, 'discountamount'));
        $this->soHeader['sototal'] = array_sum(array_column($this->soDetails, 'netamount'));
        $this->soHeader['salestax'] = round(array_sum(array_column($this->soDetails, 'taxamount')), 2);
    }

    public function confirmDelete($snumber) //แสดง Modal ยืนยันการลบใบสั่งขาย
    {
        $this->sNumberDelete = $snumber;
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() //กดปุ่ม Delete ที่ List รายการ
    {
        DB::transaction(function () {
            DB::table('sales')->where('sonumber', $this->sNumberDelete)->delete();
            DB::table('salesdetail')->where('snumber', $this->sNumberDelete)->delete();
        });
    }

    public function edit($soNumber) //กดปุ่ม Edit ที่ List รายการ
    {
        $this->resetValuesInModel();

        $this->showEditModal = TRUE;

        //soHeader
        $data = DB::table('sales')
            ->selectRaw("sonumber,snumber,to_char(sodate,'YYYY-MM-DD') as sodate, invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate
                        , deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate, payby
                        , CONCAT(customer.customerid,': ', customer.name) as shipname
                        , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                        , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                        , taxontotal, closed, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, sonote
                        , shipcost, refno")
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('sonumber', $soNumber)
            ->get();
        $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        $this->soHeader['discountamount'] = round($this->soHeader['discountamount'], 2);
        $this->soHeader['shipcost'] = round($this->soHeader['shipcost'], 2);
        $this->soHeader['salestax'] = round($this->soHeader['salestax'], 2);
        $this->soHeader['sototal'] = round($this->soHeader['sototal'], 2);

        //soDetails
        $data2 = DB::table('salesdetail')
            ->select('itemid', 'description', 'quantity', 'salesac', 'unitprice', 'discountamount', 'taxrate', 'taxamount', 'id', 'inventoryac', 'cost')
            ->where('snumber', $soNumber)
            ->where('soreturn', 'D')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true);

        $this->reCalculateInGrid();

        $this->dispatchBrowserEvent('show-soDeliveryTaxForm'); //แสดง Model Form
        //$this->dispatchBrowserEvent('clear-select2');

        //Bind salesaccount
        $newOption = "<option value=''>---โปรดเลือก---</option>";
        foreach ($this->salesAcs_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
            if ($row['account'] == $this->soHeader['salesaccount']) { 
                $newOption = $newOption . "selected='selected'"; 
            }
            $newOption = $newOption . ">" . $row['account'] . " : " . $row['accnameother'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect', ['newOption' => $newOption, 'selectName' => '#salesaccount-select2']);
    }

    public function updatingSearchTerm() //Event นี้เกิดจากการ Key ที่ input wire:model.lazy="searchTerm"
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->sumQuantity = 0;
        $this->sumAmount = 0;
        $this->soHeader['discountamount'] = 0;
        $this->soHeader['salestax'] = 0;
        $this->soHeader['sototal'] = 0;
        $this->soHeader['customerid'] = "";
        $this->soHeader['salesaccount'] = "";
    }

    public function render()
    {
        // Summary grid     
        if ($this->soDetails != Null) {
            $this->reCalculateSummary();
        } else {
        }

        // Dropdown
        $this->salesAcs_dd = DB::table('account')
            ->select('account', 'accnameother')
            ->where('detail', TRUE)
            ->orderby('account')
            ->get();

        // getSalesOrder
        $salesOrders = DB::table('sales')
            ->select('sales.id', 'sonumber', 'snumber', 'sodate', 'name', 'sototal', 'sales.transactiondate', 'sales.refno') //sonumber=เลขที่ปรับปรุง, refno=ใบกำกับต้นทาง
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('closed', FALSE)
            ->where('soreturn', 'D')
            ->whereIn('sonumber', function ($query) {
                $query->select('snumber')->from('salesdetail')
                    ->where('quantitybac', '>', 0)
                    ->where('soreturn', 'D');
            })
            ->Where(function ($query) {
                $query->where('snumber', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('sonumber', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('sodate', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('sototal', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('sales.transactiondate', 'like', '%' . $this->searchTerm . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->numberOfPage);

        return view('livewire.accstar.sales.adjust-so-delivery-tax', [
            'salesOrders' => $salesOrders
        ]);
    }
}
