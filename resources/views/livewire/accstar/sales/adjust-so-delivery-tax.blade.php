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
                    <h1 class="m-0 text-dark">ปรับปรุงราคาขาย</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">ระบบขาย</li>
                        <li class="breadcrumb-item active">ปรับปรุงราคาขาย</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .List Sales Order -->
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
                    <table class="table table-md table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">เลขที่ปรับปรุง
                                    <span wire:click="sortBy('sales.sonumber')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'sales.sonumber' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'sales.sonumber' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">วันที่
                                    <span wire:click="sortBy('sales.sodate')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'sales.sodate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'sales.sodate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ผู้ซื้อ
                                    <span wire:click="sortBy('customer.name')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customer.name' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customer.name' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ยอดเงิน
                                    <span wire:click="sortBy('sales.sototal')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'sales.sototal' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'sales.sototal' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ใบกำกับอ้างอิง
                                    <span wire:click="sortBy('sales.refno')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'sales.refno' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'sales.refno' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">แก้ไขล่าสุด
                                    <span wire:click="sortBy('sales.transactiondate')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'sales.transactiondate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'sales.transactiondate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesOrders as $salesOrder)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $salesOrders->firstitem()-1  }}</td>
                                <td scope="col">{{ $salesOrder->sonumber }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($salesOrder->sodate)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $salesOrder->name }} </td>
                                <td scope="col">{{ number_format($salesOrder->sototal,2) }} </td>
                                <td scope="col">{{ $salesOrder->refno }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($salesOrder->transactiondate)->format('Y-m-d') }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $salesOrder->sonumber }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                    <a href="" wire:click.prevent="confirmDelete('{{ $salesOrder->sonumber }}')">
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $salesOrders->links() }} <span
                        class="ml-2">จำนวน {{ number_format($salesOrders->Total(),0) }} รายการ</span>
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
    <!-- /.List Sales Order -->
    @include('livewire.accstar.sales._modal-adjust-so-delivery-tax')
    @include('livewire.accstar._modalGenGL')
</div>
