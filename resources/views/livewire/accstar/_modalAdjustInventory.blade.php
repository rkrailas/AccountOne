<div wire:ignore.self class="modal" id="adjustInventoryForm" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 85%;">
        <form autocomplete="off" wire:submit.prevent="createAdjustInventory">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title font-weight-bold" id="exampleModalLabel">
                        ปรับปรุงสินค้า
                    </div>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="clearSelect2('itemid-select2')">
                            <i class="fa fa-times mr-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save mr-1"></i>
                            Save
                        </button>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="container">
                        <div class="card card-primary mt-2">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-4">
                                        <label>รหัสสินค้า</label>
                                        <div wire:ignore>
                                            <x-select2 id="itemid-select2" wire:model.defer="adjInventory.itemid">
                                                <option value=" ">---โปรดเลือก---</option>
                                                @foreach($inventorys_dd as $row)
                                                <option value='{{ $row->itemid }}'>
                                                    {{ $row->itemid . ': ' . $row->description }}
                                                </option>
                                                @endforeach
                                            </x-select2>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <label>รายละเอียดสินค้า</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="adjInventory.description" readonly>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-3">
                                        <label>ชนิดสินค้า</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="adjInventory.stocktypename" readonly>
                                    </div>
                                    <div class="col-3">
                                        <label>ประเภทสินค้า</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="adjInventory.category" readonly>
                                    </div>
                                    <div class="col-3">
                                        <label>คงเหลือ</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="adjInventory.instock" style="text-align: right;" readonly>
                                    </div>
                                    <div class="col-3">
                                        <label>มูลค่าคงเหลือ</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="adjInventory.instockvalue" style="text-align: right;" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-primary mt-2">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-4">
                                        <label>หมายเลขอ้างอิง</label>
                                        <input type="text"
                                            class="form-control form-control-sm @error('documentno') is-invalid @enderror" required
                                            wire:model.defer="adjInventory.documentno">
                                        @error('documentno')
                                        <div class="invalid-feedback">
                                            The Document No. has already been taken.
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="col-4">
                                        <label class="">วันที่ปรับปรุง</label>
                                        <div class="input-group mb-1">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-calendar"></i>
                                                </span>
                                            </div>
                                            <x-datepicker wire:model.defer="adjInventory.adjustdate" id="adjDate"
                                                :error="'date'" required/>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label>ประเภทปรับปรุง</label>
                                        <div class="form-check">
                                            <input class="form-check-input" name="adjustType" type="radio" value="in" required
                                                wire:model="adjustType">
                                            <label class="form-check-label">
                                                ปรับปรุง-เข้า
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" name="adjustType" type="radio" value="out" required
                                                wire:model="adjustType">
                                            <label class="form-check-label">
                                                ปรับปรุง-ออก
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-4">
                                        <label>จำนวน</label>
                                        <div class="row">
                                            <div class="col-8">
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    required style="text-align: right;"
                                                    wire:model.lazy="adjInventory.adjquantity">
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control form-control-sm" readonly
                                                    wire:model.defer="adjInventory.unitofmeasure">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label>ต้นทุน/หน่วย</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm" required id="adjvalue"
                                            {{ $adjustType  == 'out'? 'readonly' : '' }}
                                            style="text-align: right;" wire:model.lazy="adjInventory.adjvalue">
                                    </div>
                                    <div class="col-4">
                                        <label>บัญชีเจ้าหนี้/ลูกหนี้</label>
                                        <x-select2 id="account-select2" wire:model.defer="adjInventory.account">
                                            <option value=" ">---โปรดเลือก---</option>
                                            @foreach($account_dd as $row)
                                            <option value='{{ $row->account }}'>
                                                {{ $row->account . ': ' . $row->accnameother }}
                                            </option>
                                            @endforeach
                                        </x-select2>
                                    </div>
                                </div>

                                <div class="row mb-2">
                                    <div class="col-4">
                                        <label>สถานที่เก็บ</label>
                                        <select class="form-control form-control-sm" required
                                            wire:model.defer="adjInventory.location">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($location_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label>ทุนรวม</label>
                                        <input type="text" class="form-control form-control-sm" readonly
                                            style="text-align: right;" wire:model.defer="adjInventory.adjtotalvalue">
                                    </div>
                                </div>
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
<x-popup-alert></x-popup-alert>

@push('js')

<script>
    window.addEventListener('show-adjustInventoryForm', event => {
        $('#adjustInventoryForm').modal('show');
    });

    window.addEventListener('hide-adjustInventoryForm', event => {
        $('#adjustInventoryForm').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    });
</script>

@endpush