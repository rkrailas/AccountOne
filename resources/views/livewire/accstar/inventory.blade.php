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
                    <h1 class="m-0 text-dark">ข้อมูลสินค้า</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .List Products -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <div>
                            <button wire:click.prevent="addNew" class="btn btn-primary"><i
                                    class="fa fa-plus-circle mr-1"></i>
                                สร้างสินค้า</button>
                            <button wire:click.prevent="exportExcel" class="btn btn-success"><i
                                    class="fas fa-file-excel mr-1"></i>
                                Excel</button>
                        </div>
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
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    รหัสสินค้า
                                    <span wire:click="sortBy('inventory.itemid')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.itemid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.itemid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ชื่อสินค้า
                                    <span wire:click="sortBy('inventory.description')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.description' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.description' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ชนิดของสินค้า
                                    <span wire:click="sortBy('inventory.stocktype')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.stocktype' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.stocktype' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ประเภทสินค้า
                                    <span wire:click="sortBy('inventory.category')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.category' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.category' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    คงเหลือ
                                    <span wire:click="sortBy('inventory.instock')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.instock' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.instock' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ราคาขาย
                                    <span wire:click="sortBy('inventory.salesprice')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-arrow-up {{ $sortBy === 'inventory.salesprice' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-arrow-down {{ $sortBy === 'inventory.salesprice' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($inventorys) > 0)
                            @foreach ($inventorys as $inventory)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $inventorys->firstitem()-1  }}</td>
                                <td scope="col">{{ $inventory->itemid }} </td>
                                <td scope="col">{{ $inventory->description }} </td>
                                <td scope="col">{{ $inventory->stocktype }} </td>
                                <td scope="col">{{ $inventory->category }} </td>
                                <td scope="col" class="text-right">{{ number_format($inventory->instock, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($inventory->salesprice, 2) }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $inventory->id }}')">
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $inventorys->links() }} <span
                        class="ml-2">จำนวน {{ number_format($inventorys->Total(),0) }} รายการ</span>
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
</div>