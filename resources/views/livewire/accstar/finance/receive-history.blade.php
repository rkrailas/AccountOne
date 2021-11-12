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
                    <h1 class="m-0 text-dark">ประวัติการรับชำระ</h1>
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
                                    ใบสำคัญรับ
                                    <span wire:click="sortBy('bank.gltran')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'bank.gltran' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'bank.gltran' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    วันที่
                                    <span wire:click="sortBy('bank.gjournaldt')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'bank.gjournaldt' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'bank.gjournaldt' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    ผู้ซื้อ
                                    <span wire:click="sortBy('customer.customerid')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'customer.customerid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'customer.customerid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ยอดเงิน
                                </th>
                                <th scope="col">
                                    หัก ณ ที่จ่าย
                                </th>
                                <th scope="col">
                                    เพิ่ม/ลด
                                </th>
                                <th scope="col">
                                    ยอดสุทธิ
                                </th>
                                <th scope="col">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($receiveJournals) > 0)
                            @foreach ($receiveJournals as $row)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $receiveJournals->firstitem()-1  }}</td>
                                <td scope="col">{{ $row->gltran }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($row->gjournaldt)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $row->customer }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->balance, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->witholdtax, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->plus_deduct, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->balance - $row->witholdtax + $row->plus_deduct, 2) }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $row->gltran }}')">
                                        <i class="fas fa-search mr-2"></i>
                                    </a>
                                </td>
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $receiveJournals->links() }} <span
                        class="ml-2">จำนวน {{ number_format($receiveJournals->Total(),0) }} รายการ</span>
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

    @include('livewire.accstar.finance._modalReceiveHistory')
</div>