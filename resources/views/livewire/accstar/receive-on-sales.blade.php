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
                    <h1 class="m-0 text-dark">รับชำระเงิน</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">รับชำระเงิน</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .แสดงรายการใบสำคัญรับ -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle"
                                mr-1></i>
                            สร้างข้อมูลใหม่</button>                        
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-0 pl-0">
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
                                <th scope="col">เลขที่ใบสำคัญรับ
                                    <a href="" wire:click.prevent="sortJR('gltran')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">วันที่ใบสำคัญรับ
                                    <a href="" wire:click.prevent="sortJR('gjournaldt')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ผู้ซื้อ
                                    <a href="" wire:click.prevent="sortJR('customername')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                    
                                <th scope="col">ยอดเงิน
                                    <a href="" wire:click.prevent="sortJR('amount')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>                                    
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recieptJournals as $recieptJournal)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $recieptJournals->firstitem()-1  }}</td>
                                <td scope="col">{{ $recieptJournal->gltran }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($recieptJournal->gjournaldt)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $recieptJournal->customername }} </td>
                                <td scope="col">{{ number_format($recieptJournal->amount,2) }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $recieptJournal->gltran }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                    <a href="" wire:click.prevent="confirmDelete('{{ $recieptJournal->gltran }}')">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $recieptJournals->links() }} <span
                        class="ml-2">จำนวน {{ number_format($recieptJournals->Total(),0) }} รายการ</span>
                    <div class="col">
                        <select class="form-control" style="width: 80px;" wire:model.lazy="numberOfPage">
                            <option value="10" selected>10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.แสดงรายการใบสำคัญรับ -->

    @include('livewire.accstar._modalReceiveOnSales')
    @include('livewire.accstar._modalGenGL')
    @include('livewire.accstar._mypopup')
</div>
