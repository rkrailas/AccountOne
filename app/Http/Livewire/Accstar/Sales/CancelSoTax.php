<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelSoTax extends Component
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
                , to_char(sales.duedate,'YYYY-MM-DD') as duedate, sales.exclusivetax
                , sales.taxontotal, sales.posted, sales.salesaccount, sales.taxrate, sales.salestax, sales.discountamount, sales.sototal
                , customer.customerid, sales.shipcost, sales.closed, to_char(sales.duedate,'YYYY-MM-DD') as dueydate
                , to_char(taxdata.journaldate,'YYYY-MM-DD') as taxdate, taxdata.gltran
                from sales 
                left join customer on sales.customerid=customer.customerid
                join salesdetaillog on sales.snumber=salesdetaillog.snumber
                join taxdata on salesdetaillog.taxnumber = taxdata.taxnumber and taxdata.iscancelled=false
                where sales.ram_sodeliverytax=false
                and salesdetaillog.taxnumber='" . $this->deleteNumber . "'";
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
                ->where('taxnumber', $this->deleteNumber)
                ->get();
            $this->soDetails = json_decode(json_encode($data2), true);
    
            $this->reCalculateInGrid();

            $this->btnDelete = true;

        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบใบกำกับภาษี !',
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
                where reference=? and taxnumber=? " 
                , [true, 'Admin', Carbon::now(), $this->soHeader['snumber'], $this->deleteNumber]);

            //2. Update salesdetaillog.soreturn=G and taxnumber=''
            DB::statement("UPDATE salesdetaillog SET soreturn=?, taxnumber=?, employee_id=?, transactiondate=? 
                where snumber=? and taxnumber=? " 
                , ['G', '', 'Admin', Carbon::now(), $this->soHeader['snumber'], $this->deleteNumber]);

            //3. ตรวจสอบว่า post gl หรือยัง
            $strsql = "select * from gltran where gltran='" . $this->soHeader['gltran'] . "'";
            $data =  DB::select($strsql);
            if(count($data)){
                DB::statement("DELETE FROM gltran where gltran=? " 
                , [$this->soHeader['gltran']]);
            }else{
                $strsql = "select * from glmast where gltran='" . $this->soHeader['gltran'] . "'";
                $data =  DB::select($strsql);
                if(count($data)){
                    $data2 = json_decode(json_encode($data), true);
                    $genGL = [];
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
        return view('livewire.accstar.sales.cancel-so-tax');
    }
}
