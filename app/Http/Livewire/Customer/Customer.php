<?php

namespace App\Http\Livewire\Customer;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class Customer extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "customer.customerid";
    public $numberOfPage = 10;
    public $searchTerm = null;
    
    public $showEditModal;    
    public $citys_dd, $states_dd, $accountNos_dd, $taxs_dd, $taxs1_dd, $taxs1Ap_dd, $priceLevels_dd; //Dropdown
    public $state = [];

    //Not Support Thai
    // public function exportPDF(){
    //     return Excel::download(new CustomersExport($this->searchTerm), 'Customers.pdf');
    // }

    public function refreshData()
    {
        $this->resetPage();
    }

    public function exportExcel(){
        return Excel::download(new CustomersExport($this->searchTerm), 'Customers.xlsx');
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
        $this->state = [
                'customerid'=>'','name'=>'','names'=>'','name1'=>'','taxid'=>'','branchno'=>'','debtor'=>FALSE,'creditor'=>FALSE,'corporate'=>FALSE
                ,'address11'=>'','address12'=>'','city1'=>'','state1'=>'','zipcode1'=>'','phone1'=>'','fax1'=>'','email1'=>'','contact1'=>'','notes1'=>''
                ,'creditlimit'=>0,'discountday'=>0,'discount'=>0,'dueday'=>0,'generaldiscount'=>0,'termdiscount'=>'','account'=>''
                ,'tax'=>'','tax1'=>'','pricelevel'=>''
                ,'creditlimit_ap'=>0,'discountday_ap'=>0,'discount_ap'=>0,'dueday_ap'=>0,'generaldiscount_ap'=>0,'termdiscount_ap'=>'','account_ap'=>''
                ,'tax_ap'=>'','tax1_ap'=>'','pricelevel_ap'=>'','discountontotal_ap'=>FALSE,'exclusivetax_ap'=>FALSE
        ];
        $this->dispatchBrowserEvent('show-customerForm');
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function createCustomer()
    {        
        // $validateData = Validator::make($this->state, [
        //         'customerid' => 'required|unique:customer,customerid',
        //     ])->validate();
        
        $strsql = "select customerid from customer where customerid='" . $this->state['customerid'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
                $this->dispatchBrowserEvent('popup-alert', [
                        'title' => 'มีรหัสลูกค้านี้อยู่แล้ว !',
                        ]);
        }else{
                DB::transaction(function () {
                        DB::statement("INSERT INTO customer(customerid,name,names,name1,taxid,branchno,debtor,creditor,corporate
                        ,address11,address12,city1,state1,zipcode1,phone1,fax1,email1,contact1,notes1,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        ,[$this->state['customerid'],$this->state['name'],$this->state['names'],$this->state['name1'],$this->state['taxid'],$this->state['branchno']
                        ,convertToBoolean($this->state['debtor']),convertToBoolean($this->state['creditor']),convertToBoolean($this->state['corporate'])
                        ,$this->state['address11'],$this->state['address12'],$this->state['city1'],$this->state['state1'],$this->state['zipcode1']
                        ,$this->state['phone1'],$this->state['fax1'],$this->state['email1'],$this->state['contact1'],$this->state['notes1'],'Admin'
                        , Carbon::now()]);
                
                        DB::statement("INSERT INTO buyer(customerid,creditlimit,discountday,discount,dueday,generaldiscount,termdiscount
                        ,account,tax,tax1,pricelevel,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)" 
                        ,[$this->state['customerid'],$this->state['creditlimit'],$this->state['discountday'],$this->state['discount'],$this->state['dueday']
                        ,$this->state['generaldiscount'],$this->state['termdiscount'],$this->state['account'],$this->state['tax'],$this->state['tax1']
                        ,$this->state['pricelevel'],'Admin', Carbon::now()]);

                        DB::statement("INSERT INTO vendor(customerid,creditlimit,discountday,discount,dueday,generaldiscount,termdiscount,account
                        ,tax,tax1,pricelevel,discountontotal,exclusivetax,employee_id,transactiondate)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)" 
                        ,[$this->state['customerid'],$this->state['creditlimit_ap'],$this->state['discountday_ap'],$this->state['discount_ap']
                        ,$this->state['dueday_ap'],$this->state['generaldiscount_ap'],$this->state['termdiscount_ap'],$this->state['account_ap']
                        ,$this->state['tax_ap'],$this->state['tax1_ap'],$this->state['pricelevel_ap'],convertToBoolean($this->state['discountontotal_ap'])
                        ,convertToBoolean($this->state['exclusivetax_ap']),'Admin', Carbon::now()]);
                });
        
                $this->dispatchBrowserEvent('hide-customerForm');
                $this->dispatchBrowserEvent('alert',['message' => 'Create Successfully!']);        
        }
    }

    public function updateCustomer()
    {       
        DB::transaction(function () {
            DB::statement("UPDATE customer SET name=?, names=?, name1=?, taxid=?, branchno=?, debtor=?, creditor=?, corporate=?
                , address11=?, address12=?, city1=?, state1=?, zipcode1=?, phone1=?, fax1=?, email1=?, contact1=?, notes1=?
                , employee_id=?, transactiondate=?
                where customerid=?" 
                ,[$this->state['name'],$this->state['names'],$this->state['name1'],$this->state['taxid'],$this->state['branchno']
                ,convertToBoolean($this->state['debtor']),convertToBoolean($this->state['creditor']),convertToBoolean($this->state['corporate'])
                ,$this->state['address11'],$this->state['address12'],$this->state['city1'],$this->state['state1'],$this->state['zipcode1']
                ,$this->state['phone1'],$this->state['fax1'],$this->state['email1'],$this->state['contact1'],$this->state['notes1']
                ,'Admin', Carbon::now(), $this->state['customerid']]);
            
            DB::statement("UPDATE buyer SET creditlimit=?, discountday=?, discount=?, dueday=?, generaldiscount=?
                , termdiscount=?, account=?, tax=?, tax1=?, pricelevel=?, employee_id=?, transactiondate=? 
                where customerid=?" 
                ,[$this->state['creditlimit'],$this->state['discountday'],$this->state['discount'],$this->state['dueday'],$this->state['generaldiscount']
                ,$this->state['termdiscount'],$this->state['account'],$this->state['tax'],$this->state['tax1'],$this->state['pricelevel']
                ,'Admin', Carbon::now(),$this->state['customerid']]);

            DB::statement("UPDATE vendor SET creditlimit=?, discountday=?, discount=?, dueday=?, generaldiscount=?
                , termdiscount=?, account=?, tax=?, tax1=?, pricelevel=?, discountontotal=?, exclusivetax=?, employee_id=?, transactiondate=?
                where customerid=?" 
                ,[$this->state['creditlimit_ap'],$this->state['discountday_ap'],$this->state['discount_ap'],$this->state['dueday_ap'],$this->state['generaldiscount_ap']
                ,$this->state['termdiscount_ap'],$this->state['account_ap'],$this->state['tax_ap'],$this->state['tax1_ap'],$this->state['pricelevel_ap']
                ,convertToBoolean($this->state['discountontotal_ap']),convertToBoolean($this->state['exclusivetax_ap']), 'Admin'
                , Carbon::now(),$this->state['customerid']]);
        });

        $this->dispatchBrowserEvent('hide-customerForm');
        $this->dispatchBrowserEvent('alert',['message' => 'Update Successfully!']);
    }

    public function edit($customerId)
    {
        $this->showEditModal = TRUE;

        //ดึงข้อมูลลูกค้าคนนั้น ๆ
        $data = DB::table('customer')
                -> select('customer.id','customer.customerid'
                        ,'customer.name','customer.names','customer.name1'
                        ,'customer.taxid','customer.branchno','customer.debtor','customer.creditor','customer.corporate'
                        ,'customer.address11','customer.address12','customer.city1','customer.state1','customer.zipcode1'
                        ,'customer.phone1','customer.fax1','customer.email1','customer.contact1','customer.notes1'
                        ,'buyer.creditlimit','buyer.discountday','buyer.discount','buyer.dueday','buyer.generaldiscount'
                        ,'buyer.termdiscount','buyer.account','buyer.tax','buyer.tax1','buyer.pricelevel'
                        ,'vendor.creditlimit as creditlimit_ap','vendor.discountday as discountday_ap','vendor.discount as discount_ap'
                        ,'vendor.dueday as dueday_ap','vendor.generaldiscount as generaldiscount_ap'
                        ,'vendor.termdiscount as termdiscount_ap','vendor.account as account_ap','vendor.tax as tax_ap','vendor.tax1 as tax1_ap'
                        ,'vendor.pricelevel as pricelevel_ap','vendor.discountontotal as discountontotal_ap','vendor.exclusivetax as exclusivetax_ap'
                        )
                -> leftJoin('buyer', 'customer.customerid', '=', 'buyer.customerid')
                -> leftJoin('vendor', 'customer.customerid', '=', 'vendor.customerid')
                -> where('customer.customerid','=',$customerId)
                -> get();
        
        //Convert เป็น Arrat 1 มิติ
        $this->state = json_decode(json_encode($data[0]), true);

        //Bind Buyer Account
        $newOption = "<option value=''>---โปรดเลือก---</option>";
        foreach ($this->accountNos_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
            if ($row['account'] == $this->state['account']) {
                $newOption = $newOption . "selected='selected'"; 
            }
            $newOption = $newOption . ">" . $row['account'] . " : " . $row['accname'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect', ['newOption' => $newOption, 'selectName' => '#account-select2']);

        //Bind Vendor Account
        $newOption = "<option value=''>---โปรดเลือก---</option>";
        foreach ($this->accountNosAp_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
            if ($row['account'] == $this->state['account_ap']) {
                $newOption = $newOption . "selected='selected'"; 
            }
            $newOption = $newOption . ">" . $row['account'] . " : " . $row['accname'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect', ['newOption' => $newOption, 'selectName' => '#account_ap-select2']);

        $this->dispatchBrowserEvent('show-customerForm');
    }

    public function updatingSearchTerm()
    {
        //กรณีมีการเปลี่ยนคำค้นหา ให้กลับไปหน้าแรก
        $this->resetPage();
    }

    public function render()
    {
        // .กำหนดค่า Dropdown ใน Form Model
        //City
        $first = DB::table('zipcodet')
                -> selectRaw("CONCAT('เขต', TRIM(city)) as city")
                -> where('state','=','กรุงเทพฯ')
                -> groupBy('city')
                -> get();
        $second = DB::table('zipcodet')
                -> selectRaw("CONCAT('อำเภอ', TRIM(city)) as city")
                -> where('state','!=','กรุงเทพฯ')                
                -> groupBy('city')
                -> get();
        $this->citys_dd = $first->merge($second);

        //state
        $this->states_dd = DB::table('zipcodet')
                -> select('state')
                -> groupBy('state')
                -> orderBy('state')
                -> get();

        //Account
        $strsql = "SELECT account, accname FROM account WHERE detail=true ORDER BY acctype, account";
        $this->accountNos_dd = DB::select($strsql);
        $this->accountNosAp_dd = DB::select($strsql);
                
        // $this->accountNosAp_dd = DB::table('account')
        //         -> select('account','accname')
        //         -> where('detail',TRUE)
        //         -> orderBy('acctype')
        //         -> orderBy('account')
        //         -> get();
        
        //Tax
        $this->taxs_dd = DB::table('taxtable')
                -> select('code','description')
                -> where('taxtype','1')
                -> get();
        $this->taxsAp_dd = DB::table('taxtable')
                -> select('code','description')
                -> where('taxtype','1')
                -> get();
        
        //Tax1
        $this->taxs1_dd = DB::table('taxtable')
                -> select('code','description')
                -> where('taxtype','2')
                -> get();
        $this->taxs1Ap_dd = DB::table('taxtable')
                -> select('code','description')
                -> where('taxtype','3')
                -> get();

        //Price Level
        $this->priceLevels_dd = DB::table('misctable')
                -> select('code','description')
                -> where('tabletype','PL')
                -> orderBy('code')
                -> get();
        // /.กำหนดค่า Dropdown ใน Form Model
        
        $customers = DB::table('customer')
        ->select('customer.customerid','customer.name','customer.contact1','customer.phone1'
                ,'customer.taxid','customer.debtor','customer.creditor','customer.corporate')
        ->whereNotNull('customerid')
        ->Where(function($query) {
                $query->where('customer.customerid', 'ilike', '%'.$this->searchTerm.'%')
                      ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%');
                })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.customer.customer',[
            'customers' => $customers,
        ]);        
    }
}