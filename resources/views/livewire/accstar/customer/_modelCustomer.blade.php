<div class="modal fade bd-example-modal-xl" id="customerForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <form autocomplete="off" wire:submit.prevent="{{ $showEditModal ? 'updateCustomer' : 'createCustomer' }}">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        @if($showEditModal)
                        แก้ไขข้อมูล #{{$state['customerid']}} : {{$state['name']}}
                        @else
                        สร้างข้อมูลลูกค้า
                        @endif
                    </div>
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
                    <div class="row">
                        <div class="col-md-12">
                            <!-- .Tab Header -->
                            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                                <li class="nav-item" wire:ignore>
                                    <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">ข้อมูลทั่วไป</a>
                                </li>
                                <li class="nav-item" wire:ignore>
                                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">ลูกหนี้</a>
                                </li>
                                <li class="nav-item" wire:ignore>
                                    <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">เจ้าหนี้</a>
                                </li>
                            </ul>
                            <!-- /.Tab Header -->

                            <!-- .Tab ข้อมูลทั่วไป -->
                            <div class="tab-content ml-2 mt-2" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" wire:ignore.self>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="customerID" style="color:red;">รหัส</label>
                                            <input class="form-control form-control-sm @error('customerid') is-invalid @enderror" maxlength="15" type="text" 
                                                id="customerID" required {{ $showEditModal ? 'readonly' : '' }} wire:model.defer="state.customerid">
                                            @error('customerid')
                                            <div class="invalid-feedback">
                                                The Customer ID has already been taken.
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col-md-8">
                                            <label for="name" style="color:red;">ชื่อ</label>
                                            <input class="form-control form-control-sm" type="text" id="name" maxlength="100" required wire:model.defer="state.name">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="names" style="color:red;">ชื่อ2</label>
                                            <input class="form-control form-control-sm" type="text" id="names" maxlength="100" required wire:model.defer="state.names">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="name1" style="color:red;">ชื่อ3</label>
                                            <input class="form-control form-control-sm" type="text" id="name1" maxlength="100" required wire:model.defer="state.name1">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="taxID" style="color:red;">เลขประจำตัวภาษี</label>
                                            <input class="form-control form-control-sm" type="text" id="taxID" maxlength="30" required wire:model.defer="state.taxid">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="branchNo">รหัสสาขา</label>
                                            <input class="form-control form-control-sm" type="text" maxlength="10" id="branchNo" wire:model.defer="state.branchno">
                                        </div>
                                        <div class="col-md-5">
                                            <label for="groupCheckbox">สถานะ</label>
                                            <div class="form-control form-control-sm" id="groupCheckbox">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="debtor" wire:model.defer="state.debtor">
                                                    <label class="form-check-label" for="debtor">ลูกหนี้</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="creditor" wire:model.defer="state.creditor">
                                                    <label class="form-check-label" for="creditor">เจ้าหนี้</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="corporate" wire:model.defer="state.corporate">
                                                    <label class="form-check-label" for="corporate">นิติบุคคล</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="address11" style="color:red;">ที่อยู่บรรทัดที่ 1</label>
                                            <input class="form-control form-control-sm" type="text" id="address11" maxlength="100" required wire:model.defer="state.address11">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="address12">ที่อยู่บรรทัดที่ 2</label>
                                            <input class="form-control form-control-sm" type="text" id="address12" maxlength="100" wire:model.defer="state.address12">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="city1">อำเภอ</label>
                                            <x-select2 id="city1-select2" wire:model.defer="state.city1">
                                                <option value=" ">---โปรดเลือก---</option>
                                                @foreach($citys_dd as $row)
                                                <option value='{{ $row->city }}'>
                                                    {{ $row->city }}
                                                </option>
                                                @endforeach
                                            </x-select2>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="state1">จังหวัด</label>
                                            <x-select2 id="state1-select2" wire:model.defer="state.state1">
                                                <option value=" ">---โปรดเลือก---</option>
                                                @foreach($states_dd as $row)
                                                <option value='{{ $row->state }}'>
                                                    {{ $row->state }}
                                                </option>
                                                @endforeach
                                            </x-select2>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="zipcode1">รหัสไปรษณีย์</label>
                                            <input class="form-control form-control-sm" type="text" id="zipcode1" maxlength="10" wire:model.defer="state.zipcode1">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="phone1">โทรศัพท์</label>
                                            <input class="form-control form-control-sm" type="text" id="phone1" maxlength="50" wire:model.defer="state.phone1">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="fax1">โทรสาร</label>
                                            <input class="form-control form-control-sm" type="text" id="fax1" maxlength="50" wire:model.defer="state.fax1">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="email1">Email</label>
                                            <input class="form-control form-control-sm" type="text" id="email1" maxlength="50" wire:model.defer="state.email1">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="contact1">ผู้ติดต่อ</label>
                                            <input class="form-control form-control-sm" type="text" id="contact1" maxlength="100" wire:model.defer="state.contact1">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="notes1">บันทึก</label>
                                            <input class="form-control form-control-sm" type="text" id="notes1" maxlength="100" wire:model.defer="state.notes1"></input>
                                        </div>
                                    </div>

                                </div>
                                <!-- /.Tab ข้อมูลทั่วไป -->

                                <!-- .Tab ลูกหนี้ -->
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" wire:ignore.self>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="creditLimit">วงเงินเครดิต</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="creditLimit" wire:model.defer="state.creditlimit">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="discountDay">ชำระภายใน (วัน)</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="discountDay" wire:model.defer="state.discountday">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="discount">ได้รับส่วนลด %</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="discount" wire:model.defer="state.discount">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="dueDay">จำนวนวันที่จะต้องชำระ</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="dueDay" wire:model.defer="state.dueday">
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-md-3">
                                            <label for="generalDiscount">ส่วนลดทั่วไป %</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="generalDiscount" wire:model.defer="state.generaldiscount">
                                        </div>
                                        <div class="col-md-9">
                                            <label for="termDiscount">เงิ่อนไขชำระเงิน</label>
                                            <input class="form-control form-control-sm" type="text" id="termDiscount" maxlength="50" wire:model.defer="state.termdiscount">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="account">บัญชีลูกหนี้</label>
                                            <x-select2 id="account-select2" wire:model.defer="state.account">
                                                <option value=" ">---โปรดเลือก---</option>
                                                @foreach($accountNos_dd as $row)
                                                <option value='{{ $row->account }}'>
                                                    {{ $row->account . ': ' . $row->accname }}
                                                </option>
                                                @endforeach
                                            </x-select2>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tax">รหัสภาษี</label>
                                            <select class="form-control form-control-sm" id="tax" wire:model.defer="state.tax">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($taxs_dd as $tax_dd)
                                                <option value="{{ $tax_dd->code }}">
                                                    {{ $tax_dd->code }} : {{ $tax_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="tax1">ภาษีหัก ณ ที่จ่าย</label>
                                            <select class="form-control form-control-sm" id="tax1" wire:model.defer="state.tax1">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($taxs1_dd as $tax1_dd)
                                                <option value="{{ $tax1_dd->code }}">
                                                    {{ $tax1_dd->code }} : {{ $tax1_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="priceLevel">ระดับราคา</label>
                                            <select class="form-control form-control-sm" id="priceLevel" wire:model.defer="state.pricelevel">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($priceLevels_dd as $priceLevel_dd)
                                                <option value="{{ $priceLevel_dd->code }}">
                                                    {{ $priceLevel_dd->code }} :
                                                    {{ $priceLevel_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.Tab ลูกหนี้ -->

                                <!-- .Tab เจ้าหนี้ -->
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" wire:ignore.self>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="creditLimit_ap">วงเงินเครดิต</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="creditLimit_ap" wire:model.defer="state.creditlimit_ap">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="discountDay">ชำระภายใน (วัน)</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="discountDay_ap" wire:model.defer="state.discountday_ap">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="discount_ap">ได้รับส่วนลด %</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="discount_ap" wire:model.defer="state.discount_ap">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="dueDay_ap">จำนวนวันที่จะต้องชำระ</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="dueDay_ap" wire:model.defer="state.dueday_ap">
                                        </div>
                                    </div>
                                    <div class="row">                                        
                                        <div class="col-md-3">
                                            <label for="generalDiscount_ap">ส่วนลดทั่วไป %</label>
                                            <input class="form-control form-control-sm" type="number" step="0.01" id="generalDiscount_ap" wire:model.defer="state.generaldiscount_ap">
                                        </div>
                                        <div class="col-md-9">
                                            <label for="termDiscount_ap">เงิ่อนไขชำระเงิน</label>
                                            <input class="form-control form-control-sm" type="text" id="termDiscount_ap" maxlength="50" wire:model.defer="state.termdiscount_ap">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="account">บัญชีเจ้าหนี้</label>
                                            <x-select2 id="account_ap-select2" wire:model.defer="state.account_ap">
                                                <option value=" ">---โปรดเลือก---</option>
                                                @foreach($accountNos_dd as $row)
                                                <option value='{{ $row->account }}'>
                                                    {{ $row->account . ': ' . $row->accname }}
                                                </option>
                                                @endforeach
                                            </x-select2>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="tax">รหัสภาษี</label>
                                            <select class="form-control form-control-sm" id="tax_ap" wire:model.defer="state.tax_ap">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($taxs_dd as $tax_dd)
                                                <option value="{{ $tax_dd->code }}">
                                                    {{ $tax_dd->code }} : {{ $tax_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="tax1">ภาษีหัก ณ ที่จ่าย</label>
                                            <select class="form-control form-control-sm" id="tax1_ap" wire:model.defer="state.tax1_ap">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($taxs1Ap_dd as $tax1Ap_dd)
                                                <option value="{{ $tax1Ap_dd->code }}">
                                                    {{ $tax1Ap_dd->code }} : {{ $tax1Ap_dd->description }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="groupCheckbox2">อื่น ๆ</label>
                                            <div class="form-control form-control-sm" id="groupCheckbox2">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="discountonTotal" wire:model.defer="state.discountontotal_ap">
                                                    <label class="form-check-label" for="discountonTotal">ส่วนลดจากยอดรวมภาษี</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="exclusiveTax" wire:model.defer="state.exclusivetax_ap">
                                                    <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.Tab เจ้าหนี้ -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                </div>
            </div>
        </form>
    </div>
</div>

@push('styles')

{{-- label{
    margin-bottom: 0px;
}

.row{
    margin-bottom: 0px;
} --}}

@endpush

@push('js')
<script>
    window.addEventListener('show-customerForm', event => {
    $('#customerForm').modal('show');
    })

    window.addEventListener('hide-customerForm', event => {
    $('#customerForm').modal('hide');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('account-select2');
        clearSelect2('city1-select2');
        clearSelect2('state1-select2');
        clearSelect2('account_ap-select2');
    })
</script>
@endpush