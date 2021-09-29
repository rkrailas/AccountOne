<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- .ปุ่มซ่อนเมนู -->
                    <div class="float-left d-none d-sm-inline">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </div>
                    <!-- /.ปุ่มซ่อนเมนู -->
                    <h3 class="m-0 text-dark">ข้อมูลสินค้า</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ข้อมูลสินค้า</li>
                    </ol>
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
                        <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle" mr-1></i>
                            สร้างสินค้า</button>
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-0 pl-0">
                            <input wire:model.lazy="searchTerm" type="text" class="form-control border-0" placeholder="Search"> <!-- lazy=Lost Focus ถึงจะ Postback  -->
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
                                <th scope="col" class="text-center">รหัส
                                    <a href="" wire:click.prevent="sortBy('inventory.itemid')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col" class="text-center">ชื่อ
                                    <a href="" wire:click.prevent="sortBy('inventory.description')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col" class="text-center">ขนิด
                                    <a href="" wire:click.prevent="sortBy('inventory.stocktype')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col" class="text-center">ประเภท
                                    <a href="" wire:click.prevent="sortBy('inventory.category')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col" class="text-center">จำนวนคงเหลือ
                                    <a href="" wire:click.prevent="sortBy('inventory.instock')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($products) > 0)
                            @foreach ($products as $product)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $products->firstitem()-1  }}</td>
                                <td scope="col">{{ $product->itemid }} </td>
                                <td scope="col">{{ $product->description }} </td>
                                <td scope="col">{{ $product->stocktype }} </td>
                                <td scope="col">{{ $product->category }} </td>
                                <td scope="col" class="text-right">{{ number_format($product->instock,2) }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $product->id }}')" >
                                        <i class="fa fa-edit"></i>
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $products->links() }} <span class="ml-2">จำนวน {{ number_format($products->Total(),0) }} รายการ</span>
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
    <!-- /.List product -->
</div>