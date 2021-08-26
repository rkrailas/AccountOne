<?php

namespace App\Http\Livewire\Accstar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ListGjournal extends Component
{
    use WithPagination; // .Require for Pagination
    protected $paginationTheme = 'bootstrap'; // .Require for Pagination

    public $searchTerm = null;
    public $showEditModal = null;
    public $journalDetails = null; //รายละเอียดของใบสำคัญนั้น ๆ
    public $allocations,$accountNos,$journals; //ตัวแปร Dropdown
    public $sumGldebit,$sumGlcredit,$gltranNo2,$gjournal2,$department2,$gjournaldt2,$gldescription2; //ตัวแปรใน Model Form
    public $book;

    public function filterJournalByBook($book = null) //จากปุ่มประเภทใบสำคัญ
    {
        $this->resetPage();
        $this->book = $book;
    }
 
    public function clearVarModelForm() //Clear ตัวแปรใน Model Form
    {        
        $this->sumGldebit = 0;
        $this->sumGlcredit = 0;
        $this->gltranNo2 = "";
        $this->gjournal2 = "";
        $this->department2 = "";
        $this->gjournaldt2 = "";
        $this->gldescription2 = "";
        $this->journalDetails = collect([]);
    }

    public function addRow() //กดปุ่มสร้าง Row ใน Grid
    {   
        //สร้าง Row ว่างๆ ใน Gird
        $this->journalDetails[] = collect([
            'gltran'=>'','glaccount'=>'','glaccname'=>'','gldescription'=>'','gldebit'=>0
            ,'glcredit'=>0,'gjournal'=>'','gjournaldt'=>null,'department'=>''
        ]);
    }

    public function removeRow($index) //กดปุ่มลบ Row ใน Grid
    {        
        $this->journalDetails = collect($this->journalDetails);      
        $this->journalDetails->forget($index); //คำสั่งลบ Row ใน Collection ถ้าเป็น Array unset($this->journalDetails[$index]);
    }

    public function addNew() //จากการกดปุ่ม สร้างใบสำคัญ
    {
        $this->showEditModal = FALSE;
        $this->addRow();
        $this->dispatchBrowserEvent('show-formJournal'); //แสดง Model Form
    }

    public function edit($gltranNo) //จากการกดปุ่ม Edit ที่ List รายการ
    {
        $this->showEditModal = TRUE;

        //ดึงรายการบันทึกบัญชีใบสำคัญนั้น ๆ ($journalDetails = Collection)
        $this->journalDetails = DB::table('gltran')
            ->select('gltran','glaccount','glaccname','gldescription','gldebit','glcredit','gjournal','gjournaldt','department')
            ->where('gltran','=',$gltranNo)
            ->get();

        // .เชื่อมกับ Dropdown ใน Form Model
        $this->gltranNo2 = $gltranNo;
        $this->gjournal2 = $this->journalDetails->first()->gjournal;
        $this->department2 = $this->journalDetails->first()->department;
        $this->gjournaldt2 = \Carbon\Carbon::parse($this->journalDetails->first()->gjournaldt)->format('d-m-Y');
        $this->gldescription2 = $this->journalDetails->first()->gldescription;
        // /.เชื่อมกับ Dropdown ใน Form Model

        $this->dispatchBrowserEvent('show-formJournal'); //แสดง Model Form
    }

    public function createUpdateJournal()
    {        
        $this->journalDetails = collect($this->journalDetails);
        
        //เปลี่ยนค่าใน Collection
        $this->journalDetails->transform(function ($item, $key) {
            $item['gltran'] = $this->gltranNo2;
            $item['gjournal'] = $this->gjournal2;
            $item['gjournaldt'] = $this->gjournaldt2;
            $item['department'] = $this->department2;
            $item['gldescription'] = $this->gldescription2;
            return $item;
        });
        
        //Delete แล้ว Insert ใหม่
        DB::transaction(function () {
            DB::table('gltran')->where('gltran', '=', $this->gltranNo2)->delete();
            DB::table('gltran')->insert($this->journalDetails->all()); //จะ Insert ได้ $journalDetails ต้องเป็น Array
        });
        
        $this->clearVarModelForm();
        $this->dispatchBrowserEvent('hide-formJournal', ['message' => 'Updated Successfully!']);
    }

    public function cancelJournal() //กดปุ่ม ยกเลิก จาก Model Form
    {
        $this->clearVarModelForm();
    }

    public function updatingSearchTerm() //จากการ Key ที่ input wire:model.lazy="searchTerm"
    {
        $this->resetPage();
    }

    public function mount()
    {
        // if($this->journalDetails == null)
        // {            
        //     $this->journalDetails = collect([]); //เปลี่ยนให้เป็น Collection
        // }
    }

    public function render()
    {
        // .Bind Data to Dropdown
        $this->accountNos = DB::table('account')
        ->select('account','accname')
        ->where('detail','=',TRUE)
        ->orderby('account')
        ->get();

        $this->journals = DB::table('misctable')
        ->select('code','other')
        ->where('tabletype','=','JR')
        ->get();

        $this->allocations = DB::table('allocationtable')
        ->select('code','description')
        ->get();
        // /.Bind Data to Dropdown
        
        if($this->journalDetails != Null ) //เดิม if($this->journalDetails->isNotEmpty())
        {
            $this->sumGldebit = number_format($this->journalDetails->sum('gldebit'),2); 
            $this->sumGlcredit = number_format($this->journalDetails->sum('glcredit'),2);
        }
                
        // .นับจำนวนแยกตาม Book (ยังไม่ได้ใช้งาน เพราะนับไม่ตรงกับ List)
        $allCount = DB::select("select count(*) as mycount from (select gltran from gltran where gltran<>'' 
                            group by gltran) as xxx")[0]->mycount;
        $glCount = DB::select("select count(*) as mycount from (select gltran from gltran where gltran<>'' 
                            and gjournal='GL' group by gltran) as xxx")[0]->mycount;
        $poCount = DB::select("select count(*) as mycount from (select gltran from gltran where gltran<>'' 
                            and gjournal='PO' group by gltran) as xxx")[0]->mycount;
        $soCount = DB::select("select count(*) as mycount from (select gltran from gltran where gltran<>'' 
                            and gjournal='SO' group by gltran) as xxx")[0]->mycount;
        $jpCount = DB::select("select count(*) as mycount from (select gltran from gltran where gltran<>'' 
                            and gjournal='JP' group by gltran) as xxx")[0]->mycount;
        $jrCount = DB::select("select count(*) as mycount from (select gltran from gltran where gltran<>'' 
                            and gjournal='JR' group by gltran) as xxx")[0]->mycount;
        // /.นับจำนวนแยกตาม Book (ยังไม่ได้ใช้งาน เพราะนับไม่ตรงกับ List)

        //แสดงรายการใบสำคัญ ค้นหาจาก gltran หรือ gldescription แต่ดึงมาเฉพาะ gltran ไม่ว่าง
        if($this->book){
            $gltrans = DB::table('gltran')
            ->select('gltran.gltran','gltran.gjournaldt','gltran.gldescription','misctable.other')
            ->join('misctable', function ($join) 
                {
                    $join->on('gltran.gjournal', '=', 'misctable.code')
                    ->where('misctable.tabletype', '=', 'JR');
                })
            ->join('employee','gltran.employee_id','=','employee.employeeid')
            ->where('gltran.gltran', '<>', '')
            ->Where(function($query) 
                {
                    $query->where('gltran.gltran', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('gltran.gldescription', 'like', '%'.$this->searchTerm.'%');
                })
            ->where('gltran.gjournal','=',$this->book)
            ->groupBy('gltran.gltran','gltran.gjournaldt','gltran.gldescription','misctable.other')
            ->paginate(10);
        }else{
            $gltrans = DB::table('gltran')
            ->select('gltran.gltran','gltran.gjournaldt','gltran.gldescription','misctable.other')
            ->join('misctable', function ($join) 
                {
                    $join->on('gltran.gjournal', '=', 'misctable.code')
                     ->where('misctable.tabletype', '=', 'JR');
                })
            ->join('employee','gltran.employee_id','=','employee.employeeid')
            ->where('gltran.gltran', '<>', '')
            ->Where(function($query) 
                {
                    $query->where('gltran.gltran', 'like', '%'.$this->searchTerm.'%')
                          ->orWhere('gltran.gldescription', 'like', '%'.$this->searchTerm.'%');
                })
            ->groupBy('gltran.gltran','gltran.gjournaldt','gltran.gldescription','misctable.other')
            ->paginate(10);
        }
        
        return view('livewire.accstar.list-gjournal',[
            'gltrans' => $gltrans,
            'allCount' => $allCount,
            'glCount' => $glCount,
            'poCount' => $poCount,
            'soCount' => $soCount,
            'jpCount' => $jpCount,
            'jrCount' => $jrCount,
        ]);
    }
}