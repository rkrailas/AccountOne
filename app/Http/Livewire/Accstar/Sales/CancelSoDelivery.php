<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelSoDelivery extends Component
{
    protected $listeners = ['deleteConfirmed' => 'delete'];
    
    public $deleteNumber;
    public $soHeader =[];
    public $soDetails =[];
    public $sumQuantity, $sumAmount = 0;
    public $btnDelete;

    public function searchDoc()
    {
        $strsql = "select sales.snumber,to_char(sales.sodate,'YYYY-MM-DD') as sodate, sales.deliveryno, to_char(sales.deliverydate,'YYYY-MM-DD') as deliverydate
                , to_char(sales.expirydate,'YYYY-MM-DD') as expirydate, sales.refno, sales.payby
                , CONCAT(customer.customerid,': ', customer.name) as shipname
                , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                , to_char(sales.duedate,'YYYY-MM-DD') as duedate, to_char(sales.journaldate,'YYYY-MM-DD') as journaldate, sales.exclusivetax
                , sales.taxontotal, sales.posted, sales.salesaccount, sales.taxrate, sales.salestax, sales.discountamount, sales.sototal
                , customer.customerid, sales.shipcost, sales.closed
                , to_char(sales.duedate,'YYYY-MM-DD') as dueydate
                from sales 
                left join customer on sales.customerid=customer.customerid
                join salesdetaillog on sales.snumber=salesdetaillog.snumber
                where salesdetaillog.soreturn='G' and salesdetaillog.deliveryno='" . $this->deleteNumber . "'";
        $data =  DB::select($strsql);

        if (count($data)) { //ถ้าพบ SO
            $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
            $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
            $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
            $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
            $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);

            $data2 = DB::table('salesdetaillog')
                ->select('itemid','description','quantity','salesac','unitprice','discountamount','taxrate','taxamount'
                        ,'id','inventoryac','soreturn','ram_salesdetail_id','cost') //cost > per unit
                ->where('deliveryno', $this->deleteNumber)
                ->get();
            $this->soDetails = json_decode(json_encode($data2), true);
    
            $this->reCalculateInGrid();

            //ตรวจสอบว่ามีการรับใบกำกับหรือยัง
            $strsql = "select deliveryno from salesdetaillog where deliveryno='" . $this->deleteNumber . "' and soreturn='N'";
            $data2 =  DB::select($strsql);
            if (count($data2)){ //มีการรับใบกำกับแล้ว
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'ไม่สามารถยกเลิกได้ เพราะมีการรับใบกำกับภาษีแล้ว !',
                ]);
            }else{
                $this->btnDelete = true;
            }

        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบใบส่งสินค้า !',
            ]);

            $this->resetPara();
        }
    }

    public function pressCancel()
    {
        $this->resetPara();
    }

    public function confirmDelete() 
    {
        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function delete() 
    {   
        DB::transaction(function() 
        {
            // 1. Update salesdetaillog.soreturn=C
            DB::statement("UPDATE salesdetaillog SET soreturn=?, employee_id=?, transactiondate=? 
                where snumber=? and deliveryno=? and soreturn='G' " 
                , ['C', 'Admin', Carbon::now(), $this->soHeader['snumber'], $this->deleteNumber]);

            
            foreach ($this->soDetails as $soDetails2){
                // 2. Update salesdetail (soreturn=N, quantitydel,quantitybac>คืนจำนวน)
                $strsql = "select quantitydel, quantitybac from salesdetail where id=" . $soDetails2['ram_salesdetail_id'];
                $data =  DB::select($strsql);
                if (count($data)){
                    $newQuantityDel = $data[0]->quantitydel - $soDetails2['quantity'];
                    $newQuantityBac = $data[0]->quantitybac + $soDetails2['quantity'];

                    DB::statement("UPDATE salesdetail SET quantitydel=?,quantitybac=?,quantity=?,soreturn=?,employee_id=?,transactiondate=? where id=?" 
                    ,[
                        $newQuantityDel,$newQuantityBac,$newQuantityBac,'N','Admin',Carbon::now(),$soDetails2['ram_salesdetail_id']
                    ]);
                }

                // 3. Update Inventory (instock, instockvalue)
                $strsql = "select instock, instockvalue from inventory where itemid='" . $soDetails2['itemid'] . "'";
                $data =  DB::select($strsql);
                if (count($data)){
                    $newInstock = $data[0]->instock + $soDetails2['quantity'];
                    $newInstockValue = $data[0]->instockvalue + $soDetails2['cost'];

                    DB::statement("UPDATE inventory SET instock=?,instockvalue=?,employee_id=?,transactiondate=? where itemid=?" 
                    ,[
                        $newInstock,$newInstockValue,'Admin',Carbon::now(),$soDetails2['itemid']
                    ]);
                }
            }

            $this->resetPara();
            $this->dispatchBrowserEvent('display-Message',['message' => 'Cancel Successfully!']);
        });
    }

    public function resetPara()
    {
        $this->deleteNumber = "";
        $this->soHeader =[];
        $this->soDetails =[];
        $this->btnDelete = false;

        $this->sumQuantity = 0;
        $this->sumAmount = 0;
        $this->soHeader['discountamount'] = 0;
        $this->soHeader['salestax'] = 0;
        $this->soHeader['sototal'] = 0;
        $this->soHeader['customerid'] = "";
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

    public function reCalculateSummary()
    {
        // Summary Gird
        $this->sumQuantity = array_sum(array_column($this->soDetails,'quantity'));
        $this->sumAmount = array_sum(array_column($this->soDetails,'amount'));
        $this->soHeader['discountamount'] = array_sum(array_column($this->soDetails,'discountamount'));
        $this->soHeader['sototal'] = array_sum(array_column($this->soDetails,'netamount'));
        $this->soHeader['salestax'] = round(array_sum(array_column($this->soDetails,'taxamount')),2);
    }

    public function mount()
    {
        $this->sumQuantity = 0;
        $this->sumAmount = 0;
        $this->soHeader['discountamount'] = 0;
        $this->soHeader['salestax'] = 0;
        $this->soHeader['sototal'] = 0;
        $this->soHeader['customerid'] = "";
        $this->btnDelete = false;
    }

    public function render()
    {
        // Summary grid     
        if($this->soDetails != Null)
        {            
            $this->reCalculateSummary();
        }else{
        }

        return view('livewire.accstar.sales.cancel-so-delivery');
    }
}
