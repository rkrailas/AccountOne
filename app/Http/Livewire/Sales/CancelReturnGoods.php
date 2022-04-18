<?php

namespace App\Http\Livewire\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelReturnGoods extends Component
{
    protected $listeners = ['deleteConfirmed' => 'delete'];
    
    public $deleteNumber;
    public $soHeader =[];
    public $soDetails =[];
    public $sumQuantity, $sumAmount = 0;
    public $btnDelete;

    public function searchDoc()
    {
        $strsql = "select sonumber,to_char(sodate,'YYYY-MM-DD') as sodate, deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate
                , invoiceno, to_char(invoicedate,'YYYY-MM-DD') as invoicedate, refno
                , CONCAT(customer.customerid,': ', customer.name) as shipname
                , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                , exclusivetax, taxontotal, posted, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost, closed
                from sales
                join customer on sales.customerid=customer.customerid
                where soreturn='Y' and closed=true and sonumber='" . $this->deleteNumber . "'";
        $data =  DB::select($strsql);

        if (count($data)) { //ถ้าพบ SO
            $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
            $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
            $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
            $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
            $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);

            //soDetails
            $data2 = DB::table('salesdetaillog')
            ->select('salesdetaillog.itemid','salesdetaillog.description','salesdetaillog.quantity','salesdetaillog.salesac','salesdetaillog.unitprice'
                    ,'salesdetaillog.discountamount','salesdetaillog.taxrate','salesdetaillog.taxamount','salesdetaillog.id','salesdetaillog.inventoryac'
                    ,'salesdetaillog.cost', 'salesdetaillog.serialno', 'salesdetaillog.lotnumber', 'inventory.stocktype')
            ->join('inventory', 'salesdetaillog.itemid', '=', 'inventory.itemid')
            ->where('salesdetaillog.snumber', $this->deleteNumber)
            ->where('salesdetaillog.soreturn', 'Y')
            ->get();
            $this->soDetails = json_decode(json_encode($data2), true); 

            $this->reCalculateInGrid();

            $this->btnDelete = true;

        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบเอกสารรับคืนสินค้า !',
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
            // 1. Update sales
            DB::statement("UPDATE sales SET soreturn=?, employee_id=?, transactiondate=? 
                where sonumber=?" 
                , ['C', 'Admin', Carbon::now(), $this->soHeader['sonumber']]);

            // 2. Update salesdetail
            DB::statement("UPDATE salesdetail SET soreturn=?, employee_id=?, transactiondate=? 
                where snumber=?" 
                , ['C', 'Admin', Carbon::now(), $this->soHeader['sonumber']]);
            
            // 3. Update salesdetaillog
            DB::statement("UPDATE salesdetaillog SET soreturn=?, employee_id=?, transactiondate=? 
                where snumber=?" 
                , ['C', 'Admin', Carbon::now(), $this->soHeader['sonumber']]);

            // 4. Update taxdata.iscancelled=true
            DB::statement("UPDATE taxdata SET iscancelled=?, employee_id=?, transactiondate=? 
                where purchase=false and taxnumber=? and customerid=?" 
                , [true, 'Admin', Carbon::now(), $this->soHeader['invoiceno'], $this->soHeader['customerid']]);

            // 5. Updaate Inventory inventoryserial & purchasedetaillog
            foreach ($this->soDetails as $soDetails2) {
                $xinventory = DB::table('inventory')
                ->select('instock','instockvalue','averagecost')
                ->where('itemid', $soDetails2['itemid'])
                ->get();

                if ($xinventory->count() > 0) {
                    $xinstock = $xinventory[0]->instock - $soDetails2['quantity']; //บวกจำนวนกลับ
                    $newAVG = round(($xinventory[0]->instockvalue - $soDetails2['cost']) / $xinstock, 2); //หา AVG Cost ใหม่
                    $xinstockvalue = $xinstock * $newAVG;

                    DB::statement("UPDATE inventory SET instock=?, instockvalue=?, cost=?, averagecost=?, employee_id=?, transactiondate=?
                        where itemid=?" 
                        , [$xinstock, $xinstockvalue, $newAVG, $newAVG, 'Admin', Carbon::now(), $soDetails2['itemid']]);

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

            // 6. ตรวจสอบว่า post gl หรือยัง
            $strsql = "select * from gltran where gltran='" . $this->soHeader['deliveryno'] . "'";
            $data =  DB::select($strsql);
            if(count($data)){
                DB::statement("DELETE FROM gltran where gltran=? " 
                , [$this->soHeader['deliveryno']]);
            }else{
                $strsql = "select * from glmast where gltran='" . $this->soHeader['deliveryno'] . "'";
                $data =  DB::select($strsql);
                if(count($data)){
                    $data2 = json_decode(json_encode($data), true);
                    for($i=0; $i<count($data2);$i++){
                        //สลับ gldebit กับ glcredit
                        DB::statement("INSERT INTO gltran(gjournal,gltran,gjournaldt,glaccount,gldebit,glcredit
                            ,gldescription,department,jobid,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?)"
                            , [$data2[$i]['gjournal'],  $data2[0]['gltran'] . "R", Carbon::now()->format('Y-m-d'), $data2[$i]['glaccount']
                            , $data2[$i]['glcredit'], $data2[$i]['gldebit'], "ยกเลิก-" . $data2[$i]['gldescription']
                            , $data2[$i]['department'], $data2[$i]['jobid'], 'Admin', Carbon::now()]);
                    }
                }
            }
            
            $this->resetPara();
            $this->dispatchBrowserEvent('display-Message',['message' => 'Cancel Successfully!']);
        });        
    }

    public function resetPara()
    {
        $this->reset(['deleteNumber', 'soHeader', 'soDetails', 'btnDelete', 'sumQuantity', 'sumAmount']);
        $this->soHeader['discountamount'] = 0;
        $this->soHeader['salestax'] = 0;
        $this->soHeader['sototal'] = 0;
        $this->soHeader['customerid'] = "";
    }

    public function reCalculateInGrid()
    {
        for($i=0; $i<count($this->soDetails);$i++)
        {
            //$this->soDetails[$index]['amount'] ยอดก่อน VAT และส่วนลด
            //$this->soDetails[$index]['netamount'] ยอดรวม VAT หักส่วนลด
            if ($this->soHeader['exclusivetax'] == TRUE) 
            {
                $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity'] 
                            * ($this->soDetails[$i]['cost'] - $this->soDetails[$i]['unitprice']),2);
                
            }else{
                $this->soDetails[$i]['amount'] = round($this->soDetails[$i]['quantity'] * 
                            (($this->soDetails[$i]['cost'] - $this->soDetails[$i]['unitprice'])
                            - ($this->soDetails[$i]['cost'] - $this->soDetails[$i]['unitprice']) * 7 / 107),2);
            }

            //ตรวจสอบ Taxrate เป็น Null หรือไม่
            if ($this->soDetails[$i]['taxrate'] == null) {
                if($this->soHeader['taxrate']){
                    $this->soDetails[$i]['taxrate'] = $this->soHeader['taxrate'];
                }else{
                    $this->soDetails[$i]['taxrate'] = 0;
                }
            }

            //ตรวจสอบ Amount เป็น Null หรือไม่
            if ($this->soDetails[$i]['amount'] == null){
                $this->soDetails[$i]['amount'] = 0;
            }

            //ตรวจสอบว่า soDetails.taxamount = -0 หรือไม่ 
            $this->soDetails[$i]['taxamount'] = round(($this->soDetails[$i]['amount'] * $this->soDetails[$i]['taxrate']) / 100, 2);
            if ($this->soDetails[$i]['taxamount'] == -0){
                $this->soDetails[$i]['taxamount'] =0;
            }

            $this->soDetails[$i]['netamount'] = round($this->soDetails[$i]['amount'] + $this->soDetails[$i]['taxamount'],2);
            $this->soDetails[$i]['quantity'] = round($this->soDetails[$i]['quantity'],2);
            $this->soDetails[$i]['unitprice'] = round($this->soDetails[$i]['unitprice'],2);
            $this->soDetails[$i]['taxrate'] = round($this->soDetails[$i]['taxrate'],2);
            $this->soDetails[$i]['cost'] = round($this->soDetails[$i]['cost'],2);

            //หลังจาก Re-Cal รายบรรทัดเสร็จ มันจะไปเข้า function reCalculateSummary ที่ render
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
        $this->resetPara();
    }

    public function render()
    {
        // Summary grid     
        if($this->soDetails != Null)
        {            
            $this->reCalculateSummary();
        }else{
        }

        return view('livewire.sales.cancel-return-goods');
    }
}
