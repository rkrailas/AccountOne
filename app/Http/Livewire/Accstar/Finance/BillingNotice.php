<?php

namespace App\Http\Livewire\Accstar\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BillingNotice extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public $sortDirection = "desc";
    public $sortBy = "billingnotice.billingno";
    public $numberOfPage = 10;
    public $searchTerm = null;
    
    public $showEditModal = null;
    public $customers_dd; //Dropdown
    public $sNumberDelete;
    public $billingHeader = [];
    public $billingDetails = [];
    public $sumBalance;

    public $selectedRows = [];
    public $selectPageRows = false;

    public function updatedSelectPageRows($value)
    {
        $this->selectedRows = [];
        if ($value){
            for($i=0; $i<count($this->billingDetails);$i++)
            {
                $this->selectedRows[] = $this->billingDetails[$i]['taxdataid'];
            }
        }else{
            $this->reset(['selectedRows', 'selectPageRows']);
        }
    }

    public function clearValue()
    {
        $this->reset(['billingHeader','billingDetails','sNumberDelete','sumBalance','selectedRows']);
    }

    public function updatedbillingHeaderCustomerid()
    {
        if ($this->billingHeader['customerid'] != " " && $this->billingHeader['customerid'] != " ") {
            // billingHeader (billingno,customerid,billingdate,duedate,customername,addressl1,addressl2,addressl3,notes,amount,employee_id,transactiondate)
            $data = DB::table('customer')
            ->selectRaw("customerid, name as customername, address11 as addressl1, address12 as addressl2
                        , city1 || ' ' || state1 || ' ' || zipcode1 as addressl3")
            ->where('customerid', $this->billingHeader['customerid'])
            ->get();
            $data = json_decode(json_encode($data), true);

            // babkDetails (billingno,balance,amount,taxref,taxdataid,employee_id,transactiondate)
            $strsql = "select tax.id as taxdataid, tax.taxnumber as taxref, tax.amount - tax.paidamount as balance
                    , tax.duedate, tax.description, tax.description
                    from taxdata tax
                    left join billingnoticedetail bill on tax.id = bill.taxdataid
                    where tax.purchase=false and tax.iscancelled=false
                    and tax.customerid='" . $this->billingHeader['customerid'] . "'"
                    . " and tax.paidamount < tax.amount and bill.taxdataid is null";
            $data = DB::select($strsql);

            if (count($data) > 0){
                $this->billingDetails = json_decode(json_encode($data), true);
            };
        }
    }

    public function createUpdateBillingNotice()
    {        
        if ($this->showEditModal == true){
            //===Edit=== ไม่ได้ใช้งาน
            // DB::transaction(function () {
            //     // BillingNotice
            //     DB::statement("UPDATE billingnotice SET billingdate=?,duedate=?,notes=?,closed=?,employee_id=?,transactiondate=?
            //                 where billingno=?" 
            //     , [$this->billingHeader['billingdate'], $this->billingHeader['duedate'], $this->billingHeader['notes']
            //     , true, 'Admin', Carbon::now(), $this->billingHeader['billingno']]);

            //     $this->dispatchBrowserEvent('hide-billingNoticeForm');
            //     $this->dispatchBrowserEvent('alert',['message' => 'Save Successfully!']);
            // });
        }else{
            //===Insert====
            DB::transaction(function () {
                // BillingNotice (billingno,customerid,billingdate,duedate,customername,addressl1,addressl2,addressl3,notes,amount,employee_id,transactiondate)
                if ($this->selectedRows) {
                    $data = DB::table('taxdata')
                            ->selectRaw("sum(amount - paidamount) as amount")
                            ->whereIn('id', $this->selectedRows)
                            ->get();
                    if (count($data) > 0){
                        $data = json_decode(json_encode($data[0]), true);
                    };

                    DB::statement("INSERT INTO billingnotice(billingno,customerid,billingdate,duedate,notes,amount,closed,employee_id,transactiondate)
                    VALUES(?,?,?,?,?,?,?,?)"
                    , [$this->billingHeader['billingno'], $this->billingHeader['customerid'], $this->billingHeader['billingdate']
                    , $this->billingHeader['duedate'], $this->billingHeader['notes'], $data['amount'], true, 'Admin', Carbon::now()]); 
    
                    // Billingnoticedetail (billingno,balance,amount,taxref,taxdataid,employee_id,transactiondate)
                    // ตัดเฉพาะส่วนที่เลือก
                    $data = DB::table('taxdata')
                                ->selectRaw("id as taxdataid, taxnumber as taxref, amount - paidamount as balance, description")
                                ->whereIn('id', $this->selectedRows)
                                ->get();
                    if (count($data) > 0){
                        $data = json_decode(json_encode($data), true);
                    };

                    for($i=0; $i<count($data);$i++)
                    {
                        DB::statement("INSERT INTO billingnoticedetail(billingno,balance,amount,taxref,taxdataid,description,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?)"
                        , [$this->billingHeader['billingno'], $data[$i]['balance'], $data[$i]['balance'], $data[$i]['taxref'], $data[$i]['taxdataid']
                        , $data[$i]['description'], 'Admin', Carbon::now()]); 
                    } 
                }

                $this->dispatchBrowserEvent('hide-billingNoticeForm');
                $this->dispatchBrowserEvent('alert',['message' => 'Save Successfully!']);
            });
        }
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
        $this->showEditModal = false;

        $this->clearValue();
        $this->billingHeader['billingno'] = "";
        $this->billingHeader['billingdate'] = Carbon::now()->format('Y-m-d');
        $this->billingHeader['duedate'] = Carbon::now()->addMonth()->format('Y-m-d');
        $this->billingHeader['notes'] = "";
        $this->billingHeader['amount'] = 0;

        $this->dispatchBrowserEvent('show-billingNoticeForm'); //แสดง Model Form
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function calculateSummary() //ทำทุกครั้งที่มีการ Render
    {
        $this->sumBalance = 0;

        for($i=0; $i<count($this->billingDetails);$i++)
        {
            $this->sumBalance = $this->sumBalance + $this->billingDetails[$i]['balance'];
        }
        
        $this->sumBalance = round($this->sumBalance, 2);
    }

    public function edit($billingNo)
    {
        $this->showEditModal = TRUE;
        $this->clearValue();

        // billingHeader
        $strsql = "select billingno, bill.customerid, cus.customerid || ': ' || cus.name as customername
            , to_char(billingdate,'YYYY-MM-DD') as billingdate, to_char(duedate,'YYYY-MM-DD') as duedate, bill.closed, bill.notes
            from billingnotice bill
            join customer cus on bill.customerid=cus.customerid
            where billingno='" . $billingNo . "'";
        $data = DB::select($strsql);

        if (count($data) > 0){
            $this->billingHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        }

        // billingDetails
        $strsql = "select billingno, balance, taxref, taxdataid, description
            from billingnoticedetail bill
            where bill.billingno = '" . $billingNo . "'";
        $data = DB::select($strsql);

        if(count($data) > 0){
            $this->billingDetails = json_decode(json_encode($data), true); 

            for($i=0; $i<count($this->billingDetails);$i++)
            {
                $this->billingDetails[$i]['balance'] = round($this->billingDetails[$i]['balance'],2);
            }
        }

        $this->dispatchBrowserEvent('show-billingNoticeForm'); //แสดง Model Form
    }

    public function confirmDelete($billingNo) //แสดง Modal ยืนยันการลบ
    {
        $this->sNumberDelete = $billingNo;
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() //กดปุ่ม Delete ที่ List รายการ
    {   
        DB::transaction(function() 
        {
            DB::table('billingnotice')->where('billingno', $this->sNumberDelete)->delete();
            DB::table('billingnoticedetail')->where('billingno', $this->sNumberDelete)->delete();
        });
    }
    
    public function render()
    {
        // .Summary grid     
        if($this->billingDetails != Null)
        {
            $this->calculateSummary();
        }else{
            //$this->clearValue();
        }


        // Bind Data to Dropdown
        $this->customers_dd = DB::table('customer')
        ->select('customerid','name','taxid')
        ->where('debtor',true)
        ->orderBy('customerid')
        ->get();

        $billingNotices = DB::table('billingnotice')
        ->selectRaw("billingnotice.billingno, billingnotice.billingdate, billingnotice.duedate, billingnotice.amount
                ,customer.customerid || ': ' ||customer.name as customername
                ,sum(taxdata.amount-taxdata.paidamount) as balance")
        ->join('customer','billingnotice.customerid','=','customer.customerid')
        ->join('billingnoticedetail','billingnotice.billingno','=','billingnoticedetail.billingno')
        ->join('taxdata', 'billingnoticedetail.taxdataid', '=', 'taxdata.id')
        ->where('billingnotice.closed', true)
        ->where('taxdata.purchase', false)
        ->where('taxdata.iscancelled', false)
        ->whereRaw('taxdata.amount <> taxdata.paidamount')
        ->Where(function($query) 
        {
            $query->where('billingnotice.billingno', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('billingnotice.billingdate', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('billingnotice.duedate', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('billingnotice.amount', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('customer.name', 'like', '%'.$this->searchTerm.'%');
        })
        ->groupBy('billingnotice.billingno', 'billingnotice.billingdate', 'billingnotice.duedate', 'billingnotice.amount'
                , 'customer.customerid', 'customer.name')
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.accstar.finance.billing-notice',[
            'billingNotices' => $billingNotices
        ]);
    }
}
