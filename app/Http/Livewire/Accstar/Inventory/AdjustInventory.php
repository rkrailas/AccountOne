<?php

namespace App\Http\Livewire\Accstar\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\AdjustInventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class AdjustInventory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $sortDirection = "asc";
    public $sortBy = "inventory.itemid";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $inventorys_dd, $account_dd, $location_dd;
    public $adjInventory = []; //documentno,itemid,description,stocktype,category,location,adjustdate,adjquantity
                            //,adjvalue(per unit),adjtotalvalue(total value),unitofmeasure,instock,instockvalue,account,averagecost
    public $adjustType;

    public function exportExcel(){
        return Excel::download(new AdjustInventoryExport($this->searchTerm), 'AdjustInventory.xlsx');
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

    public function createAdjustInventory()
    {
        $validateData = Validator::make($this->adjInventory, [
            'documentno' => 'required|unique:inventoryadjlog,documentno',
        ])->validate();

        //***ปรับปรุง-เข้า
        if ($this->adjustType == "in"){ 
            //Check data last update?
            $data = DB::table('inventory')
                    ->select('itemid','instock','instockvalue')
                    ->where('itemid',$this->adjInventory['itemid'])
                    ->get();
            if (count($data) > 0)
            {
                $data = json_decode(json_encode($data[0]), true);
                
                //Check has changed in table inventory?
                if ($data['instock'] == $this->adjInventory['instock'] && $data['instockvalue'] == $this->adjInventory['instockvalue']){
                    DB::transaction(function () use ($data) {
                        //Calulate New instock, instockvalue, averagecost
                        $newInstock = $data['instock'] + $this->adjInventory['adjquantity'];
                        $newInstockvalue = $data['instockvalue'] + $this->adjInventory['adjtotalvalue'];
                        $newAveragecost = $newInstockvalue / $newInstock;

                        //Update Inventory
                        DB::statement("UPDATE inventory SET cost=?,instock=?,instockvalue=?,averagecost=?,employee_id=?,transactiondate=?
                        where id=?" 
                        ,[$newAveragecost,$newInstock,$newInstockvalue,$newAveragecost,'Admin', Carbon::now(), $this->adjInventory['id']]);

                        //Insert or Update InventoryLocation
                        // $data2 = DB::table('inventorylocation')
                        //     ->select('itemid', 'location', 'instock')
                        //     ->where('itemid', $this->adjInventory['itemid'],)
                        //     ->where('location', $this->adjInventory['location'],)
                        //     ->get();
                        
                        // if (count($data2) > 0){
                        //     $data2 = json_decode(json_encode($data2[0]), true);
                        //     $newInstockLocation = $data2['instock'] + $this->adjInventory['adjquantity'];
                        //     DB::statement("UPDATE inventorylocation SET instock=?
                        //     where itemid=? and location=?" 
                        //     ,[$newInstockLocation, $this->adjInventory['itemid'], $this->adjInventory['location']]);
                        // }else{
                        //     DB::statement("INSERT INTO inventorylocation(itemid,location,instock)
                        //     VALUES(?,?,?)"
                        //     ,[$this->adjInventory['itemid'],$this->adjInventory['location'],$newInstock]);
                        // }

                        //Insert inventoryadjlog
                        $isadjustin = true;
                        DB::statement("INSERT INTO inventoryadjlog(itemid,documentno,adjquantity,adjvalue,location,isadjustin,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?)"
                            ,[$this->adjInventory['itemid'],$this->adjInventory['documentno'],$this->adjInventory['adjquantity'],$this->adjInventory['adjvalue']
                            ,$this->adjInventory['location'],$isadjustin,'Admin',Carbon::now()]);

                        //Insert purchasedetaillog
                        DB::statement("INSERT INTO purchasedetaillog(ponumber,podate,itemid,description,quantity,quantityg,unitprice,amount
                                    ,taxref,receiveno,cost,location,unitofmeasure,stocktype,poreturn,goodsin,journal,posted,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                            ,$this->adjInventory['adjquantity'],$this->adjInventory['adjquantity'],$this->adjInventory['adjvalue'],$this->adjInventory['adjtotalvalue']
                            ,$this->adjInventory['documentno'],$this->adjInventory['documentno'],$this->adjInventory['adjvalue'],$this->adjInventory['location']
                            ,$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype'],'N',true,'AI',true,'Admin',Carbon::now()]);

                        $this->dispatchBrowserEvent('hide-adjustInventoryForm',['message' => 'Create Successfully!']);
                    });

                }else{
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => 'ทำรายการใหม่ เพราะข้อมูลมีการเปลี่ยนแปลงระหว่างทำการ',
                    ]);
                }
            }
        }//***ปรับปรุง-ออก
        else if($this->adjustType == "out"){ 
            //Check data last update?
            $data = DB::table('inventory')
                    ->select('itemid','instock','instockvalue')
                    ->where('itemid',$this->adjInventory['itemid'])
                    ->get();
            if (count($data) > 0)
            {
                $data = json_decode(json_encode($data[0]), true);

                //Check has changed in table inventory?
                if ($data['instock'] == $this->adjInventory['instock'] && $data['instockvalue'] == $this->adjInventory['instockvalue']){
                    DB::transaction(function () use ($data) {
                        //Calulate New instock, instockvalue, averagecost
                        $newInstock = $data['instock'] - $this->adjInventory['adjquantity'];
                        $newInstockvalue = $data['instockvalue'] - $this->adjInventory['adjtotalvalue'];

                        //Update Inventory
                        DB::statement("UPDATE inventory SET instock=?,instockvalue=?,employee_id=?,transactiondate=?
                        where id=?" 
                        ,[$newInstock, $newInstockvalue, 'Admin', Carbon::now(), $this->adjInventory['id']]);

                        //Update InventoryLocation
                        // $data2 = DB::table('inventorylocation')
                        //     ->select('itemid', 'location', 'instock')
                        //     ->where('itemid', $this->adjInventory['itemid'],)
                        //     ->where('location', $this->adjInventory['location'],)
                        //     ->get();
                        
                        // if (count($data2) > 0){
                        //     $data2 = json_decode(json_encode($data2[0]), true);
                        //     $newInstockLocation = $data2['instock'] - $this->adjInventory['adjquantity'];
                        //     DB::statement("UPDATE inventorylocation SET instock=?
                        //     where itemid=? and location=?" 
                        //     ,[$newInstockLocation, $this->adjInventory['itemid'], $this->adjInventory['location']]);
                        // }

                        //Insert inventoryadjlog
                        $isadjustin = false;
                        DB::statement("INSERT INTO inventoryadjlog(itemid,documentno,adjquantity,adjvalue,location,isadjustin,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?)"
                            ,[$this->adjInventory['itemid'],$this->adjInventory['documentno'],$this->adjInventory['adjquantity']
                            ,$this->adjInventory['averagecost'],$this->adjInventory['location'],$isadjustin,'Admin',Carbon::now()]);

                        //Insert salesdetaillog
                        DB::statement("INSERT INTO salesdetaillog(snumber,sdate,itemid,description,quantity,amount,cost,location
                                    ,unitofmeasure,stocktype,soreturn,goodsout,journal,posted,employee_id,transactiondate)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            ,[$this->adjInventory['documentno'],$this->adjInventory['adjustdate'],$this->adjInventory['itemid'],$this->adjInventory['description']
                            ,$this->adjInventory['adjquantity'],$this->adjInventory['adjtotalvalue'],$this->adjInventory['adjtotalvalue'],$this->adjInventory['location']
                            ,$this->adjInventory['unitofmeasure'],$this->adjInventory['stocktype'],'N',true,'AO',true,'Admin',Carbon::now()]);

                        $this->dispatchBrowserEvent('hide-adjustInventoryForm',['message' => 'Create Successfully!']);
                    });

                }else{
                    $this->dispatchBrowserEvent('popup-alert', ['title' => 'ทำรายการใหม่ เพราะข้อมูลมีการเปลี่ยนแปลงระหว่างทำการ']);
                }
            }
        }
    }

    public function updatedAdjInventoryAdjquantity() //Calulate adjtotalvalue
    {
        if ($this->adjustType == "in"){
            try {
                $this->adjInventory['adjtotalvalue'] = $this->adjInventory['adjquantity'] * $this->adjInventory['adjvalue'];
            } catch (\Throwable $th) {
                $this->adjInventory['adjtotalvalue'] = 0;
                return false;
            }  
        }else if ($this->adjustType == "out"){
            try {
                $this->adjInventory['adjtotalvalue'] = $this->adjInventory['adjquantity'] * $this->adjInventory['averagecost'];
            } catch (\Throwable $th) {
                $this->adjInventory['adjtotalvalue'] = 0;
                return false;
            }  
        }
    }

    public function updatedAdjInventoryAdjvalue() //Calulate adjtotalvalue
    {
        try {
            $this->adjInventory['adjtotalvalue'] = $this->adjInventory['adjquantity'] * $this->adjInventory['adjvalue'];
        } catch (\Throwable $th) {
            $this->adjInventory['adjtotalvalue'] = 0;
            return false;
        }  
    }

    public function updatedAdjInventoryItemid() //Start create new adjust
    {
        $data = DB::table('inventory')
        ->select('inventory.id','inventory.itemid','inventory.description','b.other as stocktypename','c.other as category','inventory.location'
                ,'inventory.unitofmeasure','inventory.instock','inventory.instockvalue','inventory.stocktype','inventory.averagecost')
        ->leftJoin('misctable as b', function ($join) {
            $join->on('inventory.stocktype', '=', 'b.code')
                    ->where('b.tabletype', 'I1');
                }) 
        ->leftJoin('misctable as c', function ($join) {
            $join->on('inventory.category', '=', 'c.code')
                    ->where('c.tabletype', 'CA');
                })
        ->where('inventory.itemid',$this->adjInventory['itemid'])
        ->get();

        if (count($data) > 0) //Check for clear value in itemid (select2)
        {
            $this->adjInventory = json_decode(json_encode($data[0]), true);

            $this->adjInventory['instock'] = number_format($this->adjInventory['instock'],2);
            $this->adjInventory['instockvalue'] = number_format($this->adjInventory['instockvalue'],2);
            $this->adjInventory['adjtotalvalue'] = 0;
        }
    }

    public function addNew()
    {
        $this->adjInventory = [
            'documentno'=>'','itemid'=>'','description'=>'','stocktype'=>'','category'=>'','location'=>''
            ,'adjustdate'=>'','adjquantity'=>0,'adjvalue'=>0,'unitofmeasure'=>'','adjtotalvalue'=>0
            ,'instock'=>0,'instockvalue'=>0,'account'=>''
        ];

        $this->dispatchBrowserEvent('show-adjustInventoryForm');
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function render()
    {
        //Dropdown
        $this->inventorys_dd = DB::table('inventory')
        ->select('itemid','description')
        ->orderBy('itemid')
        ->get();

        $this->account_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail', true)
        ->orderBy('account')
        ->get();

        $this->location_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'LO')
        ->orderBy('code')
        ->get();

        $adjlogs = DB::table('inventoryadjlog')
        ->select('inventoryadjlog.id','inventoryadjlog.documentno','inventoryadjlog.itemid','inventory.description as description'
                ,'inventoryadjlog.adjquantity','inventoryadjlog.adjvalue','misctable.other as location','inventoryadjlog.isadjustin'
                ,'employee.name as employee','inventoryadjlog.transactiondate')
        ->leftJoin('misctable', function ($join) {
            $join->on('inventoryadjlog.location', '=', 'misctable.code')
                 ->where('misctable.tabletype', 'LO');
                }) 
        ->leftJoin('inventory', 'inventoryadjlog.itemid', '=', 'inventory.itemid')
        ->leftJoin('employee', 'inventoryadjlog.employee_id', '=', 'employee.employeeid')
        ->Where(function($query) {
            $query->where('inventoryadjlog.documentno', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventoryadjlog.itemid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventory.description', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('misctable.other', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventoryadjlog.transactiondate', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('employee.name', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.accstar.inventory.adjust-inventory',[
            'adjlogs' => $adjlogs,
        ]);
    }
}
