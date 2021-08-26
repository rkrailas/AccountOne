<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CustomerForm extends Component
{
    public $state = [];
    public $citys_dd, $states_dd, $accountNos_dd, $taxs_dd, $taxs1_dd, $taxs1Ap_dd, $priceLevels_dd; //Dropdown

    public function UpdateCustomer()
    {
        DB::transaction(function () {
            DB::statement("UPDATE customer SET name=?, names=?, name1=?, taxid=?, branchno=?, debtor=?, creditor=?, corporate=?
                ,address11=?, address12=?, city1=?, state1=?, zipcode1=?, phone1=?, fax1=?, email1=?, contact1=?, notes1=?
                where customerid=?" 
                ,[$this->state['name'],$this->state['names'],$this->state['name1'],$this->state['taxid'],$this->state['branchno']
                ,$this->state['debtor'],$this->state['creditor'],$this->state['corporate'],$this->state['address11']
                ,$this->state['address12'],$this->state['city1'],$this->state['state1'],$this->state['zipcode1'],$this->state['phone1']
                ,$this->state['fax1'],$this->state['email1'],$this->state['contact1'],$this->state['notes1'],$this->state['customerid']]);
            
            DB::statement("UPDATE buyer SET creditlimit=?, discountday=?, discount=?, dueday=?, generaldiscount=?
                , termdiscount=?, account=?, tax=?, tax1=?, pricelevel=? where customerid=?" 
                ,[$this->state['creditlimit'],$this->state['discountday'],$this->state['discount'],$this->state['dueday'],$this->state['generaldiscount']
                ,$this->state['termdiscount'],$this->state['account'],$this->state['tax'],$this->state['tax1'],$this->state['pricelevel'],$this->state['customerid']]);

            DB::statement("UPDATE vendor SET creditlimit=?, discountday=?, discount=?, dueday=?, generaldiscount=?
                , termdiscount=?, account=?, tax=?, tax1=?, pricelevel=?, discountontotal=?, exclusivetax=? where customerid=?" 
                ,[$this->state['creditlimit_ap'],$this->state['discountday_ap'],$this->state['discount_ap'],$this->state['dueday_ap'],$this->state['generaldiscount_ap']
                ,$this->state['termdiscount_ap'],$this->state['account_ap'],$this->state['tax_ap'],$this->state['tax1_ap'],$this->state['pricelevel_ap']
                ,$this->state['discountontotal_ap'],$this->state['exclusivetax_ap'],$this->state['customerid']]);
        });


        $this->dispatchBrowserEvent('alert',['message' => 'Update Successfully!']);
    }

    public function mount($customer_id)
    {
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
                -> join('buyer', 'customer.customerid', '=', 'buyer.customerid')
                -> join('vendor', 'customer.customerid', '=', 'vendor.customerid')
                -> where('customer.customerid','=',$customer_id)
                -> get();
        
        //Convert เป็น Arrat 1 มิติ
        $this->state = json_decode(json_encode($data[0]), true);
    }

    public function render()
    {
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
        $this->accountNos_dd = DB::table('account')
                -> select('account','accname')
                -> where('detail',TRUE)
                -> orderBy('acctype')
                -> orderBy('account')
                -> get();
        $this->accountNosAp_dd = DB::table('account')
                -> select('account','accname')
                -> where('detail',TRUE)
                -> orderBy('acctype')
                -> orderBy('account')
                -> get();
        
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

        $theCustomer = $this->state['customerid'] . " (" . $this->state['name'] . ")";

        return view('livewire.accstar.customer-form',[
            'theCustomer' => $this->state['customerid'] . " (" . $this->state['name'] . ")"
        ]);
    }
}
