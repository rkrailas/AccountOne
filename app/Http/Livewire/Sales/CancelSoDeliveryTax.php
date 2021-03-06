<?php

namespace App\Http\Livewire\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelSoDeliveryTax extends Component
{
    protected $listeners = ['deleteConfirmed' => 'delete'];
    
    public $deleteNumber;
    public $soHeader =[];
    public $soDetails =[];
    public $sumQuantity, $sumAmount = 0;
    public $btnDelete;

    public function searchDoc()
    {
        $strsql = "select to_char(taxdata.journaldate,'YYYY-MM-DD') as taxdate, taxdata.gltran, to_char(taxdata.journaldate,'YYYY-MM-DD') as journaldate
                , sales.snumber, to_char(sales.sodate,'YYYY-MM-DD') as sodate, CONCAT(customer.customerid,': ', customer.name) as shipname
                , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                , to_char(sales.duedate,'YYYY-MM-DD') as duedate, sales.exclusivetax, sales.taxontotal, sales.taxrate, sales.salestax
                , sales.discountamount, sales.sototal
                from sales 
                join customer on sales.customerid=customer.customerid
                join taxdata on sales.snumber = taxdata.reference and taxdata.iscancelled=false
                where sales.soreturn='N' and sales.posted=true and ram_sodeliverytax=true
                and taxdata.taxnumber='" . $this->deleteNumber . "'";
        $data =  DB::select($strsql);

        if (count($data)) { //ถ้าพบ SO
            $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
            $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
            //$this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
            $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
            $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);

            $strsql = "select sl.itemid,sl.quantity,sl.salesac,sl.unitprice,sl.discountamount,sl.taxrate,sl.taxamount
                    ,sl.id,sl.inventoryac,sl.soreturn,sl.cost,sl.serialno, sl.lotnumber, inv.stocktype, sl.ram_salesdetail_id
                    ,CASE 
                        WHEN inv.stocktype = '4' THEN sl.description || ' (' || sl.serialno || ')'
                        WHEN inv.stocktype = '9' THEN sl.description || ' (' || sl.lotnumber || ')'
                        ELSE sl.description
                    END as description
                    from salesdetaillog sl
                    join inventory inv on sl.itemid=inv.itemid
                    where sl.soreturn='N' and sl.snumber='" . $this->soHeader['snumber'] . "'";
            $this->soDetails = json_decode(json_encode(DB::select($strsql)), true);
            $this->reCalculateInGrid();
            $this->btnDelete = true;
        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบใบกำกับภาษี !',
            ]);

            $this->resetPara();
        }
    }

    public function pressCancel() //กดปุ่ม Cancel
    {
        $this->resetPara();
    }

    public function confirmDelete() //กดปุ่มยกเลิกเอกสาร
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
                , ['true', 'Admin', Carbon::now(), $this->soHeader['snumber'], $this->deleteNumber]);

            //2. Update salesdetaillog
            DB::statement("UPDATE salesdetaillog SET soreturn=?, taxnumber=?, employee_id=?, transactiondate=? 
                where snumber=? and taxnumber=? " 
                , ['C', '', 'Admin', Carbon::now(), $this->soHeader['snumber'], $this->deleteNumber]);

            //3. Update salesdetail
            DB::statement("UPDATE salesdetail SET quantity=quantitydel, quantitybac=quantitydel, quantitydel=quantitybac, employee_id=?, transactiondate=? 
                where snumber=?" 
                , ['Admin', Carbon::now(), $this->soHeader['snumber']]);
            
            //4. Update sales
            DB::statement("UPDATE sales SET posted=false, employee_id=?, transactiondate=? 
                where snumber=?" 
                , ['Admin', Carbon::now(), $this->soHeader['snumber']]);

            //5. Update Inventory & Inventoryserial & Purchasedetaillog
            foreach ($this->soDetails as $soDetails2){
                $strsql = "select instock, instockvalue from inventory where itemid='" . $soDetails2['itemid'] . "'";
                $data =  DB::select($strsql);
                if (count($data)){
                    $newInstock = $data[0]->instock + $soDetails2['quantity'];
                    $newInstockValue = $data[0]->instockvalue + $soDetails2['cost'];
    
                    DB::statement("UPDATE inventory SET instock=?,instockvalue=?,employee_id=?,transactiondate=? where itemid=?" 
                    ,[
                        $newInstock,$newInstockValue,'Admin',Carbon::now(),$soDetails2['itemid']
                    ]);

                    if($soDetails2['stocktype'] == "4") {
                        DB::statement("UPDATE inventoryserial SET sold=?,snumber=?,solddate=?,employee_id=?,transactiondate=? where itemid=? and serialno=?" 
                        ,['false',null,null,'Admin',Carbon::now(),$soDetails2['itemid'],$soDetails2['serialno']]);
                    }elseif($soDetails2['stocktype'] == "9") {
                        $xcount = 0;
                        while ($xcount < $soDetails2['quantity']) {
                            $strsql = "select id,sold,quantity
                                    from purchasedetaillog 
                                    where itemid='" . $soDetails2['itemid'] . "'
                                    and lotnumber='" . $soDetails2['lotnumber'] . "'
                                    and sold > 0
                                    order by id desc";
                            $data1 = DB::select($strsql);
                            if ($soDetails2['quantity'] - $xcount <= $data1[0]->sold) {   
                                DB::statement("UPDATE purchasedetaillog SET sold=sold-?,employee_id=?,transactiondate=?
                                            where id =" . $data1[0]->id
                                ,[$soDetails2['quantity'] - $xcount, 'Admin', Carbon::now()]);                                
                                $xcount = $xcount + $data1[0]->sold;
                            }else{
                                DB::statement("UPDATE purchasedetaillog SET sold=sold-?,employee_id=?,transactiondate=?
                                where id =" . $data1[0]->id
                                ,[$data1[0]->sold, 'Admin', Carbon::now()]);
                                $xcount = $xcount + $data1[0]->sold;
                            }
                        }
                    }
                }
            }

            //6. ตรวจสอบว่า post gl หรือยัง
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

        return view('livewire.sales.cancel-so-delivery-tax');
    }
}
