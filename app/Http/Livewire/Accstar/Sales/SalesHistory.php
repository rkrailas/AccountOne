<?php

namespace App\Http\Livewire\Accstar\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\SalesHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class SalesHistory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "sales.sodate";
    public $numberOfPage = 10;
    public $searchTerm = null;
    public $sDate, $eDate;
    public $sumAmount, $sumQuantity; // Modal
    public $sumAmount2, $sumTaxAmount; // Table
    public $soHeader, $soDetails = [];
    public $showEditModal = null;

    public function clearValues()
    {
        $this->reset(['sumAmount2','sumTaxAmount','soHeader','soDetails']);
    }

    public function exportExcel(){
        return Excel::download(new SalesHistoryExport($this->searchTerm,$this->sDate,$this->eDate), 'SalesHistory.xlsx');
    }

    public function edit($sNumber) //??? ถึงตรงนี้
    {
        $this->showEditModal = TRUE;

        // soHeader
        $data = DB::table('sales')
            ->selectRaw("snumber,to_char(sodate,'YYYY-MM-DD') as sodate, deliveryno, to_char(deliverydate,'YYYY-MM-DD') as deliverydate
                    , to_char(expirydate,'YYYY-MM-DD') as expirydate, refno, payby
                    , CONCAT(customer.customerid,': ', customer.name) as shipname
                    , CONCAT(customer.address11,' ',customer.address12,' ',customer.city1,' ',customer.state1,' ',customer.zipcode1) as full_address
                    , to_char(duedate,'YYYY-MM-DD') as duedate, to_char(journaldate,'YYYY-MM-DD') as journaldate, exclusivetax
                    , taxontotal, posted, salesaccount, taxrate, salestax, discountamount, sototal, customer.customerid, shipcost, closed
                    , to_char(duedate,'YYYY-MM-DD') as dueydate")
            ->leftJoin('customer', 'sales.customerid', '=', 'customer.customerid')
            ->where('snumber', $sNumber)
            ->where('soreturn', 'N')
            ->get();
        $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
        $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
        $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
        $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);  
        
        // soDetails
        $data2 = DB::table('salesdetail')
            ->select('itemid','description','quantityord as quantity','salesac','unitprice','discountamount','taxrate','taxamount','id','inventoryac')
            ->where('snumber', $sNumber)
            ->where('soreturn', '<>', 'C')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true); 

        $this->reCalculateInGrid();
    
        $this->dispatchBrowserEvent('show-SalesOrderHistoryForm'); //แสดง Model Form
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

    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;
        if ($this->sortDirection == "asc"){
            $this->sortDirection = "desc";
        }else{
            $this->sortDirection = "asc";
        }
    }

    public function refreshData()
    {
        $this->resetPage();
        $this->clearValues();
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
        $this->clearValues();
    }

    public function mount()
    {
        $this->sDate = date_format(Carbon::now()->addMonth(-3),'Y-m-d');
        $this->eDate = date_format(Carbon::now(),'Y-m-d');
    }

    public function render()
    {
        // Summary grid     
        if($this->soDetails != Null)
        {            
            $this->reCalculateSummary();
        }else{
            $this->sumQuantity = 0;
            $this->sumAmount = 0;
            $this->soHeader['discountamount'] = 0;
            $this->soHeader['salestax'] = 0;
            $this->soHeader['sototal'] = 0;
            $this->soHeader['customerid'] = "";
        }

        $salesOrders = DB::table('taxdata')
        ->selectRaw("sales.sonumber, sales.sodate, customer.customerid || ': ' || customer.name as customername
                    , taxdata.taxnumber, taxdata.taxdate, sales.sonote, taxdata.amount, taxdata.taxamount")
        ->Join('sales','taxdata.reference','=','sales.sonumber')
        ->Join('customer','taxdata.customerid','=','customer.customerid')
        ->Where('taxdata.purchase',false)
        ->Where('taxdata.iscancelled',false)
        ->whereBetween('sales.sodate',[$this->sDate, $this->eDate])
        ->Where(function($query) {
            $query->where('sales.sonumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('sales.sodate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.customerid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxdate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('sales.sonote', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.amount', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        //Get Summary
        $this->reset(['sumAmount2','sumTaxAmount']);
        $xsalesOrders = DB::table('taxdata')
            ->selectRaw("sum(taxdata.amount) as sumamount, sum(taxdata.taxamount) as sumtaxamount")
            ->Join('sales','taxdata.reference','=','sales.sonumber')
            ->Join('customer','taxdata.customerid','=','customer.customerid')
            ->Where('taxdata.purchase',false)
            ->Where('taxdata.iscancelled',false)
            ->whereBetween('sales.sodate',[$this->sDate, $this->eDate])
            ->Where(function($query) {
                $query->where('sales.sonumber', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('sales.sodate', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('customer.customerid', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('customer.name', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('taxdata.taxnumber', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('taxdata.taxdate', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('sales.sonote', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('taxdata.amount', 'ilike', '%'.$this->searchTerm.'%')
                        ->orWhere('taxdata.taxamount', 'ilike', '%'.$this->searchTerm.'%');
                })
            ->get();

            if ($xsalesOrders->count() > 0) {
                $this->sumAmount2 =$xsalesOrders[0]->sumamount;
                $this->sumTaxAmount = $xsalesOrders[0]->sumtaxamount;
            }
        
        return view('livewire.accstar.sales.sales-history',[
            'salesOrders' => $salesOrders,
        ]);
    }
}
