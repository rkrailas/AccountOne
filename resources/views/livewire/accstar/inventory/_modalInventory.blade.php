<div wire:ignore.self class="modal" id="inventoryForm" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 85%;">
        <form autocomplete="off" enctype="multipart/form-data"
            wire:submit.prevent="{{ $showEditModal ? 'updateInventory' : 'createInventory' }}">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        @if($showEditModal)
                        แก้ไขข้อมูลสินค้า
                        @else
                        สร้างสินค้าใหม่
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
                    <div class="container">
                        <div class="card card-primary mt-2">
                            <div class="card-header mt-0 mb-0">
                                <h3 class="card-title">ข้อมูลทั่วไป</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-2">
                                        <label>รหัสสินค้า</label>
                                        <input type="text" class="form-control form-control-sm @error('itemid') is-invalid @enderror"                                            
                                            {{ $showEditModal ? 'readonly' : '' }} wire:model.defer="product.itemid">
                                        @error('itemid')
                                        <div class="invalid-feedback">
                                            The Item ID has already been taken.
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="col-7">
                                        <label>รายละเอียดสินค้า</label>
                                        <input type="text" class="form-control form-control-sm"
                                            wire:model.defer="product.description">
                                    </div>
                                    <div class="col-3">
                                        <label>ชนิดสินค้า</label>
                                        <select class="form-control form-control-sm"
                                            wire:model.defer="product.stocktype">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($stocktype_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-3">
                                        <label>ประเภทสินค้า</label>
                                        <select class="form-control form-control-sm"
                                            wire:model.defer="product.category">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($category_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>กลุ่มสินค้า-1</label>
                                        <select class="form-control form-control-sm" wire:model.defer="product.brand">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($brand_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>กลุ่มสินค้า-2</label>
                                        <select class="form-control form-control-sm" wire:model.defer="product.model">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($model_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>สถานที่เก็บ</label>
                                        <select class="form-control form-control-sm"
                                            wire:model.defer="product.location">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($location_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-3">
                                        <label>หน่วยซื้อ</label>
                                        <select class="form-control form-control-sm"
                                            wire:model.defer="product.unitofmeasure">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($unitofmeasure_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>หน่วยขาย</label>
                                        <select class="form-control form-control-sm"
                                            wire:model.defer="product.unitofmeasures">
                                            <option value="">---โปรดเลือก---</option>
                                            @foreach ($unitofmeasure_dd as $row)
                                            <option value='{{ $row->code }}'>
                                                {{ $row->other }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>ทุนต่อหน่วย</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right"
                                            readonly wire:model.defer="product.averagecost">
                                    </div>
                                    <div class="col-3">
                                        <label>ราคาขาย</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right"
                                            wire:model.defer="product.salesprice">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">ข้อมูลบัญชี</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <label>บันทึกบัญชี</label>
                                        <x-select2 id="inventoryac-dropdown" wire:model.defer="product.inventoryac">
                                            <option value=" ">---โปรดเลือก---</option>
                                            @foreach($account_dd as $row)
                                            <option value='{{ $row->account }}'>
                                                {{ $row->account . ': ' . $row->accnameother }}
                                            </option>
                                            @endforeach
                                        </x-select2>
                                    </div>
                                    <div class="col-6">
                                        <label>บัญชีขาย</label>
                                        <x-select2 id="salesac-dropdown" wire:model.defer="product.salesac">
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
                                    <div class="col-6">
                                        <label>บัญชีส่งคืน-ซื้อ</label>
                                        <x-select2 id="purchasertac-dropdown" wire:model.defer="product.purchasertac">
                                            <option value=" ">---โปรดเลือก---</option>
                                            @foreach($account_dd as $row)
                                            <option value='{{ $row->account }}'>
                                                {{ $row->account . ': ' . $row->accnameother }}
                                            </option>
                                            @endforeach
                                        </x-select2>
                                    </div>
                                    <div class="col-6">
                                        <label>บัญชีส่งคืน-ขาย</label>
                                        <x-select2 id="salesrtac-dropdown" wire:model.defer="product.salesrtac">
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
                                    <div class="col-3">
                                        <label>วิธีคำนวณต้นทุน</label>
                                        <select class="form-control form-control-sm"
                                            wire:model.defer="product.costtype">
                                            <option value="">---โปรดเลือก---</option>
                                            <option value="0">Average</option>
                                            <option value="1">FIFO</option>
                                            <option value="2">LIFO</option>
                                            <option value="3">Standard</option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <label>ต้นทุนมาตราฐาน</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right"
                                            wire:model.defer="product.stdcost">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">อื่นๆ</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-3">
                                        <label>จำนวนขั้นต้ำ</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right"
                                            wire:model.defer="product.reorderlevel">
                                    </div>
                                    <div class="col-3">
                                        <label>จำนวนสั่ง</label>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-right"
                                            wire:model.defer="product.reorderqty">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-12">
                                        <label>บันทึก</label>
                                        <textarea class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">รูปภาพ</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <div class="custom-file">
                                            <input wire:model="photo" type="file" class="custom-file-input"
                                                id="customFile">
                                            <label class="custom-file-label" for="customFile">
                                                @if ($photo)
                                                {{ $photo->getClientOriginalName() }}
                                                @else
                                                Choose Image
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        @if ($photo)
                                        <div>
                                            <img src="{{ $photo->temporaryUrl() }}" style="width: 150px;" alt="">
                                        </div>
                                        @else
                                            @if(count($product) > 0 && $product['ram_inventory_image'])
                                            <img src="{{url('storage/inventory_images/'.$product['ram_inventory_image'])}}"
                                                style="width: 150px;">
                                            @endif
                                        @endif
                                        
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


@push('js')

<script>
    window.addEventListener('show-inventoryForm', event => {
        $('#inventoryForm').modal('show');
    })

    window.addEventListener('hide-inventoryForm', event => {
        $('#inventoryForm').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('inventoryac-dropdown');
        clearSelect2('salesac-dropdown');
        clearSelect2('purchasertac-dropdown');
        clearSelect2('salesrtac-dropdown');
    })

    window.addEventListener('bindToSelect', event => {
        $(event.detail.selectName).html(" ");
        $(event.detail.selectName).append(event.detail.newOption);
    })
</script>

@endpush