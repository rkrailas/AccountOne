<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <div class="float-left d-none d-sm-inline">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                                class="fas fa-bars"></i></a> <!-- .ปุ่มซ่อนเมนู -->
                    </div>
                    <h1 class="m-0 text-dark">ปรับปรุงสินค้า</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">

            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <button wire:click.prevent="addNew" class="btn btn-primary"><i
                                    class="fa fa-plus-circle mr-1"></i>
                                ปรับปรุงสินค้า</button>
                            <button wire:click.prevent="exportExcel" class="btn btn-success"><i
                                    class="fas fa-file-excel mr-1"></i>
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
                                    หมายเลข
                                    <span wire:click="sortBy('inventoryadjlog.documentno')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.documentno' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.documentno' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ประเภท
                                    <span wire:click="sortBy('inventoryadjlog.isadjustin')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.isadjustin' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.isadjustin' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    รหัสสินค้า
                                    <span wire:click="sortBy('inventoryadjlog.itemid')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.itemid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.itemid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    รายละเอียด
                                    <span wire:click="sortBy('inventory.description')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.description' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.description' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    จำนวน
                                    <span wire:click="sortBy('inventoryadjlog.adjquantity')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.adjquantity' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.adjquantity' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ทุน/หน่วย
                                    <span wire:click="sortBy('inventoryadjlog.adjvalue')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.adjvalue' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.adjvalue' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    สถานที่เก็บ
                                    <span wire:click="sortBy('inventoryadjlog.location')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.location' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.location' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    แก้ไขล่าสุด
                                    <span wire:click="sortBy('inventoryadjlog.transactiondate')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventoryadjlog.transactiondate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventoryadjlog.transactiondate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>                                
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($adjlogs) > 0)
                            @foreach ($adjlogs as $item)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $adjlogs->firstitem()-1  }}</td>
                                <td scope="col">{{ $item->documentno }}</td>
                                <td scope="col">
                                    @if($item->isadjustin === true)
                                        ปรับปรุง-เข้า
                                    @elseif($item->isadjustin === false)
                                        ปรับปรุง-ออก
                                    @else
                                        ไม่ระบุ
                                    @endif
                                </td>
                                <td scope="col">{{ $item->itemid }}</td>
                                <td scope="col">{{ $item->description }}</td>                                
                                <td scope="col" class="text-right">{{ number_format($item->adjquantity, 2) }}</td>
                                <td scope="col" class="text-right">{{ number_format($item->adjvalue, 2) }}</td>
                                <td scope="col">{{ $item->location }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($item->transactiondate)->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $adjlogs->links() }} <span
                        class="ml-2">จำนวน {{ number_format($adjlogs->Total(),0) }} รายการ</span>
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

    @include('livewire.accstar.inventory._modalAdjustInventory')
    @include('livewire.accstar._modalGenGL')
    @include('livewire.accstar.inventory._modalSerialNo')
    @include('livewire.accstar.inventory._modalSerialNoOut')
</div>