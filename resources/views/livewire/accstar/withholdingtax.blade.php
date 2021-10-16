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
                    <h1 class="m-0 text-dark">ข้อมูลภาษีหัก ณ ที่จ่าย</h1>
                </div>
            </div>
        </div>
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
                                <select wire:model.defer="whType" class="form-control form-control-sm mr-1">
                                    <option value="3">ภงด 3</option>
                                    <option value="53">ภงด 53</option>
                                </select>
                                <button wire:click.prevent="refreshData" class="btn btn-sm btn-primary mr-1"><i
                                        class="fas fa-sync-alt"></i>
                                    Refresh</button>
                                <button wire:click.prevent="exportExcel" class="btn btn-sm btn-success"><i
                                        class="fas fa-file-excel mr-1"></i>
                                    Excel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <table class="table table-hover small">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">
                                    ผู้มีรายได้
                                    <span wire:click="sortBy('withholdingtax.custname')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.custname' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.custname' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ที่อยู่
                                    <span wire:click="sortBy('withholdingtax.address')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.address' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.address' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เลขที่ประจำตัว
                                    <span wire:click="sortBy('withholdingtax.taxid')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.taxid' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.taxid' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    วัน/เดือน/ปี
                                    <span wire:click="sortBy('withholdingtax.gjournaldt')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.gjournaldt' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.gjournaldt' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ประเภทเงินได้
                                    <span wire:click="sortBy('withholdingtax.description')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.description' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.description' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    อัตรา
                                    <span wire:click="sortBy('withholdingtax.witholdtaxrate')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.witholdtaxrate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.witholdtaxrate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เงินที่จ่าย
                                    <span wire:click="sortBy('withholdingtax.witholdamt')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.witholdamt' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.witholdamt' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    ภาษี
                                    <span wire:click="sortBy('withholdingtax.witholdtax')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.witholdtax' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.witholdtax' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">
                                    เงื่อนไข
                                    <span wire:click="sortBy('withholdingtax.whpayby')" class="float-right text-sm"
                                        style="cursor: pointer;">
                                        <i
                                            class="fa fa-xs fa-arrow-up {{ $sortBy === 'withholdingtax.whpayby' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i
                                            class="fa fa-xs fa-arrow-down {{ $sortBy === 'withholdingtax.whpayby' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>                               
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($withholdingtaxs) > 0)
                            @foreach ($withholdingtaxs as $withholdingtax)
                            <tr>
                                <td scope="col">{{ $loop->iteration }}</td>
                                <td scope="col">{{ $withholdingtax->custname }} </td>
                                <td scope="col">{{ $withholdingtax->address }} </td>
                                <td scope="col">{{ $withholdingtax->taxid }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($withholdingtax->gjournaldt)->format('d/m/Y') }}</td>
                                <td scope="col">{{ $withholdingtax->description }} </td>
                                <td scope="col" class="text-right">{{ number_format($withholdingtax->witholdtaxrate , 1) }} </td>
                                <td scope="col" class="text-right">{{ number_format($withholdingtax->witholdamt , 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($withholdingtax->witholdtax , 2) }} </td>
                                <td scope="col" class="text-center">{{ $withholdingtax->whpayby }} </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7"></td>
                                <td class="text-right font-weight-bold" style="font-size: medium;">{{ number_format($totalWitholdAmt, 2) }}</td>
                                <td class="text-right font-weight-bold" style="font-size: medium;">{{ number_format($totalWitholdTax, 2) }}</td>
                                <td colspan="1"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>