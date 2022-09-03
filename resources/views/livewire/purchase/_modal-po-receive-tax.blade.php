<div class="modal" id="poReceiveTaxForm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form autocomplete="off" wire:submit.prevent="createUpdatePO">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        รับสินค้าพร้อมใบกำกับ
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
                            <label class="">เลขที่ใบสั่งซื้อ:</label>
                            <input type="text" class="form-control form-control-sm mb-1 @error('ponumber') is-invalid @enderror"
                            {{ $showEditModal ? 'readonly' : '' }}
                            required wire:model.defer="poHeader.ponumber">
                            @error('ponumber')
                            <div class="invalid-feedback">
                                เลขที่เอกสารซ้ำ
                            </div>
                            @enderror
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ใบสั่งซื้อ:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="poHeader.podate" id="podate" :error="'date'"/>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="">คำอธิบายรายการ:</label>
                            <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="poHeader.gldescription">
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-3">
                            <label class="">เลขที่ใบกำกับ:</label>
                            <input type="text" class="form-control form-control-sm mb-1 {{ $errorTaxNumber ? 'is-invalid' : '' }}" 
                                required wire:model.defer="poHeader.taxref">
                        </div>
                        <div class="col-3">
                            <label class="">วันที่ใบกำกับ:</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="poHeader.paiddate" id="paiddate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col-3">
                            <label class="">เลขที่ใบสำคัญ:</label>
                            <input type="text" class="form-control form-control-sm mb-1 {{ $errorGLTran ? 'is-invalid' : '' }}" 
                                required wire:model.defer="poHeader.receiveno">
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
                                <x-datepicker wire:model.defer="poHeader.journaldate" id="journaldate" :error="'date'" required />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="">ชื่อ:</label>
                            <div>
                                <x-select2 id="customer-select2" wire:model.defer="poHeader.customerid" required="true">
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
                            <textarea class="form-control form-control-sm mb-1" rows="2" readonly wire:model.defer="poHeader.full_address"></textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="poHeader.exclusivetax" wire:change="checkExclusiveTax">
                                <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" {{ $showEditModal ? '' : 'disabled' }}
                                    wire:model.defer="poHeader.posted">
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
                                    @foreach($poDetails as $index => $poDetail)
                                    <tr>
                                        <td scope="row" class="align-middle text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือกสินค้า" readonly
                                                    wire:model.defer="poDetails.{{$index}}.itemid"
                                                    {{-- wire:keydown.enter="selectedItem('{{ $index }}','{{ $poDetails[$index]['itemid'] }}')" 1/12/64 ติดปัญหาส่งค่าตัวแปรกลับมาไม่ทัน--}}
                                                    >
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary form-control-sm" type="button" 
                                                        wire:click.prevent="showModalItem('{{$index}}')">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" wire:model.defer="poDetails.{{$index}}.description">
                                        </td>
                                        <td>                                            
                                            {{-- <div class="input-group">
                                                <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือก S/N" readonly
                                                    wire:model.defer="poDetails.{{$index}}.serialno">
                                                <div class="input-group-append">
                                                <button class="btn btn-primary form-control-sm" type="button" 
                                                    {{ $poDetails[$index]['stocktype'] == '4' ? '' : 'disabled'}}
                                                    wire:click.prevent="showModalSN('{{ $index }}')">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </button>
                                                </div>
                                            </div> --}}
                                            @if ($poDetails[$index]['stocktype'] == '4')                              
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือก S/N" readonly
                                                        wire:model.defer="poDetails.{{$index}}.serialno">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary form-control-sm" type="button" 
                                                            wire:click.prevent="showModalSN('{{ $index }}')">
                                                            <i class="fas fa-ellipsis-h"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @elseif ($poDetails[$index]['stocktype'] == '9')                              
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm mb-1" placeholder="เลือก Lot Number" readonly
                                                        wire:model.defer="poDetails.{{$index}}.lotnumber">
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
                                                {{ $poDetails[$index]['stocktype'] == '4' ? 'readonly' : ''}}
                                                wire:model.lazy="poDetails.{{$index}}.quantity">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                            wire:model.lazy="poDetails.{{$index}}.unitprice">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" readonly style="text-align: right;" 
                                            wire:model.defer="poDetails.{{$index}}.amount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                            wire:model.lazy="poDetails.{{$index}}.discountamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                            wire:model.lazy="poDetails.{{$index}}.taxrate">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                            wire:model.defer="poDetails.{{$index}}.taxamount">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                            wire:model.defer="poDetails.{{$index}}.netamount">
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
    window.addEventListener('show-poReceiveTaxForm', event => {
        $('#poReceiveTaxForm').modal('show');
    })

    window.addEventListener('hide-poReceiveTaxForm', event => {
        $('#poReceiveTaxForm').modal('hide');
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