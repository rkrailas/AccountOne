<?php

namespace App\Http\Livewire\Sales;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Exports\SalesHistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Support\Collection;

class SalesHistory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "desc";
    public $sortBy = "a.transactiondate";
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
            //->where('soreturn', 'N')
            ->get();
        $this->soHeader = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ
        $this->soHeader['discountamount'] = round($this->soHeader['discountamount'],2);
        $this->soHeader['shipcost'] = round($this->soHeader['shipcost'],2);
        $this->soHeader['salestax'] = round($this->soHeader['salestax'],2);
        $this->soHeader['sototal'] = round($this->soHeader['sototal'],2);  
        
        // soDetails
        $data2 = DB::table('salesdetail')
            ->select('salesdetail.itemid' ,'salesdetail.description' ,'salesdetail.quantityord as quantity' ,'salesdetail.salesac' ,'salesdetail.unitprice'
                    , 'salesdetail.discountamount' ,'salesdetail.taxrate' ,'salesdetail.taxamount' ,'salesdetail.id' ,'salesdetail.inventoryac'
                    , 'salesdetail.serialno' ,'salesdetail.lotnumber' ,'inventory.stocktype')
            ->join('inventory', 'salesdetail.itemid', '=', 'inventory.itemid')
            ->where('snumber', $sNumber)
            ->where('soreturn', '<>', 'C')
            ->get();
        $this->soDetails = json_decode(json_encode($data2), true);
        
        if($this->soDetails[0]['stocktype'] == "4"){
            $this->soDetails[0]['description'] = $this->soDetails[0]['description'] . " (" . $this->soDetails[0]['serialno'] . ")";
        }elseif($this->soDetails[0]['stocktype'] == "9"){
            $this->soDetails[0]['description'] = $this->soDetails[0]['description'] . " (" . $this->soDetails[0]['lotnumber'] . ")";
        }

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

        $strsql = "SELECT c.sonumber, c.sodate, b.customerid || ': ' || b.name as customername, a.taxnumber
            , a.taxdate, a.amount, a.taxamount, c.ram_sodeliverytax, a.transactiondate
            FROM taxdata a
            LEFT JOIN customer b ON a.customerid=b.customerid
            LEFT JOIN sales c ON a.reference=c.sonumber
            WHERE a.purchase=false AND a.iscancelled=false AND c.sodate BETWEEN '" . $this->sDate . "' AND '" . $this->eDate . "'
                AND (c.snumber ILIKE '%" . $this->searchTerm . "%'
                    OR b.customerid ILIKE '%" . $this->searchTerm . "%'
                    OR b.name ILIKE '%" . $this->searchTerm . "%'
                    OR a.taxnumber ILIKE '%" . $this->searchTerm . "%'
                    OR CAST(a.amount AS TEXT) ILIKE '%" . $this->searchTerm . "%'
                    OR CAST(a.taxamount AS TEXT) ILIKE '%" . $this->searchTerm . "%')
            ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $salesOrders = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        //Get Summary
        $this->reset(['sumAmount2','sumTaxAmount']);
        $strsql = "SELECT sum(a.amount) as sumamount, sum(a.taxamount) as sumtaxamount 
            FROM taxdata a
            JOIN sales b ON a.reference=b.sonumber
            JOIN customer c ON a.customerid=c.customerid
            WHERE a.purchase=false AND a.iscancelled=false AND b.sodate BETWEEN '" . $this->sDate . "' AND '" . $this->eDate . "'
                AND (b.sonumber ILIKE '%" . $this->searchTerm . "%'
                    OR c.customerid ILIKE '%" . $this->searchTerm . "%'
                    OR c.name ILIKE '%" . $this->searchTerm . "%'
                    OR a.taxnumber ILIKE '%" . $this->searchTerm . "%'
                    OR b.sonote ILIKE '%" . $this->searchTerm . "%'
                    OR CAST(a.amount AS TEXT) ILIKE '%" . $this->searchTerm . "%'
                    OR CAST(a.taxamount AS TEXT) ILIKE '%" . $this->searchTerm . "%')";

        $xsalesOrders = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

            if ($xsalesOrders->count() > 0) {
                $this->sumAmount2 =$xsalesOrders[0]->sumamount;
                $this->sumTaxAmount = $xsalesOrders[0]->sumtaxamount;
            }
        
        return view('livewire.sales.sales-history',[
            'salesOrders' => $salesOrders,
        ]);
    }
}
