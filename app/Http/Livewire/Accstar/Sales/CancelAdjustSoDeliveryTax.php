<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelAdjustSoDeliveryTax extends Component
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
                left join customer on sales.customerid=customer.customerid
                where soreturn='D' and sales.sonumber='" . $this->deleteNumber . "'";
        $data =  DB::select($strsql);

        if (count($data)) { //ถ้าพบ SO
            $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
            $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
            $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
            $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
            $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);

            //soDetails
            $data2 = DB::table('salesdetail')
            ->select('itemid','description','quantity','salesac','unitprice','discountamount','taxrate','taxamount','id','inventoryac','cost')
            ->where('snumber', $this->deleteNumber)
            ->where('soreturn', 'D')
            ->get();
            $this->soDetails = json_decode(json_encode($data2), true); 

            $this->reCalculateInGrid();

            $this->btnDelete = true;

        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบเอกสารปรับปรุง !',
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
            // 1. Update taxdata.iscancelled=true
            DB::statement("UPDATE taxdata SET iscancelled=?, employee_id=?, transactiondate=? 
                where purchase=false and taxnumber=? and customerid=?" 
                , [true, 'Admin', Carbon::now(), $this->soHeader['invoiceno'], $this->soHeader['customerid']]);

            //2. Update salesdetail
            DB::statement("UPDATE salesdetail SET soreturn=?, employee_id=?, transactiondate=? 
                where snumber=?" 
                , ['C', 'Admin', Carbon::now(), $this->soHeader['sonumber']]);
            
            //3. Update sales
            DB::statement("UPDATE sales SET soreturn=?, employee_id=?, transactiondate=? 
                where sonumber=?" 
                , ['C', 'Admin', Carbon::now(), $this->soHeader['sonumber']]);

            //4. ตรวจสอบว่า post gl หรือยัง
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

        return view('livewire.accstar.sales.cancel-adjust-so-delivery-tax');
    }
}
