<div class="modal fade bd-example-modal-xl" id="soDeliveryForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 95%;">
        <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        ใบส่งสินค้า
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
                        <div class="col">
                            <label class="">เลขที่ใบสั่งขาย</label>
                            <input type="text" class="form-control form-control-sm mb-1" readonly wire:model.defer="soHeader.snumber">
                        </div>
                        <div class="col">
                            <label class="">วันที่ใบสั่งขาย</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'" disabled />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">เลขที่ใบส่งสินค้า</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.deliveryno">
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
                            <label class="">เลขที่ใบสั่งซื้อลูกค้า</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.refno" disabled>
                        </div>
                        <div class="col">
                            <label class="">วันที่ครบกำหนด</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.duedate" id="duedate" :error="'date'" disabled />
                            </div>
                        </div>
                        <div class="col">
                        </div>
                        <div class="col">                            
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ</label>
                            <div {{ $showEditModal ? '' : 'class=d-none'}}>
                                <input type="text" class="form-control form-control-sm mb-1" readonly wire:model.defer="soHeader.shipname">
                            </div>
                            <div {{ $showEditModal ? 'class=d-none' : 'class=float-top'}}>
                                <x-select2 id="customer-select2" wire:model.defer="soHeader.customerid">
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
                                <input class="form-check-input" type="checkbox" disabled wire:model.defer="soHeader.exclusivetax" wire:change="checkExclusiveTax">
                                <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="closed">
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
                                        <th scope="col"></th>
                                        <th scope="col">รหัส</th>
                                        <th scope="col" style="width: 25%;">รายละเอียด</th>
                                        <th scope="col" style="width: 7%;">จำนวน</th>
                                        <th scope="col">ต่อหน่วย</th>
                                        <th scope="col">รวม</th>
                                        <th scope="col">ส่วนลด</th>
                                        <th scope="col" style="width: 5%;">%ภาษี</th>
                                        <th scope="col">ภาษี</th>
                                        <th scope="col">สุทธิ</th>
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
                                            <input type="text" class="form-control form-control-sm" readonly wire:model.defer="soDetails.{{$index}}.description">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.quantity">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.unitprice">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model="soDetails.{{$index}}.amount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model="soDetails.{{$index}}.discountamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model="soDetails.{{$index}}.taxrate">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model="soDetails.{{$index}}.taxamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" wire:model="soDetails.{{$index}}.netamount">
                                        </td>
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
    window.addEventListener('show-soDeliveryForm', event => {
        $('#soDeliveryForm').modal('show');
    })

    window.addEventListener('hide-soDeliveryForm', event => {
        $('#soDeliveryForm').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('customer-select2');
    })
</script>
@endpush