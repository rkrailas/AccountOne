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
                    <h1 class="m-0 text-dark">ข้อมูลภาษีขาย</h1>
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
                    <table class="table table-hover table-sm small">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">                                    
                                    วันที่บัญชี
                                    <span wire:click="sortBy('a.journaldate')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.journaldate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.journaldate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    เลขที่ใบกำกับ
                                    <span wire:click="sortBy('a.taxnumber')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.taxnumber' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.taxnumber' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    เลขที่อ้างอิง
                                    <span wire:click="sortBy('a.reference')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.reference' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.reference' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    ก่อน VAT
                                    <span wire:click="sortBy('a.beforevat')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.beforevat' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.beforevat' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    VAT
                                    <span wire:click="sortBy('a.taxamount')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.taxamount' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.taxamount' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    ยอดรวม
                                    <span wire:click="sortBy('a.amountcur')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.amountcur' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.amountcur' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    เลขที่ใบสำคัญ
                                    <span wire:click="sortBy('a.gltran')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.gltran' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.gltran' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    คำอธิบาย
                                    <span wire:click="sortBy('a.description')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'a.description' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'a.description' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    ผู้ซื้อ
                                    <span wire:click="sortBy('b.name')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'b.name' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'b.name' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    เลขผู้เสียภาษี
                                    <span wire:click="sortBy('b.taxid')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'b.taxid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'b.taxid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">                                    
                                    สาขา
                                    <span wire:click="sortBy('b.branchno')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'b.branchno' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'b.branchno' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($taxdatas) > 0)
                            @foreach ($taxdatas as $taxdata)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $taxdatas->firstitem()-1  }}</td>
                                <td scope="col">{{ \Carbon\Carbon::parse($taxdata->journaldate)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $taxdata->taxnumber }} </td>
                                <td scope="col">{{ $taxdata->reference }} </td>                                
                                <td scope="col" class="text-right">{{ number_format($taxdata->amountcur - $taxdata->taxamount, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($taxdata->taxamount, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($taxdata->amountcur, 2) }} </td>
                                <td scope="col">{{ $taxdata->gltran }} </td>
                                <td scope="col">{{ $taxdata->description }} </td>
                                <td scope="col">{{ $taxdata->name }} </td>
                                <td scope="col">{{ $taxdata->taxid }} </td>
                                <td scope="col">{{ $taxdata->branchno }} </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4"></td>
                                <td class="text-right font-weight-bold" style="font-size: 12px;">{{ number_format($totalBeforeVAT, 2) }}</td>
                                <td class="text-right font-weight-bold" style="font-size: 12px;">{{ number_format($totalTaxAmount, 2) }}</td>
                                <td class="text-right font-weight-bold" style="font-size: 12px;">{{ number_format($totalAmountCur, 2) }}</td>
                                <td colspan="5"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $taxdatas->links() }} <span
                        class="ml-2">จำนวน {{ number_format($taxdatas->Total(),0) }} รายการ</span>
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