<div class="modal fade bd-example-modal-xl" id="soDeliveryTaxForm" tabindex="-1" role="dialog"
    aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 95%;">
        <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        @if($showEditModal)
                        แก้ไขใบสั่งขาย
                        @else
                        สร้างใบสั่งขาย
                        @endif
                    </h5>
                    <div class="float-right">
                        @if($showEditModal)
                        <button type="button" class="btn btn-secondary" wire:click.prevent="showGL">
                            Gen GL</button>
                        @endif
                        <button type="button" class="btn btn-secondary" wire:click.prevent="closeSOModal" data-dismiss="modal">
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
                            <label class="">เลขที่ใบสั่งขาย:</label>
                            <input type="text" class="form-control mb-1" readonly
                                wire:model.defer="soHeader.snumber">
                        </div>
                        <div class="col">
                            <label class="">วันที่ใบสั่งขาย:</label>
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
                            <label class="">เลขที่ใบสำคัญ:</label>
                            <input type="text" class="form-control mb-1" {{ $showEditModal ? 'required' : 'readonly' }} 
                                wire:model.defer="soHeader.invoiceno">
                        </div>
                        <div class="col">
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
                    <div class="row ">
                        <div class="col">
                            <label class="">เลขที่ใบกำกับ:</label>
                            <input type="text" class="form-control mb-1" required
                                wire:model.defer="soHeader.deliveryno">
                        </div>
                        <div class="col">
                            <label class="">วันที่ใบกำกับ:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.deliverydate" id="deliveryDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">วันที่ครบกำหนด:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="soHeader.duedate" id="dueDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">การชำระเงิน:</label>
                            <select class="form-control mb-1" wire:model.defer="soHeader.payby">
                                <option value="0" selected>ยังไม่ชำระ</option>
                                <option value="1">เงินสด</option>
                                <option value="2">เช็ค</option>
                                <option value="3">บัตรเครดิต</option>
                                <option value="4">โอนเงิน</option>
                                <option value="5">อื่่น ๆ</option>
                                <option value="9">รวม</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ:</label>
                            <div {{ $showEditModal ? '' : 'class=d-none'}}>
                                <input type="text" class="form-control mb-1" readonly
                                    wire:model.defer="soHeader.shipname">
                            </div>
                            <div {{ $showEditModal ? 'class=d-none' : 'class=float-top'}}>
                                <div wire:ignore>
                                    <select id="customer-dropdown" class="form-control" 
                                        style="width: 100%" required 
                                        wire:model.defer="soHeader.customerid">
                                        <option value=''>--- โปรดเลือก ---</option>
                                        @foreach($customers_dd as $customer_dd)
                                        <option value='{{ $customer_dd->customerid }}'>
                                            {{ $customer_dd->customerid . ': ' . $customer_dd->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <label class="">ที่อยู่:</label>
                            <textarea class="form-control mb-1" rows="2" readonly 
                                wire:model.defer="soHeader.full_address"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="soHeader.exclusivetax"
                                    wire:change="checkExclusiveTax">
                                <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="soHeader.posted">
                                <label class="form-check-label" for="posted">ปิดรายการ</label>
                            </div>
                        </div>
                    </div>

                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            <button class="btn btn-sm btn-primary"
                                                wire:click.prevent="addRowInGrid">+Add</button>
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
                                        <td scope="row">
                                            <center>{{ $loop->iteration }}</center>
                                        </td>
                                        <td>
                                            <select class="form-control" required
                                                wire:model.lazy="soDetails.{{$index}}.itemid">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($itemNos_dd as $itemNo_dd)
                                                <option value="{{ $itemNo_dd->itemid }}">{{ $itemNo_dd->itemid }}:
                                                    {{ $itemNo_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                wire:model.defer="soDetails.{{$index}}.description">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required
                                                style="text-align: right;"
                                                wire:model.lazy="soDetails.{{$index}}.quantity">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required
                                                style="text-align: right;"
                                                wire:model.lazy="soDetails.{{$index}}.unitprice">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required readonly
                                                style="text-align: right;" wire:model="soDetails.{{$index}}.amount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required
                                                style="text-align: right;"
                                                wire:model="soDetails.{{$index}}.discountamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required
                                                style="text-align: right;"
                                                wire:model="soDetails.{{$index}}.taxrate">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required
                                                style="text-align: right;"
                                                wire:model="soDetails.{{$index}}.taxamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control" required
                                                style="text-align: right;" wire:model="soDetails.{{$index}}.netamount">
                                        </td>
                                        </td>
                                        <td>
                                            <center>
                                                <a href="" wire:click.prevent="removeRowInGrid({{ $index }})">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </center>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times mr-1"></i>Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save mr-1"></i>
                        @if($showEditModal)
                        <span>Save Changes</span>
                        @else
                        <span>Save </span>
                        @endif
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
    integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
    integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
$(document).ready(function() {    
    $('#customer-dropdown').select2({
        placeholder: "--- โปรดเลือก ---"
    });
    $('#customer-dropdown').on('change', function(e) {
        let data = $(this).val();
        @this.set('soHeader.customerid', data);
    });
});
</script>
@endpush