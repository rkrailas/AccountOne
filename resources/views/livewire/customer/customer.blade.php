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
                    <h1 class="m-0 text-dark">ข้อมูลลูกค้า</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .List Customer -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <button wire:click.prevent="addNew" class="btn btn-sm btn-primary"><i class="fa fa-plus-circle mr-1"></i>
                                สร้างลูกค้า</button>
                            <button wire:click.prevent="refreshData" class="btn btn-sm btn-primary"><i class="fas fa-sync-alt"></i>
                                ดึงข้อมูลใหม่</button>
                            <button wire:click.prevent="exportExcel" class="btn btn-sm btn-success"><i class="fas fa-file-excel mr-1"></i>
                                Excel</button>
                        </div>
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
                                <th scope="col">
                                    รหัส
                                    <span wire:click="sortBy('customer.customerid')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customer.customerid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customer.customerid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ชื่อลูกค้า
                                    <span wire:click="sortBy('customer.name')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customer.name' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customer.name' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ผู้ติดต่อ
                                    <span wire:click="sortBy('customer.contact1')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customer.contact1' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customer.contact1' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    โทรศัพท์
                                    <span wire:click="sortBy('customer.phone1')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customer.phone1' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customer.phone1' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เลขที่ผู้เสียภาษี
                                    <span wire:click="sortBy('customer.taxid')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'customer.taxid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'customer.taxid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ลูกหนี้</th>
                                <th scope="col">เจ้าหนี้</th>
                                <th scope="col">นิติบุคคล</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($customers) > 0)
                            @foreach ($customers as $customer)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $customers->firstitem()-1  }}</td>
                                <td scope="col">{{ $customer->customerid }} </td>
                                <td scope="col" class="csstextoverflow">{{ $customer->name }} </td>
                                <td scope="col" class="csstextoverflow">{{ $customer->contact1 }} </td>
                                <td scope="col">{{ $customer->phone1 }} </td>
                                <td scope="col">{{ $customer->taxid }} </td>
                                <td scope="col" style="text-align:center"> @if($customer->debtor ) <i
                                        class="fas fa-check"></i> @endif </td>
                                <td scope="col" style="text-align:center"> @if($customer->creditor ) <i
                                        class="fas fa-check"></i> @endif </td>
                                <td scope="col" style="text-align:center"> @if($customer->corporate ) <i
                                        class="fas fa-check"></i> @endif </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $customer->customerid }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $customers->links() }} <span
                        class="ml-2">จำนวน {{ number_format($customers->Total(),0) }} รายการ</span>
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
    <!-- /.List Customer -->

    @include('livewire.customer._modelCustomer')
</div>

@push('styles')
<style>
    .csstextoverflow {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush