<?php

namespace App\Http\Livewire\Accstar\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use App\Exports\InventorysExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Validator;

class Inventory extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    use WithFileUploads;

    public $sortDirection = "asc";
    public $sortBy = "inventory.itemid";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $showEditModal = null;
    public $product = []; //itemid,description,stocktype,category,brand,model,location,unitofmeasure,unitofmeasures,averagecost,salesprice
                        //,inventoryac,salesac,purchasertac,salesrtac,costtype,stdcost,reorderlevel,reorderqty,ram_inventory_image
    public $stocktype_dd, $category_dd, $brand_dd, $model_dd, $location_dd, $unitofmeasure_dd, $account_dd;
    public $photo;

    public function showImage($imageUlr){
        $this->dispatchBrowserEvent('popup-image', [
            'imageUrl' => $imageUlr ,
        ]);
    }

    public function exportExcel(){
        return Excel::download(new InventorysExport($this->searchTerm), 'Inventorys.xlsx');
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

    public function clearValues()
    {
        $this->photo = "";
        $this->product = [];
    }

    public function addNew()
    {
        $this->showEditModal = FALSE;
        $this->product = [
                'itemid'=>'','description'=>'','stocktype'=>'','category'=>'','brand'=>'','model'=>'','location'=>'','unitofmeasure'=>'','unitofmeasures'=>''
                ,'averagecost'=>0,'salesprice'=>0,'inventoryac'=>'','purchasertac'=>'','salesac'=>'','salesrtac'=>'','costtype'=>'','stdcost'=>0
                ,'reorderlevel'=>0,'reorderqty'=>0,'ram_inventory_image'=>''
        ];
        $this->dispatchBrowserEvent('show-inventoryForm');
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function createInventory()
    {
        $validateData = Validator::make($this->product, [
            'itemid' => 'required|unique:inventory,itemid',
        ])->validate();

        DB::transaction(function () {
            $inventory_images = "";
            if ($this->photo) 
            {
                $inventory_images = $this->photo->store('/', 'inventory_images');
            }

            DB::statement("INSERT INTO inventory(itemid,description,stocktype,category,brand,model,location,unitofmeasure,unitofmeasures
            ,averagecost,salesprice,inventoryac,salesac,purchasertac,salesrtac,costtype,stdcost,reorderlevel,reorderqty,ram_inventory_image
            ,employee_id,transactiondate)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            ,[$this->product['itemid'],$this->product['description'],$this->product['stocktype'],$this->product['category'],$this->product['brand']
            ,$this->product['model'],$this->product['location'],$this->product['unitofmeasure'],$this->product['unitofmeasures'],$this->product['averagecost']
            ,$this->product['salesprice'],$this->product['inventoryac'],$this->product['salesac'],$this->product['purchasertac'],$this->product['salesrtac']
            ,$this->product['costtype'],$this->product['stdcost'],$this->product['reorderlevel'],$this->product['reorderqty'],$inventory_images
            ,'Admin', Carbon::now()
            ]);
        });

        $this->dispatchBrowserEvent('hide-inventoryForm',['message' => 'Create Successfully!']);
    }

    public function edit($id)
    {
        $this->showEditModal = TRUE;
        $this->clearValues();

        $data = DB::table('inventory')
            ->select("inventory.*")
            ->where('id', $id)
            ->get();
        $this->product = json_decode(json_encode($data[0]), true);   //Convert เป็น Arrat 1 มิติ

        $this->product['averagecost'] = round($this->product['averagecost'],2);
        $this->product['salesprice'] = round($this->product['salesprice'],2);
        $this->product['stdcost'] = round($this->product['stdcost'],2);
        $this->product['reorderlevel'] = round($this->product['reorderlevel'],2);
        $this->product['reorderqty'] = round($this->product['reorderqty'],2);

        $this->dispatchBrowserEvent('show-inventoryForm');
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function updateInventory()
    {
        DB::transaction(function () {
            $inventory_images = "";
            if ($this->photo) 
            {
                $inventory_images = $this->photo->store('/', 'inventory_images');
            }

            DB::statement("UPDATE inventory SET itemid=?,description=?,stocktype=?,category=?,brand=?,model=?,location=?,unitofmeasure=?,unitofmeasures=?
                ,averagecost=?,salesprice=?,inventoryac=?,salesac=?,purchasertac=?,salesrtac=?,costtype=?,stdcost=?,reorderlevel=?,reorderqty=?
                ,employee_id=?, transactiondate=?, ram_inventory_image=?
                where id=?" 
                ,[$this->product['itemid'],$this->product['description'],$this->product['stocktype'],$this->product['category'],$this->product['brand']
                ,$this->product['model'],$this->product['location'],$this->product['unitofmeasure'],$this->product['unitofmeasures']
                ,$this->product['averagecost'],$this->product['salesprice'],$this->product['inventoryac'],$this->product['salesac'],$this->product['purchasertac']
                ,$this->product['salesrtac'],$this->product['costtype'],$this->product['stdcost'],$this->product['reorderlevel'],$this->product['reorderqty']
                ,'Admin', Carbon::now(), $inventory_images, $this->product['id']]);
        });

        $this->dispatchBrowserEvent('hide-inventoryForm', ['message' => 'Updated Successfully!']);
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        //Dropdown
        $this->stocktype_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'I1')
        ->orderBy('code')
        ->get();

        $this->category_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'CA')
        ->orderBy('code')
        ->get();

        $this->brand_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'BR')
        ->orderBy('code')
        ->get();

        $this->model_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'MO')
        ->orderBy('code')
        ->get();

        $this->location_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'LO')
        ->orderBy('code')
        ->get();

        $this->unitofmeasure_dd = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype', 'UN')
        ->orderBy('code')
        ->get();

        $this->account_dd = DB::table('account')
        ->select('account','accnameother')
        ->where('detail', true)
        ->orderBy('account')
        ->get();
        


        $inventorys = DB::table('inventory')
        ->select('inventory.id','inventory.itemid','inventory.description','b.other as stocktype'
                ,'c.other as category','inventory.instock','inventory.salesprice','inventory.ram_inventory_image')
        ->leftJoin('misctable as b', function ($join) {
            $join->on('inventory.stocktype', '=', 'b.code')
                 ->where('b.tabletype', 'I1');
                }) 
        ->leftJoin('misctable as c', function ($join) {
            $join->on('inventory.category', '=', 'c.code')
                    ->where('c.tabletype', 'CA');
                })
        ->Where(function($query) {
            $query->where('inventory.itemid', 'ilike', '%'.$this->searchTerm.'%')
                    ->orWhere('inventory.description', 'ilike', '%'.$this->searchTerm.'%');
            })
        ->orderBy($this->sortBy,$this->sortDirection)
        ->paginate($this->numberOfPage);

        return view('livewire.accstar.inventory.inventory',[
            'inventorys' => $inventorys,
        ]);
    }
}
