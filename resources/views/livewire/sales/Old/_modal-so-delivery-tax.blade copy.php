<div class="modal fade bd-example-modal-xl" id="soDeliveryTaxForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 95%;">
        <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        ส่งสินค้าพร้อมใบกำกับ
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" wire:click.prevent="showGL" {{ $showEditModal ? '' : 'disabled' }}>
                            Gen GL</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>
                            @if($showEditModal)
                            <span>Save Changes</span>
                            @else
                            <span>Save</span>
                            @endif
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row ">
                        <div class="col-3">
                            <label class="">เลขที่ใบสั่งขาย:</label>
                            <input type="text" class="form-control form-control-sm mb-1 @error('snumber') is-invalid @enderror"
                            {{ $showEditModal ? 'readonly' : '' }}
                            required wire:model.defer="soHeader.snumber">
                            @error('snumber')
                            <div class="invalid-feedback">
                                เลขที่เอกสารซ้ำ
                            </div>
                            @enderror
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ใบสั่งขาย:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'"/>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="">คำอธิบายรายการ:</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.sonote">
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-3">
                            <label class="">เลขที่ใบกำกับ:</label>
                            <input type="text" class="form-control form-control-sm mb-1 {{ $errorTaxNumber ? 'is-invalid' : '' }}" 
                                required wire:model.defer="soHeader.invoiceno">
                                @if($errorTaxNumber)
                                <div class="invalid-feedback">
                                    เลขที่เอกสารซ้ำ
                                </div>
                                @endif
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ใบกำกับ:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.invoicedate" id="invoiceDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="">เลขที่ใบสำคัญ:</label>
                            <input type="text" class="form-control form-control-sm mb-1 {{ $errorGLTran ? 'is-invalid' : '' }}" 
                                required wire:model.defer="soHeader.deliveryno">
                                @if($errorGLTran)
                                <div class="invalid-feedback">
                                    เลขที่เอกสารซ้ำ
                                </div>
                                @endif
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ใบสำคัญ:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.journaldate" id="journaldate" :error="'date'" required />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ:</label>
                            <div>
                                <x-select2 id="customer-select2" wire:model.defer="soHeader.customerid" required="true">
                                    @foreach($customers_dd as $row)
                                    <option value='{{ $row->customerid }}'>
                                        {{ $row->customerid . ': ' . $row->name }}
                                    </option>
                                    @endforeach
                                </x-select2>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="">ที่อยู่:</label>
                            <textarea class="form-control form-control-sm mb-1" rows="2" readonly wire:model.defer="soHeader.full_address"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="soHeader.exclusivetax" wire:change="checkExclusiveTax">
                                <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" {{ $showEditModal ? '' : 'disabled' }}
                                    wire:model.defer="soHeader.posted">
                                <label class="form-check-label" for="posted">ปิดรายการ</label>
                            </div>
                        </div>
                    </div>

                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped myGridTB">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">
                                            <button class="btn btn-sm btn-primary" wire:click.prevent="addRowInGrid">+Add</button>
                                        </th>
                                        <th scope="col">รหัส</th>
                                        <th scope="col" style="width: 25%;">รายละเอียด</th>
                                        <th scope="col" style="width: 7%;">จำนวน</th>
                                        <th scope="col">ต่อหน่วย</th>
                                        <th scope="col">รวม</th>
                                        <th scope="col">ส่วนลด</th>
                                        <th scope="col" style="width: 5%;">%ภาษี</th>
                                        <th scope="col">ภาษี</th>
                                        <th scope="col">สุทธิ</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($soDetails as $index => $soDetail)
                                    <tr>
                                        <td scope="row" class="align-middle text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm" required wire:model.lazy="soDetails.{{$index}}.itemid">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($itemNos_dd as $itemNo_dd)
                                                <option value="{{ $itemNo_dd->itemid }}">{{ $itemNo_dd->itemid }}:
                                                    {{ $itemNo_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="soDetails.{{$index}}.description">
                                            @if ($soDetails[$index]['stocktype'] == '4')
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm mb-1" placeholder="Serial No"
                                                    wire:model.defer="soDetails.{{$index}}.serialno">
                                                <div class="input-group-append">
                                                <button class="btn btn-primary form-control-sm" type="button" wire:click.prevent="showSN('{{ $index }}')">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                                {{ $soDetails[$index]['stocktype'] == '4' ? 'readonly' : ''}}
                                                wire:model.lazy="soDetails.{{$index}}.quantity">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.unitprice">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model.defer="soDetails.{{$index}}.amount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.discountamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.taxrate">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.defer="soDetails.{{$index}}.taxamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.defer="soDetails.{{$index}}.netamount">
                                        </td>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="" wire:click.prevent="removeRowInGrid({{ $index }})">
                                                <i class="fa fa-trash text-danger"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td></td>                                        
                                        <td>ยอดรวม</td>
                                        <td>{{ number_format($sumQuantity,2) }}</td>
                                        <td></td>
                                        <td>{{ number_format($sumAmount,2) }}</td>
                                        <td>{{ number_format($soHeader['discountamount'],2) }}</td>
                                        <td></td>
                                        <td>{{ number_format($soHeader['salestax'],2) }}</td>
                                        <td>{{ number_format($soHeader['sototal'],2) }}</td>
                                        <td></td>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.Grid -->
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('js')
<script>
    window.addEventListener('show-soDeliveryTaxForm', event => {
        $('#soDeliveryTaxForm').modal('show');
    })

    window.addEventListener('hide-soDeliveryTaxForm', event => {
        $('#soDeliveryTaxForm').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('customer-select2');
    })

    window.addEventListener('bindToSelect', event => {
        $(event.detail.selectName).html(" ");
        $(event.detail.selectName).append(event.detail.newOption);
    })
</script>
@endpush