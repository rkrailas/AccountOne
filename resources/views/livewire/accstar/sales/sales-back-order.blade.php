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
                    <h1 class="m-0 text-dark">รายการค้างส่ง</h1>
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
                            <div class="form-inline">
                                <label class=" mr-1">วันที่:</label>
                                <div class="input-group mr-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="sDate" id="่sDate" :error="'date'" required />
                                </div>                            
                                <label class="mr-1">ถึง:</label>
                                <div class="input-group mr-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="eDate" id="่eDate" :error="'date'" required />
                                </div>
                                <button wire:click.prevent="refreshData" class="btn btn-sm btn-primary mr-1"><i class="fas fa-sync-alt"></i>
                                ดึงข้อมูลใหม่</button>
                                <button wire:click.prevent="exportExcel" class="btn btn-sm btn-success"><i class="fas fa-file-excel mr-1"></i>
                                Excel</button>
                            </div>
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
                                    ใบสั่งขาย
                                    <span wire:click="sortBy('salesBackOrders.sonumber')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.sonumber' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.sonumber' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    วันที่
                                    <span wire:click="sortBy('salesBackOrders.sodate')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.sodate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.sodate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    สินค้า
                                    <span wire:click="sortBy('salesBackOrders.item')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.item' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.item' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    จำนวนสั่ง
                                    <span wire:click="sortBy('salesBackOrders.quantityord')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.quantityord' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.quantityord' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    ค้างส่ง
                                    <span wire:click="sortBy('salesBackOrders.quantitybac')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.quantitybac' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.quantitybac' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    ยอดเงิน
                                    <span wire:click="sortBy('salesBackOrders.amount')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.amount' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.amount' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ผู้ซื้อ
                                    <span wire:click="sortBy('salesBackOrders.customer')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'salesBackOrders.customer' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'salesBackOrders.customer' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($salesBackOrders) > 0)
                            @foreach ($salesBackOrders as $row)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $salesBackOrders->firstitem()-1  }}</td>
                                <td scope="col">{{ $row->sonumber }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($row->sodate)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $row->item }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->quantityord, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->quantitybac, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->amount, 2) }} </td>
                                <td scope="col">{{ $row->customer }} </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $salesBackOrders->links() }} <span
                        class="ml-2">จำนวน {{ number_format($salesBackOrders->Total(),0) }} รายการ</span>
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
</div>