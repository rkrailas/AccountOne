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
                    <h1 class="m-0 text-dark">ใบแจ้งหนี้/ใบวางบิล</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ใบแจ้งหนี้/ใบวางบิล</li>
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
                        <div class="form-inline">
                            <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle"
                                mr-1></i>
                            สร้างข้อมูลใหม่</button>
                            <label class=" ml-1 mr-1">วันที่:</label>
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
                                <th scope="col">ใบแจ้งหนี้
                                    <span wire:click="sortBy('billingno')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'billingno' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'billingno' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">วันที่
                                    <span wire:click="sortBy('billingdate')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'billingdate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'billingdate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">ครบกำหนด
                                    <span wire:click="sortBy('duedate')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'duedate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'duedate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
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
                                <th scope="col">คงเหลือ
                                    <span wire:click="sortBy('balance')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'balance' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'balance' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($billingNotices as $billingNotice)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $billingNotices->firstitem()-1  }}</td>
                                <td scope="col">{{ $billingNotice->billingno }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($billingNotice->billingdate)->format('Y-m-d') }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($billingNotice->duedate)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $billingNotice->customername }} </td>
                                <td scope="col">{{ number_format($billingNotice->amount,2) }} </td>
                                <td scope="col">{{ number_format($billingNotice->balance,2) }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $billingNotice->billingno }}')">
                                        <i class="fas fa-search mr-2"></i>
                                    </a>
                                    <a href="" wire:click.prevent="confirmDelete('{{ $billingNotice->billingno }}')">
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $billingNotices->links() }} <span
                        class="ml-2">จำนวน {{ number_format($billingNotices->Total(),0) }} รายการ</span>
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

    @include('livewire.accstar.finance._modalBillingNotice')
</div>
