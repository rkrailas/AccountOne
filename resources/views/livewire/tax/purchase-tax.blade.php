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
                    <h1 class="m-0 text-dark">ข้อมูลภาษีซื้อ</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-0">
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
                                    <x-datepicker wire:model.defer="sDate" id="่sDate" :error="'date'" style="width: 100px;" />
                                </div>
                                <label class="mr-1">ถึง:</label>
                                <div class="input-group mr-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="eDate" id="่eDate" :error="'date'" style="width: 100px;"/>
                                </div>
                                <select wire:model.defer="isInputTax" class="form-control form-control-sm mr-1">
                                    <option value="0">ทั้งหมด</option>
                                    <option value="1">ภาษีซื้อตัวจริง (ทั้งหมด)</option>
                                    <option value="2">ภาษีซื้อตัวจริง (กำหนดยื่นแล้ว)</option>
                                    <option value="3">ภาษีซื้อตัวจริง (ยังไม่กำหนดยื่น)</option>
                                    <option value="4">ภาษีซื้อไม่ถึงกำหนด</option>
                                </select>
                                <button wire:click.prevent="refreshData" class="btn btn-sm btn-primary mr-1"><i
                                        class="fas fa-sync-alt"></i>
                                    Refresh</button>
                                <button wire:click.prevent="exportExcel" class="btn btn-sm btn-success"><i
                                        class="fas fa-file-excel mr-1"></i>
                                    Excel</button>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-0 pl-0">
                            <input wire:model.lazy="searchTerm" type="text"
                                class="form-control form-control-sm border-0" placeholder="Search">
                            <!-- lazy=Lost Focus ถึงจะ Postback  -->
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
                    <div class="form-inline">                        
                        <label class=" mr-1">วันที่:</label>
                        <div class="input-group mr-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="sendTaxDate" id="sendTaxDate" :error="'date'" style="width: 100px;"/>
                        </div>     
                          <span class="badge badge-info mr-1 p-2">รายการที่เลือก {{ count($selectedRows) }}</span>
                          <span class="badge badge-info mr-1 p-2">ยอดภาษีที่เลือก {{ number_format($sumSelectedVAT, 2) }}</span>
                        <button type="button" class="btn btn-sm btn-warning mr-1" {{ $selectedRows ? '' : 'disabled' }}
                            wire:click.prevent="saveSendTaxDate" >
                            <i class="far fa-calendar-check"></i>
                            ยืนภาษี</button>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <table class="table table-hover small">
                        <thead>
                            <tr>
                                <th>
                                    <div d-inline ml-2>
                                        <input wire:model="selectPageRows" type="checkbox" name="" id="todoCheck2">
                                        <label for="todoCheck2"></label>
                                    </div>
                                </th>
                                <th scope="col">#</th>
                                <th scope="col">
                                    วันที่ใบกำกับ
                                    <span wire:click="sortBy('taxdata.journaldate')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.journaldate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.journaldate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เลขที่ใบกำกับ
                                    <span wire:click="sortBy('taxdata.taxnumber')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.taxnumber' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.taxnumber' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เลขที่อ้างอิง
                                    <span wire:click="sortBy('taxdata.reference')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.reference' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.reference' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ก่อน VAT
                                    <span wire:click="sortBy('taxdata.beforevat')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.beforevat' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.beforevat' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    VAT
                                    <span wire:click="sortBy('taxdata.taxamount')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.taxamount' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.taxamount' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ยอดรวม
                                    <span wire:click="sortBy('taxdata.amountcur')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.amountcur' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.amountcur' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เลขที่ใบสำคัญ
                                    <span wire:click="sortBy('taxdata.gltran')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.gltran' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.gltran' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    คำอธิบาย
                                    <span wire:click="sortBy('taxdata.description')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.description' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.description' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ผู้ขาย
                                    <span wire:click="sortBy('customer.name')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'customer.name' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'customer.name' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เลขผู้เสียภาษี
                                    <span wire:click="sortBy('customer.taxid')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'customer.taxid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'customer.taxid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ภาษีตัวจริง
                                    <span wire:click="sortBy('taxdata.isinputtax')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.isinputtax' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.isinputtax' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    วันที่ยื่นภาษี
                                    <span wire:click="sortBy('taxdata.ram_sendtaxdate')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'taxdata.ram_sendtaxdate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'taxdata.ram_sendtaxdate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($taxdatas) > 0)
                            @foreach ($taxdatas as $taxdata)
                            <tr>
                                <td>
                                    <div d-inline ml-2>
                                        <input wire:model="selectedRows" type="checkbox" value="{{ $taxdata->id }}"
                                            id="{{ $taxdata->id }}">
                                        <label for="{{ $taxdata->id }}"></label>
                                    </div>
                                </td>
                                <td scope="col">{{ $loop->iteration + $taxdatas->firstitem()-1 }}</td>
                                <td scope="col">{{ \Carbon\Carbon::parse($taxdata->journaldate)->format('Y-m-d') }}
                                </td>
                                <td scope="col">{{ $taxdata->taxnumber }} </td>
                                <td scope="col">{{ $taxdata->reference }} </td>                                
                                <td scope="col" class="text-right">{{ number_format($taxdata->amountcur -
                                    $taxdata->taxamount, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($taxdata->taxamount, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($taxdata->amountcur, 2) }} </td>
                                <td scope="col">{{ $taxdata->gltran }} </td>
                                <td scope="col">{{ $taxdata->description }} </td>
                                <td scope="col">{{ $taxdata->name }} </td>
                                <td scope="col">{{ $taxdata->taxid }} </td>
                                <td scope="col" class="text-center">
                                    @if ($taxdata->isinputtax)
                                        <i class="fas fa-check"></i>
                                    @endif
                                </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($taxdata->ram_sendtaxdate)->format('Y-m-d') }} </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5"></td>
                                <td class="text-right font-weight-bold">{{ number_format($totalBeforeVAT, 2) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($totalTaxAmount, 2) }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($totalAmountCur, 2) }}</td>
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
                        <select class="form-control form-control-sm" style="width: 80px;"
                            wire:model.lazy="numberOfPage">
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

<script>
    window.addEventListener('updatedSendTaxDate', event => {
        toastr.success(event.detail.message, 'Success!');
    });
</script>