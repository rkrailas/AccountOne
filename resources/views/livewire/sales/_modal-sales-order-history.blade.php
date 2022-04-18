<div class="modal fade bd-example-modal-xl" id="SalesOrderHistoryForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 95%;">
        <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        ใบสั่งขาย
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Closed</button>
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
                                <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'" readonly />
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
                                <x-datepicker wire:model.defer="soHeader.expirydate" id="expiryDate" :error="'date'" readonly />
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
                                <x-datepicker wire:model.defer="soHeader.deliverydate" id="deliverydate" :error="'date'" readonly />
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
                                <x-datepicker wire:model.defer="soHeader.duedate" id="duedatedate" :error="'date'" readonly />
                            </div>
                        </div>
                        <div class="col">
                            <label class="">เลขที่ใบสั่งซื้อลูกค้า</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.refno" readonly>
                        </div>
                        <div class="col">
                        </div>
                        <div class="col">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ</label>
                                <input type="text" class="form-control form-control-sm mb-1" readonly wire:model.defer="soHeader.shipname" readonly>
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
                        </div>
                    </div>

                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped myGridTB" id="table" style="width: 1200px;">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">#</th>
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
                                            {{ $soDetails[$index]['itemid'] }}
                                        </td>
                                        <td>
                                            {{ $soDetails[$index]['description'] }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['quantity'],2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['unitprice'],2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['amount'],2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['discountamount'],2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['taxrate'],2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['taxamount'],2) }}
                                        </td>
                                        <td style="text-align: right;">
                                            {{ number_format($soDetails[$index]['netamount'],2) }}
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
    window.addEventListener('show-SalesOrderHistoryForm', event => {
        $('#SalesOrderHistoryForm').modal('show');
    })

    window.addEventListener('hide-SalesOrderHistoryForm', event => {
        $('#SalesOrderHistoryForm').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })
</script>
@endpush