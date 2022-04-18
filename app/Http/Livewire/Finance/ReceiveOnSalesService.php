<?php

namespace App\Http\Livewire\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Support\Collection;

class ReceiveOnSalesService extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $sortDirection = "desc";
    public $sortBy = "a.gjournaldt";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $showEditModal = null;
    public $customers_dd, $taxTypes_dd, $accountNos_dd, $billingNotices_dd; //Dropdown
    public $sNumberDelete;
    public $bankHeader = [];
    public $bankDetails = [];
    public $sumPlus, $sumDeduct, $sumBalance, $sumAR = 0;
    public $genGLs = [];
    public $sumDebit, $sumCredit = 0;
    public $selectCustomerid, $billingNo;

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

        // 1.Dr เงินสดหรือเงินฝากธนาคาร 
        if ($this->bankHeader['amount']) {
            $accountCode = "";
            $accountName = "";

            if ($this->bankHeader['account']) {
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
                'gjournal' => 'JR', 'gltran' => $this->bankHeader['gltran'], 'gjournaldt' => $this->bankHeader['gjournaldt'], 'glaccount' => $accountCode, 'glaccname' => $accountName, 'gldescription' => $this->bankHeader['notes'], 'gldebit' => $this->bankHeader['amount'], 'glcredit' => 0, 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => 'Admin', 'transactiondate' => Carbon::now()
            ]);
        }

        // 2.Dr.ภาษีถูกหัก ณ ที่จ่าย 
        if ($this->bankHeader['witholdtax']) {
            $accountCode = "";
            $accountName = "";

            if ($this->bankHeader['accounttax']) {
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
                'gjournal' => 'JR', 'gltran' => $this->bankHeader['gltran'], 'gjournaldt' => $this->bankHeader['gjournaldt'], 'glaccount' => $accountCode, 'glaccname' => $accountName, 'gldescription' => 'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref'], 'gldebit' => $this->bankHeader['witholdtax'], 'glcredit' => 0, 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => 'Admin', 'transactiondate' => Carbon::now()
            ]);
        }

        // 3.Dr.ส่วนลดจ่าย
        if ($this->bankHeader['findiscount']) {
            $accountCode = "";
            $accountName = "";

            if ($this->bankHeader['accountdis']) {
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
                'gjournal' => 'JR', 'gltran' => $this->bankHeader['gltran'], 'gjournaldt' => $this->bankHeader['gjournaldt'], 'glaccount' => $accountCode, 'glaccname' => $accountName, 'gldescription' => 'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref'], 'gldebit' => $this->bankHeader['findiscount'], 'glcredit' => 0, 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => 'Admin', 'transactiondate' => Carbon::now()
            ]);
        }

        // 4.Dr.ค่าธรรมเนียม
        if ($this->bankHeader['feeamt']) {
            $accountCode = "";
            $accountName = "";

            if ($this->bankHeader['accountfee']) {
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
                'gjournal' => 'JR', 'gltran' => $this->bankHeader['gltran'], 'gjournaldt' => $this->bankHeader['gjournaldt'], 'glaccount' => $accountCode, 'glaccname' => $accountName, 'gldescription' => 'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref'], 'gldebit' => $this->bankHeader['feeamt'], 'glcredit' => 0, 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => 'Admin', 'transactiondate' => Carbon::now()
            ]);
        }

        // 5.Cr.ค่าปรับ (รายได้เบ็ดเตล็ด)
        if ($this->bankHeader['fincharge']) {
            $accountCode = "";
            $accountName = "";

            if ($this->bankHeader['accountcharge']) {
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
                'gjournal' => 'JR', 'gltran' => $this->bankHeader['gltran'], 'gjournaldt' => $this->bankHeader['gjournaldt'], 'glaccount' => $accountCode, 'glaccname' => $accountName, 'gldescription' => 'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref'], 'gldebit' => 0, 'glcredit' => $this->bankHeader['fincharge'], 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => 'Admin', 'transactiondate' => Carbon::now()
            ]);
        }

        // 6.Cr.ลูกหนี้การค้า //account = $bankHeader['accountcus'] or buyer.account or controldef.account where id='AR'
        if ($this->sumAR) {
            $accountCode = "";
            $accountName = "";

            if ($this->bankHeader['accountcus']) {
                $accountCode = $this->bankHeader['accountcus'];
            } else {
                $data = DB::table('buyer')
                    ->select("account")
                    ->where('customerid', $this->bankHeader['customerid'])
                    ->get();

                if ($data->count() > 0) {
                    $accountCode = $data[0]->account;
                } else {
                    $data = DB::table('controldef')
                        ->select("account")
                        ->where('id', 'AR')
                        ->get();

                    if ($data->count() > 0) {
                        $accountCode = $data[0]->account;
                    }
                }
            }

            if ($accountCode != "") {
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
                'gjournal' => 'JR', 'gltran' => $this->bankHeader['gltran'], 'gjournaldt' => $this->bankHeader['gjournaldt'], 'glaccount' => $accountCode, 'glaccname' => $accountName, 'gldescription' => 'รับเงิน - บัญชีลูกหนี้' . ' - ' . $this->bankHeader['documentref'], 'gldebit' => 0, 'glcredit' => $this->sumAR, 'jobid' => '', 'department' => '', 'allocated' => 0, 'currencyid' => '', 'posted' => false, 'bookid' => '', 'employee_id' => 'Admin', 'transactiondate' => Carbon::now()
            ]);
        }
        //.Dr.ลูกหนี้การค้า 

        // Summary Debit & Credit
        for ($i = 0; $i < count($this->genGLs); $i++) {
            $this->sumDebit = $this->sumDebit + $this->genGLs[$i]['gldebit'];
            $this->sumCredit = $this->sumCredit + $this->genGLs[$i]['glcredit'];
        }
    }

    public function clearValue()
    {
        $this->reset(['bankHeader','bankDetails','sumPlus','sumDeduct','sumBalance','sumAR','billingNo']);
        $this->bankHeader['amount'] = 0;
    }

    // public function updatedBillingNo()
    // {
    //     $this->bankDetails = [];
    //     $this->sumPlus = 0;
    //     $this->sumDeduct = 0;
    //     $this->sumBalance = 0;
    //     $this->sumAR = 0;
    //     $this->bankHeader['amount'] = 0;

    //     // babkDetails
    //     $strsql = "select '' as gltran, description, amount-paidamount as balance, 0 as findiscount, 0 as amount, 'B1' as journal
    //             , taxnumber as taxref, taxamount as tax, taxdate, 'R1' as bookid, id as taxdataid, amount as totalamount
    //             , amount as oriamount, taxamount as oritax
    //             from taxdata
    //             where iscancelled=false and purchase=false and amount > paidamount
    //             and customerid = '" . $this->bankHeader['customerid'] . "'
    //             and id in (select taxdataid from billingnoticedetail where billingno='" . $this->billingNo . "')";
    //     $data = DB::select($strsql);
    //     $this->bankDetails = json_decode(json_encode($data), true);

    //     for ($i = 0; $i < count($this->bankDetails); $i++) {
    //         $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'], 2);
    //         $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'], 2);
    //     }
    // }

    public function updatedSelectCustomerid()
    {
        $this->clearValue();

        if ($this->selectCustomerid != " ") {
            // Get & Bind Billingno
            // $strsql = "select b.billingno, c.name, b.amount
            //         from billingnotice b
            //         join customer c on b.customerid=c.customerid
            //         where b.closed=true and b.paid=false
            //         and b.customerid='" . $this->selectCustomerid . "'";
            // $this->billingNotices_dd = DB::select($strsql);
            // $newOption = "<option value=''>---โปรดเลือก---</option>";
            // foreach ($this->billingNotices_dd as $row) {
            //     $newOption = $newOption . "<option value='" . $row->billingno . "'>"
            //         . $row->billingno . " (" . number_format($row->amount, 2) . ") </option>";
            // }
            // $this->dispatchBrowserEvent('bindToBillingNo', ['newOption' => $newOption]);

            // bankHeader
            $this->bankHeader = ([
                'gjournaldt' => Carbon::now()->format('Y-m-d'), 'documentref' => '', 'customerid' => $this->selectCustomerid, 'customername' => ''
                , 'addressl1' => '', 'addressl2' => '', 'addressl3' => '', 'amount' => 0, 'findiscount' => 0, 'fincharge' => 0, 'feeamt' => 0
                , 'payby' => '', 'journal' => 'JR', 'bookid' => 'RV', 'account' => '', 'accountcus' => '', 'accounttax' => '', 'accountcharge' => ''
                , 'accountdis' => '', 'accountfee' => '', 'taxscheme' => '', 'witholdamt' => 0, 'witholdtax' => 0, 'witholdtaxrate' => 0, 'taxscheme1' => ''
                , 'witholdamt1' => 0, 'witholdtax1' => 0, 'witholdtaxrate1' => 0, 'taxtype' => '2', 'taxrunningno' => '', 'posted' => false
                , 'department' => '', 'notes' => '', 'gltran' => getGlNunber('JR'), 'billingno' => ''
            ]);

            $data = DB::table('customer')
                ->selectRaw("name, address11, address12, city1 || ' ' || state1 || ' ' || zipcode1 as address3, buyer.account")
                ->Join('buyer', 'customer.customerid', '=', 'buyer.customerid')
                ->where('customer.customerid', $this->selectCustomerid)
                ->get();
            $data = json_decode(json_encode($data), true);

            if (count($data) > 0) {
                $this->bankHeader['customername'] = $data[0]['name'];
                $this->bankHeader['address1'] = $data[0]['address11'];
                $this->bankHeader['address2'] = $data[0]['address12'];
                $this->bankHeader['address3'] = $data[0]['address3'];
                $this->bankHeader['accountcus'] = $data[0]['account'];
                $this->bankHeader['notes'] = 'รับเงิน - บัญชีลูกหนี้ - ' . $data[0]['name']; 
            }

            // babkDetails
            $data = DB::table('taxdataservice')
                ->selectRaw("'' as gltran, description, amount-paidamount as balance, 0 as findiscount, 0 as amount, 'B1' as journal
                , taxnumber as taxref, taxamount as tax, taxdate, 'RV' as bookid, id as taxdataid, amount as totalamount
                , amount as oriamount, taxamount as oritax")
                ->where('customerid', $this->selectCustomerid)
                ->where('iscancelled', false)
                ->where('purchase', false)
                ->whereRaw('amount > paidamount')
                ->get();
            $this->bankDetails = json_decode(json_encode($data), true);

            for ($i = 0; $i < count($this->bankDetails); $i++) {
                $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'], 2);
                $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'], 2);
            }
        }
    }

    public function createUpdateReceiveOnSales()
    {
        if ($this->showEditModal == true) {
            //===Edit===
            DB::transaction(function () {
                // Bank
                $xAmount = $this->bankHeader['amount'] - ($this->bankHeader['witholdtax'] + $this->bankHeader['witholdtax1']);
                DB::statement(
                    "UPDATE bank SET gjournaldt=?, payby=?, documentref=?, taxrunningno=?, taxscheme=?, witholdamt=?, witholdtax=?, witholdtaxrate=?
                            , taxscheme1=?, witholdamt1=?, witholdtax1=?, witholdtaxrate1=?, account=?, accountcus=?, accounttax=?, accountcharge=?
                            , accountdis=?, accountfee=?, employee_id=?, transactiondate=?, posted=?, amount=?, notes=?
                            where gltran=?",
                    [
                        $this->bankHeader['gjournaldt'], $this->bankHeader['payby'], $this->bankHeader['documentref'], $this->bankHeader['taxrunningno']
                        , $this->bankHeader['taxscheme'], $this->bankHeader['witholdamt'], $this->bankHeader['witholdtax'], $this->bankHeader['witholdtaxrate']
                        , $this->bankHeader['taxscheme1'], $this->bankHeader['witholdamt1'], $this->bankHeader['witholdtax1'], $this->bankHeader['witholdtaxrate1']
                        , $this->bankHeader['account'], $this->bankHeader['accountcus'], $this->bankHeader['accounttax'], $this->bankHeader['accountcharge']
                        , $this->bankHeader['accountdis'], $this->bankHeader['accountfee'], 'Admin', Carbon::now(), $this->bankHeader['posted']
                        , $xAmount, $this->bankHeader['notes'], $this->bankHeader['gltran']
                    ]
                );

                //===ปิดรายกาาร===
                if ($this->bankHeader['posted']) {
                    DB::table('gltran')->insert($this->genGLs);
                }

                // Bankdetail & Taxdata
                foreach ($this->bankDetails as $row) {
                    if ($this->bankHeader['posted']) {
                        //===ปิดรายกาาร===
                        DB::statement(
                            "UPDATE bankdetail SET amount=?, employee_id=?, transactiondate=?
                        where id=?",
                            [$row['amount'], 'Admin', Carbon::now(), $row['id']]
                        );

                        DB::statement(
                            "UPDATE taxdataservice SET paidamount=paidamount+?, paiddate=?, employee_id=?, transactiondate=?
                        where id=?",
                            [$row['amount'], $this->bankHeader['gjournaldt'], 'Admin', Carbon::now(), $row['taxdataid']]
                        );

                        //Insert Taxdata
                        $strsql = "select * from taxdataservice 
                            where iscancelled=false 
                            and id='" . $this->bankDetails[0]['taxdataid'] . "'";
                        $taxdataService = DB::select($strsql);

                        DB::statement(
                            "INSERT INTO taxdata(taxnumber, taxdate, journaldate, reference, gltran, customerid, description, amountcur, amount, taxamount
                                        , paidamount, paiddate, duedate, findiscount, remark, purchase, posted, returngoods, iscancelled, isinputtax, isupdated
                                        , location, totalamount, currencyrate1, employee_id, transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                                [
                                    $this->bankHeader['documentref'], $this->bankHeader['gjournaldt'], $this->bankHeader['gjournaldt'], $this->bankHeader['gltran']
                                    , $this->bankHeader['gltran'], $this->bankHeader['customerid'], $taxdataService[0]->description, $taxdataService[0]->amountcur
                                    , $taxdataService[0]->amount, $taxdataService[0]->taxamount, $this->bankHeader['amount'], $this->bankHeader['gjournaldt']
                                    , $taxdataService[0]->duedate, $taxdataService[0]->findiscount, $taxdataService[0]->remark, $taxdataService[0]->purchase, true
                                    , $taxdataService[0]->returngoods, $taxdataService[0]->iscancelled, $taxdataService[0]->isinputtax, $taxdataService[0]->isupdated
                                    , $taxdataService[0]->location, $taxdataService[0]->totalamount, $taxdataService[0]->currencyrate1, 'Admin', Carbon::now()
                                ]
                        );

                        DB::statement(
                            "UPDATE taxdataservice SET paidamount=?, paiddate=?, paidtaxamount=?, employee_id=?, transactiondate=?
                        where id=?",
                            [$this->bankHeader['amount'], $this->bankHeader['gjournaldt'], $taxdataService[0]->taxamount, 'Admin', Carbon::now(), $this->bankDetails[0]['taxdataid']]
                        );

                    } else {

                        DB::statement(
                            "UPDATE bankdetail SET amount=?, employee_id=?, transactiondate=?
                        where id=?",
                            [$row['amount'], 'Admin', Carbon::now(), $row['id']]
                        );
                    }
                }

                $this->dispatchBrowserEvent('hide-receiveOnSalesServiceForm');
                $this->dispatchBrowserEvent('alert', ['message' => 'Save Successfully!']);
            });
        } else {
            //===Insert====
            DB::transaction(function () {
                // Bank
                DB::statement(
                    "INSERT INTO bank(gltran, gjournaldt, documentref, customerid, customername, addressl1, addressl2, addressl3
                , amount, findiscount, fincharge, feeamt, payby, journal, bookid, account, accountcus, accounttax, accountcharge, accountdis
                , accountfee, taxscheme, witholdamt, witholdtax, witholdtaxrate, billingno, employee_id, transactiondate, notes)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [
                        $this->bankHeader['gltran'], $this->bankHeader['gjournaldt'], $this->bankHeader['documentref'], $this->bankHeader['customerid']
                        , $this->bankHeader['customername'], $this->bankHeader['address1'], $this->bankHeader['address2'], $this->bankHeader['address3']
                        , $this->bankHeader['amount'], $this->bankHeader['findiscount'], $this->bankHeader['fincharge'], $this->bankHeader['feeamt']
                        , $this->bankHeader['payby'], $this->bankHeader['journal'], $this->bankHeader['bookid'], $this->bankHeader['account']
                        , $this->bankHeader['accountcus'], $this->bankHeader['accounttax'], $this->bankHeader['accountcharge'], $this->bankHeader['accountdis']
                        , $this->bankHeader['accountfee'], $this->bankHeader['taxscheme'], $this->bankHeader['witholdamt'], $this->bankHeader['witholdtax']
                        , $this->bankHeader['witholdtaxrate'], $this->bankHeader['billingno'], 'Admin', Carbon::now(), $this->bankHeader['notes']
                    ]
                );


                for ($i = 0; $i < count($this->bankDetails); $i++) {
                    if ($this->bankDetails[$i]['amount'] > 0) {
                        DB::statement(
                            "INSERT INTO bankdetail(gltran, description, balance, findiscount, amount, journal, taxref, tax, taxdate
                        , bookid, taxdataid, totalamount, employee_id, transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                            [
                                $this->bankHeader['gltran'], $this->bankDetails[$i]['description'], $this->bankDetails[$i]['balance']
                                , $this->bankDetails[$i]['findiscount'], $this->bankDetails[$i]['amount'], $this->bankDetails[$i]['journal']
                                , $this->bankDetails[$i]['taxref'], $this->bankDetails[$i]['tax'], $this->bankDetails[$i]['taxdate']
                                , $this->bankDetails[$i]['bookid'], $this->bankDetails[$i]['taxdataid'], $this->bankDetails[$i]['totalamount']
                                , 'Admin', Carbon::now()
                            ]
                        );
                    }
                }

                $this->dispatchBrowserEvent('hide-receiveOnSalesServiceForm');
                $this->dispatchBrowserEvent('alert', ['message' => 'Save Successfully!']);
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

    public function updatedBankHeaderWitholdamt()
    {
        $this->bankHeader['witholdtax'] = 0;

        if ($this->bankHeader['witholdtaxrate']) {
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdamt'] * $this->bankHeader['witholdtaxrate'] / 100, 2);
        }
    }

    public function updatedBankHeaderWitholdamt1()
    {
        $this->bankHeader['witholdtax1'] = 0;

        if ($this->bankHeader['witholdtaxrate1']) {
            $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdamt1'] * $this->bankHeader['witholdtaxrate1'] / 100, 2);
        }
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

    public function addNew()
    {
        $this->showEditModal = FALSE;
        $this->bankHeader = [];
        $this->bankDetails = [];

        $this->dispatchBrowserEvent('show-receiveOnSalesServiceForm'); //แสดง Model Form
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function addRowInGrid() //กดปุ่มสร้าง Row ใน Grid
    {
        //สร้าง Row ว่างๆ ใน Gird
        $this->bankDetails = [];
        // $this->bankDetails[] = ([
        //     'taxref'=>'', 'description'=>'', 'balance'=>0, 'amount'=>0, 'tax'=>0,'oriamount'=>0, 'oritax'=>0
        // ]);
    }

    public function updated($item) 
    {
        $xxx = explode(".",$item); 

        //ตรวจสอบว่าเป็นการแก้ไขข้อมูลที่ Grid หรือไม่
        if($xxx[0] == "bankDetails") 
        {
            $this->calculateSummary();
        }        
    }

    public function calculateSummary() //ทำทุกครั้งที่มีการ Render
    {
        //$this->bankHeader['witholdamt'] = 0; //ตามข้อ 4 วันที่ 15/11/64
        $this->sumBalance = 0;
        $this->sumAR = 0;
        $sumReceieAmount = 0; //ยอดรับก่อนหัก W/H

        for ($i = 0; $i < count($this->bankDetails); $i++) {
            //ตามข้อ 4 วันที่ 15/11/64
            // $this->bankHeader['witholdamt'] = round($this->bankHeader['witholdamt'] + ($this->bankDetails[$i]['amount'] -
            //     ($this->bankDetails[$i]['amount'] * $this->bankDetails[$i]['oritax'] / $this->bankDetails[$i]['oriamount'])), 2);

            $sumReceieAmount = $sumReceieAmount + $this->bankDetails[$i]['amount'];

            $this->sumBalance = $this->sumBalance  + $this->bankDetails[$i]['balance'];

            $this->sumAR = $this->sumAR  + $this->bankDetails[$i]['amount'];
        }

        if ($this->bankHeader['witholdtaxrate']) {
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdamt'] * $this->bankHeader['witholdtaxrate'] / 100, 2);
        }

        $this->bankHeader['amount'] = round($sumReceieAmount -  $this->bankHeader['witholdtax'] -  $this->bankHeader['witholdtax1']
            + $this->sumPlus - $this->sumDeduct, 2);
    }

    public function edit($gltran)
    {
        $this->showEditModal = TRUE;
        $this->clearValue();

        // bankHeader
        $strsql = "select gltran, to_char(gjournaldt,'YYYY-MM-DD') as gjournaldt, customerid, customername, documentref, amount
            , taxscheme, witholdamt, witholdtax, witholdtaxrate, taxscheme1, witholdamt1, witholdtax1, witholdtaxrate1
            , payby, account, accountcus, accounttax, taxrunningno, posted, billingno, notes
            , fincharge, findiscount, feeamt, accountcharge, accountdis, accountfee
            from bank 
            where gltran='" . $gltran . "'";
        $data = DB::select($strsql);

        if (count($data) > 0) {
            $this->bankHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ

            $this->bankHeader['amount'] = round($this->bankHeader['amount'], 2);
            $this->bankHeader['witholdamt'] = round($this->bankHeader['witholdamt'], 2);
            $this->bankHeader['witholdtax'] = round($this->bankHeader['witholdtax'], 2);
            $this->bankHeader['witholdtaxrate'] = round($this->bankHeader['witholdtaxrate'], 2);
            $this->bankHeader['witholdamt1'] = round($this->bankHeader['witholdamt1'], 2);
            $this->bankHeader['witholdtax1'] = round($this->bankHeader['witholdtax1'], 2);
            $this->bankHeader['witholdtaxrate1'] = round($this->bankHeader['witholdtaxrate1'], 2);
            $this->bankHeader['fincharge'] = round($this->bankHeader['fincharge'], 2);
            $this->bankHeader['findiscount'] = round($this->bankHeader['findiscount'], 2);
            $this->bankHeader['feeamt'] = round($this->bankHeader['feeamt'], 2);

            $this->calPlusDeduct();
        }

        // bankDetails
        $strsql = "select bankdetail.id, bankdetail.taxref, bankdetail.description, bankdetail.balance, bankdetail.amount, bankdetail.tax
            , taxdataservice.amount as oriamount, taxdataservice.taxamount as oritax, bankdetail.taxdataid
            from bankdetail
            join taxdataservice on bankdetail.taxdataid = taxdataservice.id
            where bankdetail.gltran = '" . $gltran . "'";
        $data = DB::select($strsql);

        if (count($data) > 0) {
            $this->bankDetails = json_decode(json_encode($data), true);

            for ($i = 0; $i < count($this->bankDetails); $i++) {
                $this->bankDetails[$i]['balance'] = round($this->bankDetails[$i]['balance'], 2);
                $this->bankDetails[$i]['amount'] = round($this->bankDetails[$i]['amount'], 2);
            }
        }

        $this->dispatchBrowserEvent('show-receiveOnSalesServiceForm'); //แสดง Model Form
    }

    public function confirmDelete($gltran) //แสดง Modal ยืนยันการลบ
    {
        $this->sNumberDelete = $gltran;
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() //กดปุ่ม Delete ที่ List รายการ
    {
        DB::transaction(function () {
            DB::table('bank')->where('gltran', $this->sNumberDelete)->delete();
            DB::table('bankdetail')->where('gltran', $this->sNumberDelete)->delete();
        });
    }

    public function render()
    {
        // Summary grid
        if ($this->bankDetails != Null) {
            $this->calculateSummary();
        } else {
            $this->clearValue();
        }

        // Bind Data to Dropdown
        $strsql = "SELECT customerid, name, taxid FROM customer WHERE debtor=true ORDER BY customerid";
        $this->customers_dd = DB::select($strsql);

        $this->taxTypes_dd = DB::table('taxtable')
            ->select('code', 'description', 'taxrate')
            ->where('taxtype', '2')
            ->orderBy('code')
            ->get();

        $strsql = "SELECT account, accnameother FROM account WHERE detail=true ORDER BY account";
        $this->accountNos_dd = DB::select($strsql);

        // ใบสำคัญรับเงินที่ยังไม่ ปิดรายการ
        $strsql = "SELECT a.gltran, a.gjournaldt, b.customerid || ' : ' || b.name as customername, a.amount, a.transactiondate
            FROM bank a
            JOIN customer b ON a.customerid=b.customerid
            WHERE a.posted=false AND a.bookid='RV'
                AND (a.gltran ILIKE '%" . $this->searchTerm . "%'
                    OR b.customerid ILIKE '%" . $this->searchTerm . "%'
                    OR b.name ILIKE '%" . $this->searchTerm . "%'
                    OR CAST(a.amount AS TEXT) ILIKE '%" . $this->searchTerm . "%')
            ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $recieptJournals = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        $salesOrders = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.finance.receive-on-sales-service', [
            'recieptJournals' => $recieptJournals
        ]);
    }
}
