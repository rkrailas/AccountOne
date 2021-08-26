<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- .ปุ่มซ่อนเมนู -->
                    <div class="float-left d-none d-sm-inline">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                                class="fas fa-bars"></i></a>
                    </div>
                    <!-- /.ปุ่มซ่อนเมนู -->
                    <h1 class="m-0 text-dark">ใบสำคัญ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ใบสำคัญ</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- /.List Journal -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle"
                                mr-1></i>
                            สร้างใบสำคัญ</button>
                        <div clasee="btn-group">
                            <button wire:click="filterJournalByBook" type="button"
                                class="btn {{ is_null($book) ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ทั้งหมด</span>
                            </button>
                            <button wire:click="filterJournalByBook('GL')" type="button"
                                class="btn {{ $book=='GL' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ทั่วไป</span>
                            </button>
                            <button wire:click="filterJournalByBook('PO')" type="button"
                                class="btn {{ $book=='PO' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ซื้อ</span>
                            </button>
                            <button wire:click="filterJournalByBook('SO')" type="button"
                                class="btn {{ $book=='SO' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ขาย</span>
                            </button>
                            <button wire:click="filterJournalByBook('JP')" type="button"
                                class="btn {{ $book=='JP' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">จ่าย</span>
                            </button>
                            <button wire:click="filterJournalByBook('JR')" type="button"
                                class="btn {{ $book=='JR' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">รับ</span>
                            </button>
                        </div>
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-2">
                            <input wire:model.lazy="searchTerm" type="text" class="form-control border-0"
                                placeholder="Search"> <!-- lazy=Lost Focus ถึงจะ Postback  -->
                            <div wire:loading.delay wire:target="searchTerm">
                                <div class="la-ball-clip-rotate la-dark la-sm">
                                    <div></div>
                                </div>
                            </div>
                        </div>
                        <x-search-input />
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">เลขที่ใบสำคัญ</th>
                                <th scope="col">วันที่</th>
                                <th scope="col">คำอธิบาย</th>
                                <th scope="col">สมุดรายวัน</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($gltrans) > 0)
                            @foreach ($gltrans as $gltran)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $gltrans->firstitem()-1  }}</td>
                                <td scope="col">{{ $gltran->gltran }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($gltran->gjournaldt)->format('Y-m-d') }} </td>
                                <td style="width:50%" scope="col">{{ $gltran->gldescription }} </td>
                                <td scope="col">{{ $gltran->other }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $gltran->gltran }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    {{ $gltrans->links() }} จำนวน {{ number_format($gltrans->Total(),0) }} รายการ
                </div>
            </div>
        </div>
    </div>
    <!-- /.List Journal -->

    <!-- .Model Form Add/Edit -->
    <div class="modal fade bd-example-modal-xl" id="formJournal" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
        <div class="modal-dialog modal-xl">
            <form autocomplete="off" wire:submit.prevent="createUpdateJournal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            @if($showEditModal)
                            แก้ไขใบสำคัญ
                            @else
                            สร้างใบสำคัญ
                            @endif
                        </h5>
                        <div class="float-right">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">
                                <i class="fa fa-times mr-1"></i>Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save mr-1"></i>
                                @if($showEditModal)
                                <span>Save Changes</span>
                                @else
                                <span>Save </span>
                                @endif
                            </button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <label>เลขใบสำคัญ:</label>
                                <input type="text" class="form-control" required wire:model.defer="gltranNo2">
                            </div>
                            <div class="col">
                                <label>วันที่ใบสำคัญ:</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="gjournaldt2" id="gjournalDate" :error="'date'"
                                        required />
                                </div>
                            </div>
                            <div class="col">
                                <label>สมุดรายวัน:</label>
                                <select class="form-control" required wire:model.defer="gjournal2">
                                    <option value="">--- โปรดเลือก ---</option>
                                    @foreach ($journals as $journal)
                                    <option value="{{ $journal->code }}">{{ $journal->other }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label>ชนิดการจัดสรร:</label>
                                <select class="form-control" wire:model.defer="department2">
                                    <option value="">--- โปรดเลือก ---</option>
                                    @foreach ($allocations as $allocation)
                                    <option value="{{ $allocation->code }}">{{ $allocation->description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group row">
                                    <label class="col-sm-1 col-form-label">คำอธิบาย:</label>
                                    <div class="col-sm-11">
                                        <input type="text" class="form-control" wire:model.defer="gldescription2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- .Grid -->
                        <div class="row">
                            <div class="col">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                <button class="btn btn-sm btn-primary"
                                                    wire:click.prevent="addRow">+Add</button>
                                            </th>
                                            <th scope="col">บัญชี</th>
                                            <th scope="col">เดบิต</th>
                                            <th scope="col">เครดิต</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($journalDetails != null)
                                        @foreach($journalDetails as $index => $journalDetail)
                                        <tr>
                                            <td scope="row"> <center>{{ $loop->iteration }}</center></td>
                                            <td>
                                                <select class="form-control" required
                                                    wire:model.defer="journalDetails.{{$index}}.glaccount">
                                                    <option value="">--- โปรดเลือก ---</option>
                                                    @foreach($accountNos as $accountNo)
                                                    <option value="{{ $accountNo->account }}">{{ $accountNo->account }}:
                                                        {{ $accountNo->accname }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control"
                                                    style="text-align: right;"
                                                    wire:model.lazy="journalDetails.{{$index}}.gldebit">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control"
                                                    style="text-align: right;"
                                                    wire:model.lazy="journalDetails.{{$index}}.glcredit">
                                            </td>
                                            <td>
                                                <center>
                                                    <a href="" wire:click.prevent="removeRow({{ $index }})">
                                                        <i class="fa fa-trash text-danger"></i>
                                                    </a>
                                                </center>
                                                
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <input wire:model="sumGldebit" class="form-control" type="number"
                                                    style="text-align: right;" readonly>
                                            </td>
                                            <td>
                                                <input wire:model="sumGlcredit" class="form-control" type="number"
                                                    style="text-align: right;" readonly>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.Grid -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>
                            @if($showEditModal)
                            <span>Save Changes</span>
                            @else
                            <span>Save </span>
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /.Model Form Add/Edit -->

    @include('livewire.accstar._mycss')
</div>