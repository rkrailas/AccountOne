<div class="modal fade bd-example-modal-xl" id="receiveOnSalesForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" 
    aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog" style="max-width: 85%;">
        <form autocomplete="off" wire:submit.prevent="createUpdateReceiveOnSales">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        @if($showEditModal)
                        แก้ไขใบสำคัญรับเงิน
                        @else
                        สร้างใบสำคัญรับเงิน
                        @endif
                    </h5>
                    <div class="float-right">
                        @if($showEditModal)
                        <button type="button" class="btn btn-secondary" wire:click.prevent="showGL">
                            Gen GL</button>
                        @endif
                        <button type="button" class="btn  btn-secondary" data-dismiss="modal" onclick="clearSelect2('customer-dropdown')">
                            <i class="fa fa-times mr-1"></i>Cancel</button>
                        <button type="submit" class="btn  btn-primary">
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
                    <!-- .Tab Header -->
                    <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                        <li class="nav-item" wire:ignore>
                            <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">ทั่วไป</a>
                        </li>
                        <li class="nav-item" wire:ignore>
                            <a class="nav-link" id="pills-account-tab" data-toggle="pill" href="#pills-account" role="tab" aria-controls="pills-account" aria-selected="false">บัญชี และอื่น ๆ</a>
                        </li>
                    </ul>
                    <!-- /.Tab Header -->

                    <div class="tab-content ml-2 mt-2" id="pills-tabContent">
                        <!-- .Tab ข้อมูลทั่วไป -->
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" wire:ignore.self>
                            <div class="row">
                                <div class="col-6 mb-1 form-group">
                                    <label class=" mr-1">ชื่อ:</label>
                                    <div {{ $showEditModal ? '' : 'class=d-none'}}>
                                        <input type="text" class="form-control form-control-sm" readonly wire:model.defer="bankHeader.customername">
                                    </div>
                                    <div {{ $showEditModal ? 'class=d-none' : 'class=float-top'}}>
                                        <div wire:ignore>
                                            <x-select2 wire:model="bankHeader.customerid" id="customer-dropdown">
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
                                <div class="col-6 mb-1 form-group">
                                    <label class=" mr-1">ภาษีถูกหัก:</label>
                                    <div class="form-inline d-flex justify-content-between">
                                        <select class="form-control form-control-sm" wire:model.lazy="bankHeader.taxscheme">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($taxTypes_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->code . ': ' . $row->description }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 30%;" wire:model.defer="bankHeader.witholdamt">
                                        <input type="number" step="0.01" class="form-control form-control-sm" style="width: 30%;" wire:model.defer="bankHeader.witholdtax">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-1 form-group">
                                    <label class="mr-1">เลขที่:</label>
                                    <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly wire:model.defer="bankHeader.gltran">
                                </div>
                                <div class="col mb-1 form-group">
                                    <label class=" mr-1">วันที่:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                        </div>
                                        <x-datepicker wire:model.defer="bankHeader.gjournaldt" id="่jrDate" :error="'date'" required />
                                    </div>
                                </div>
                                <div class="col-6 mb-1 form-group">
                                    <label class=" mr-1">ภาษีถูกหัก-1:</label>
                                    <div class="form-inline d-flex justify-content-between">
                                        <select class="form-control form-control-sm" wire:model.lazy="bankHeader.taxscheme1">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($taxTypes_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->code . ': ' . $row->description }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" class="form-control form-control-sm" required style="width: 30%;" wire:model.lazy="bankHeader.witholdamt1">
                                        <input type="number" step="0.01" class="form-control form-control-sm" required style="width: 30%;" wire:model.defer="bankHeader.witholdtax1">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-1 form-group">
                                    <label class=" mr-1">ชำระโดย:</label>
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.payby">
                                        <option value="">ยังไม่ชำระ</option>
                                        <option value="1">เงินสด</option>
                                        <option value="2">เช็ค</option>
                                        <option value="3">บัตรเครดิต</option>
                                        <option value="4">โอนเงิน</option>
                                        <option value="5">อื่่น ๆ</option>
                                        <option value="9">รวม</option>
                                    </select>
                                </div>
                                <div class="col mb-1 form-group">
                                    <label class=" mr-1">เลขอ้างอิง:</label>
                                    <input type="text" class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.documentref">
                                </div>
                                <div class="col mb-1 form-group"">
                                    <label class=" mr-1">เลขภาษีหัก:</label>
                                    <input type="text" class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.taxrunningno">
                                </div>
                                <div class="col form-group">
                                    <label class=" mr-1">ปิดรายการ:</label>
                                    <input type="checkbox" wire:model.defer="bankHeader.posted">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-1">
                                    <label class=" mr-1">รับเข้าบัญชี:</label>
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.account">
                                        <option value="">--โปรดเลือก---</option>
                                        @foreach ($accountNos_dd as $row)
                                        <option value='{{ $row->account }}'>
                                            {{ $row->account . ': ' . $row->accnameother }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col mb-1">
                                    <label class=" mr-1">บัญชีลูกหนี้:</label>
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.accountcus">
                                        <option value="">--โปรดเลือก---</option>
                                        @foreach ($accountNos_dd as $row)
                                        <option value='{{ $row->account }}'>
                                            {{ $row->account . ': ' . $row->accnameother }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <!-- .Grid -->
                            <div class="row mb-2">
                                <div class="col">
                                    <table class="table table-striped myGridTB">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th scope="col">เลขที่อ้างอิง</th>
                                                <th scope="col" style="width: 50%;">รายละเอียด</th>
                                                <th scope="col" style="text-align: right; width: 15%;">ยอดคงเหลือ</th>
                                                <th scope="col" style="text-align: right; width: 15%;">รับชำระ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($bankDetails as $index => $bankDetail)
                                            <tr>
                                                <td scope="row" class="align-middle">
                                                    <center>{{ $loop->iteration }}</center>
                                                </td>
                                                <td class="align-middle">
                                                    {{ $bankDetails[$index]['taxref'] }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $bankDetails[$index]['description'] }}
                                                </td>
                                                <td class="align-middle" style="text-align: right;">
                                                    <a href="#" wire:click.prevent="getBalance({{ $index }})">
                                                        {{ number_format($bankDetails[$index]['balance'],2) }}
                                                    </a>
                                                </td>
                                                <td class="align-middle">
                                                    <input type="number" step="0.01" class="form-control form-control-sm float-right" style="text-align: right; width: 120px;" wire:model.lazy="bankDetails.{{$index}}.amount">
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr style="text-align: right; color: blue; font-weight: bold;">
                                                <td></td>
                                                <td></td>
                                                <td class="align-middle">รวม</td>
                                                <td class="align-middle">{{ number_format($sumBalance,2) }}</td>
                                                <td class="align-middle">{{ number_format($sumAR,2) }}</td>
                                            <tr>
                                            <tr style="text-align: right; color: blue; font-weight: bold;">
                                                <td></td>
                                                <td></td>
                                                <td class="align-middle">เพิ่ม/<span style="color: red;">หัก</span></td>
                                                <td class="align-middle">{{ number_format($sumPlus,2) }}</td>
                                                <td class="align-middle"><span style="color: red;">{{ number_format($sumDeduct,2) }}</td>
                                            <tr>
                                            <tr style="text-align: right; color: blue; font-weight: bold;">
                                                <td></td>
                                                <td></td>
                                                <td class="align-middle">รับสุทธิ</td>
                                                <td></td>
                                                <td class="align-middle">{{ number_format($bankHeader['amount'],2) }}</td>
                                            <tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <!-- /.Grid -->
                        </div>
                        <!-- /.Tab ข้อมูลทั่วไป -->

                        <!-- .Tab Account -->
                        <div class="tab-pane fade" id="pills-account" role="tabpanel" aria-labelledby="pills-account-tab" wire:ignore.self>
                            <div class="row ">
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">ภาษีจ่าย:</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.accounttax">
                                        <option value="">---โปรดเลือก---</option>
                                        @foreach ($accountNos_dd as $row)
                                        <option value='{{ $row->account }}'>
                                            {{ $row->account . ': ' . $row->accnameother }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">ค่าปรับ (รายได้เบ็ดเตล็ด) :</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.accountcharge">
                                        <option value="">---โปรดเลือก---</option>
                                        @foreach ($accountNos_dd as $row)
                                        <option value='{{ $row->account }}'>
                                            {{ $row->account . ': ' . $row->accnameother }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">จำนวนเงิน:</label>
                                </div>
                                <div class="col-2">
                                    <input type="number" step="0.01" class="form-control form-control-sm mb-1 mr-1" style="text-align: right;" wire:model.lazy="bankHeader.fincharge">
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">ส่วนลด:</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.accountdis">
                                        <option value="">---โปรดเลือก---</option>
                                        @foreach ($accountNos_dd as $row)
                                        <option value='{{ $row->account }}'>
                                            {{ $row->account . ': ' . $row->accnameother }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">จำนวนเงิน:</label>
                                </div>
                                <div class="col-2">
                                    <input type="number" step="0.01" class="form-control form-control-sm mb-1 mr-1" style="text-align: right;" wire:model.lazy="bankHeader.findiscount">
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">ค่าธรรมเนียม:</label>
                                </div>
                                <div class="col-4">
                                    <select class="form-control form-control-sm mb-1 mr-1" wire:model.defer="bankHeader.accountfee">
                                        <option value="">---โปรดเลือก---</option>
                                        @foreach ($accountNos_dd as $row)
                                        <option value='{{ $row->account }}'>
                                            {{ $row->account . ': ' . $row->accnameother }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-2" style="text-align: right;">
                                    <label class=" mr-1">จำนวนเงิน:</label>
                                </div>
                                <div class="col-2">
                                    <input type="number" step="0.01" class="form-control form-control-sm mb-1 mr-1" style="text-align: right;" wire:model.lazy="bankHeader.feeamt">
                                </div>
                            </div>
                        </div>
                        <!-- /.Tab Account -->

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
        window.addEventListener('show-receiveOnSalesForm', event => {
            $('#receiveOnSalesForm').modal('show');
        });

        window.addEventListener('hide-receiveOnSalesForm', event => {
            $('#receiveOnSalesForm').modal('hide');
        });
    </script>

@endpush