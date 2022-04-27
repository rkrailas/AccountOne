<div class="modal" id="adjustSoDeliveryTaxForm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        ปรับปรุงราคาขาย
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
                    <div class="row mb-2">
                        <div class="col-3">
                            <label class="">เลขที่ใบกำกับ:</label>
                            @if($showEditModal)
                                <input type="text" class="form-control form-control-sm mb-1" readonly wire:model.defer="soHeader.refno">
                            @else
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm mb-1" placeholder="ค้นหา"
                                        wire:model.defer="taxNumber" wire:keydown.enter="searchDoc">
                                    <div class="input-group-append">
                                    <button class="btn btn-primary form-control-sm" type="button" wire:click.prevent="searchDoc">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-3">
                        </div>
                        <div class="col-6">
                            <label class="">คำอธิบายรายการ:</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.sonote">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3">
                            <label class="">เลขที่ปรับปรุง:</label>
                            <input type="text" class="form-control form-control-sm mb-1 @error('sonumber') is-invalid @enderror" 
                                required wire:model.defer="soHeader.sonumber">
                                @error('sonumber')
                                <div class="invalid-feedback">
                                    เลขที่เอกสารซ้ำ
                                </div>
                                @enderror
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ปรับปรุง:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="">เลขที่ใบสำคัญ:</label>
                            <input type="text" class="form-control form-control-sm mb-1 {{ $errorGLTran ? 'is-invalid' : '' }}" required 
                                wire:model.defer="soHeader.deliveryno">
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
                                <x-datepicker wire:model.defer="soHeader.deliverydate" id="deliverydate" :error="'date'" required />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-3">
                            <label class="">เลขที่ใบกำกับใหม่:</label>
                            <input type="text" class="form-control form-control-sm mb-1 {{ $errorTaxNumber ? 'is-invalid' : '' }}" required                                 
                                wire:model.defer="soHeader.invoiceno">
                                @if($errorTaxNumber)
                                <div class="invalid-feedback">
                                    เลขที่เอกสารซ้ำ
                                </div>
                                @endif
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ใบกำกับใหม่:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.invoicedate" id="invoicedate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="account">บัญชีขาย</label>
                            <x-select2 id="salesaccount-select2" wire:model.defer="soHeader.salesaccount" required="true">
                                @foreach($salesAcs_dd as $row)
                                <option value="{{ $row->account }}">
                                    {{ $row->account . ': ' . $row->accnameother }}
                                </option>
                                @endforeach
                            </x-select2>
                        </div>
                        <div class="col-3">
                        </div>                    
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ:</label>
                            <input type="text" class="form-control form-control-sm mb-1" readonly wire:model.defer="soHeader.shipname">
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
                                    wire:model.defer="soHeader.closed">
                                <label class="form-check-label" for="closed">ปิดรายการ</label>
                            </div>
                        </div>
                    </div>

                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped myGridTB">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col"> # </th>
                                        <th scope="col">รหัส</th>
                                        <th scope="col" style="width: 25%;">รายละเอียด</th>
                                        <th scope="col" style="width: 7%;">จำนวน</th>
                                        <th scope="col">ต่อหน่วย</th>
                                        <th scope="col">ต่อหน่วยใหม่</th>
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
                                            <input type="text" class="form-control form-control-sm" readonly wire:model.defer="soDetails.{{$index}}.itemid">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="soDetails.{{$index}}.description">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model.defer="soDetails.{{$index}}.quantity">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model.defer="soDetails.{{$index}}.unitprice">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.cost">
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
                                        <td></td>
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

@push('js')
<script>
    window.addEventListener('show-adjustSoDeliveryTaxForm', event => {
        $('#adjustSoDeliveryTaxForm').modal('show');
    })

    window.addEventListener('hide-adjustSoDeliveryTaxForm', event => {
        $('#adjustSoDeliveryTaxForm').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('salesaccount-select2');
    })

    window.addEventListener('bindToSelect', event => {
        $(event.detail.selectName).html(" ");
        $(event.detail.selectName).append(event.detail.newOption);
    })
</script>
@endpush