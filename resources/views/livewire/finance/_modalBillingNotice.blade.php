<div class="modal fade bd-example-modal-xl" id="billingNoticeForm" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog" style="max-width: 85%;">
        <form autocomplete="off" wire:submit.prevent="createUpdateBillingNotice">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        สร้างใบแจ้งหนี้/ใบวางบิล
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn  btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>{{ $showEditModal ? 'Close' : 'Cancel' }}</button>
                        @if ($showEditModal == false)
                            <button type="submit" class="btn  btn-primary">
                                <i class="fa fa-save mr-1"></i>
                                <span>Save</span>
                            </button>
                        @endif                        
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-6 mb-1">
                            <label class=" mr-1">ชื่อ:</label>
                            <div {{ $showEditModal ? '' : 'class=d-none' }}>
                                <input type="text" class="form-control form-control-sm" readonly
                                    wire:model.defer="billingHeader.customername">
                            </div>
                            <div {{ $showEditModal ? 'class=d-none' : 'class=float-top' }}>
                                <div wire:ignore>
                                    <x-select2 wire:model="billingHeader.customerid" id="customer-dropdown">
                                        <option value=" ">---โปรดเลือก---</option>
                                        @foreach($customers_dd as $row)
                                        <option value='{{ $row->customerid }}'>
                                            {{ $row->customerid . ': ' . $row->name }}
                                        </option>
                                        @endforeach
                                    </x-select2>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class=" mr-1">บันทึกข้อความ:</label>
                            <input type="text" class="form-control form-control-sm" wire:model.defer="billingHeader.notes">
                        </div>                                               
                    </div>
                    <div class="row mb-2">
                        <div class="col-3">
                            <label class=" mr-1">เลขที่ใบแจ้งหนี้:</label>
                            <input type="text" class="form-control form-control-sm" {{ $showEditModal ? 'readonly' : ''  }}
                                required wire:model.defer="billingHeader.billingno">
                        </div>
                        <div class="col-3">
                            <label class=" mr-1">วันที่ใบแจ้งหนี้:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="billingHeader.billingdate" id="billingDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col-3">
                            <label class=" mr-1">วันที่ครบกำหนด:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="billingHeader.duedate" id="dueDate" :error="'date'" required />
                            </div>
                        </div> 
                        <div class="col-3">
                        </div>
                    </div>
                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped myGridTB">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div d-inline ml-2>
                                                @if ($showEditModal)
                                                    #
                                                @else
                                                    <input wire:model="selectPageRows" type="checkbox" name="" id="todoCheck2">
                                                    <label for="todoCheck2"></label>
                                                @endif                                                
                                            </div>
                                        </th>
                                        <th scope="col">ใบกำกับภาษี</th>
                                        <th scope="col" style="width: 50%;">รายละเอียด</th>
                                        <th scope="col" style="text-align: right; width: 15%;">ยอดคงเหลือ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($billingDetails as $index => $bankDetail)
                                    <tr>
                                        <td scope="row" class="align-middle text-center">
                                            <div d-inline ml-2>
                                                @if ($showEditModal)
                                                    {{ $loop->iteration }}
                                                @else
                                                    <input wire:model="selectedRows" type="checkbox" value="{{ $billingDetails[$index]['taxdataid'] }}"
                                                        id="{{ $billingDetails[$index]['taxdataid'] }}">
                                                    <label for="{{ $billingDetails[$index]['taxdataid'] }}"></label>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            {{ $billingDetails[$index]['taxref'] }}
                                        </td>
                                        <td class="align-middle">
                                            {{ $billingDetails[$index]['description'] }}
                                        </td>
                                        <td class="align-middle" style="text-align: right;">
                                            {{ number_format($billingDetails[$index]['balance'], 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">{{ number_format($sumBalance,2) }}</td>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </form>
    </div>
</div>

@push('js')

<script>
    window.addEventListener('show-billingNoticeForm', event => {
            $('#billingNoticeForm').modal('show');
        })

        window.addEventListener('hide-billingNoticeForm', event => {
            $('#billingNoticeForm').modal('hide');
        })

        window.addEventListener('clear-select2', event => {
            clearSelect2('customer-dropdown');
        })

</script>

@endpush