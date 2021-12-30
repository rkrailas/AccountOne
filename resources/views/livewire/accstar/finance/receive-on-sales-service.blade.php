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
                    <h1 class="m-0 text-dark">รับชำระเงิน-ตามใบแจ้งหนี้ค่าบริการ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">รับชำระเงิน-ตามใบแจ้งหนี้ค่าบริการ</li>
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
                            <input wire:model.lazy="searchTerm" type="text" class="form-control form-control-sm border-0"
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
                                <th scope="col">ใบสำคัญรับ
                                    <span wire:click="sortBy('gltran')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'gltran' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'gltran' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">วันที่
                                    <span wire:click="sortBy('gjournaldt')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'gjournaldt' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'gjournaldt' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ผู้ซื้อ
                                    <span wire:click="sortBy('customername')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customername' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customername' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ยอดเงิน
                                    <span wire:click="sortBy('amount')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'amount' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'amount' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">แก้ไขล่าสุด
                                    <span wire:click="sortBy('transaactiondate')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'transaactiondate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'transaactiondate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
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
                                <td scope="col" class="text-right pr-2">{{ number_format($recieptJournal->amount,2) }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($recieptJournal->transactiondate)->format('Y-m-d') }} </td>
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
                        <select class="form-control form-control-sm" style="width: 80px;" wire:model.lazy="numberOfPage">
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

    @include('livewire.accstar.finance._modalReceiveOnSalesService')
    @include('livewire.accstar._modalGenGL')
</div>
