<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CancelSalesOrder extends Component
{
    protected $listeners = ['deleteConfirmed' => 'delete'];
    
    public $deleteNumber;
    public $soHeader =[];
    public $soDetails =[];
    public $sumQuantity, $sumAmount = 0;
    public $btnDelete;

    public function searchDoc()
    {
        $strsql = "select snumber,to_char(sodate,'YYYY-MM-DD') as sodate, deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate
                , to_char(expirydate,'YYYY-MM-DD') as expirydate, refno, payby
                , CONCAT(customer.customerid,': ', customer.name) as shipname
                , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                , taxontotal, posted, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost, closed
                , to_char(duedate,'YYYY-MM-DD') as dueydate
                from sales 
                join customer on sales.customerid=customer.customerid 
                where soreturn='N' and closed=true and sales.snumber='" . $this->deleteNumber . "'";
        $data =  DB::select($strsql);

        if (count($data)) {
            $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
            $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
            $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
            $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
            $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);  

            $data2 = DB::table('salesdetail')
                ->select('itemid','description','quantity','salesac','unitprice','discountamount','taxrate','taxamount','id','inventoryac')
                ->where('snumber', $this->deleteNumber)
                ->get();
            $this->soDetails = json_decode(json_encode($data2), true);
    
            $this->reCalculateInGrid();

            //ตรวจสอบว่า่มีการส่งสินค้าหรือยัง
            $strsql = "select snumber from salesdetail where snumber='" . $this->deleteNumber . "' and quantityord <> quantitybac";
            $data2 =  DB::select($strsql);
            if (count($data2)){ //มีส่งสินค้าแล้ว
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'ไม่สามารถยกเลิกได้ เพราะมีการส่งสินค้าแล้ว !',
                ]);
            }else{
                $this->btnDelete = true;
            }

        }else{
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'ไม่พบใบสั่งขาย !',
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
            DB::table('sales')->where('snumber', $this->deleteNumber)->delete();
            DB::table('salesdetail')->where('snumber', $this->deleteNumber)->delete();

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

        return view('livewire.accstar.sales.cancel-sales-order');
    }
}
