<div class="modal" id="SalesOrderForm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        ใบสั่งขาย
                    </h5>
                    <div class="float-right">
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
                        <div class="col">
                            <label class="">เลขที่ใบสั่งขาย</label>
                            <input type="text" class="form-control form-control-sm mb-1 @error('snumber') is-invalid @enderror" 
                            {{ $showEditModal ? 'readonly' : '' }}    
                            wire:model.defer="soHeader.snumber">
                                @error('snumber')
                                <div class="invalid-feedback">
                                    เลขที่เอกสารซ้ำ
                                </div>
                                @enderror
                        </div>
                        <div class="col">
                            <label class="">วันที่ใบสั่งขาย</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">ใช้ได้จนถึง</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.expirydate" id="expiryDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">วันที่ส่งสินค้า</label>
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
                    <div class="row ">
                        <div class="col">
                            <label class="">วันที่ครบกำหนดชำระ</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.duedate" id="duedatedate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">เลขที่ใบสั่งซื้อลูกค้า</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.refno">
                        </div>
                        <div class="col">
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ</label>
                            <div>
                                <x-select2 id="customer-select2" wire:model.defer="soHeader.customerid" required="true">
                                    <option value=" ">---โปรดเลือก---</option>
                                    @foreach($customers_dd as $row)
                                    <option value='{{ $row->customerid }}'>
                                        {{ $row->customerid . ': ' . $row->name }}
                                    </option>
                                    @endforeach
                                </x-select2>
                            </div>
                        </div>
                        <div class="col">
                            <label class="">ที่อยู่</label>
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
                            <table class="table table-striped myGridTB" id="table" style="width:1140px;">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">
                                            <button class="btn btn-sm btn-primary" wire:click.prevent="addRowInGrid">+Add</button>
                                        </th>
                                        <th scope="col" style="width: 10%;">รหัส</th>
                                        <th scope="col" style="width: 20%;">รายละเอียด</th>
                                        <th scope="col" style="width: 15%;">SN / Lot</th>
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
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือกสินค้า" readonly
                                                    wire:model.defer="soDetails.{{$index}}.itemid">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary form-control-sm" type="button" 
                                                        wire:click.prevent="showModalItem('{{$index}}')">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="soDetails.{{$index}}.description">
                                        </td>
                                        <td>              
                                            @if ($soDetails[$index]['stocktype'] == '4')                              
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือก S/N" readonly
                                                        wire:model.defer="soDetails.{{$index}}.serialno">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary form-control-sm" type="button" 
                                                            wire:click.prevent="showModalSN('{{ $index }}')">
                                                            <i class="fas fa-ellipsis-h"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif ($soDetails[$index]['stocktype'] == '9')                              
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือก Lot Number" readonly
                                                        wire:model.defer="soDetails.{{$index}}.lotnumber">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary form-control-sm" type="button" 
                                                            wire:click.prevent="showLotNumber('{{ $index }}')">
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


@push('js')
<script>
    window.addEventListener('show-SalesOrderForm', event => {
        $('#SalesOrderForm').modal('show');
    })

    window.addEventListener('hide-SalesOrderForm', event => {
        $('#SalesOrderForm').modal('hide');
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