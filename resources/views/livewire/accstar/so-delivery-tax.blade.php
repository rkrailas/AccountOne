<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- .ปุ่มซ่อนเมนู -->
                    <div class="float-left d-none d-sm-inline">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                                class="fas fa-bars"></i></a>
                    </div>
                    <!-- /.ปุ่มซ่อนเมนู -->
                    <h1 class="m-0 text-dark">ส่งสินค้าพร้อมใบกำกับ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ส่งสินค้าพร้อมใบกำกับ</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .List Sales Order -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle"
                                mr-1></i>
                            สร้างข้อมูลใหม่</button>
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-2">
                            <input wire:model.lazy="searchTerm" type="text" class="form-control border-0"
                                placeholder="Search"> <!-- lazy=Lost Focus ถึงจะ Postback  -->
                            <div wire:loading.delay wire:target="searchTerm">
                                <div class="la-ball-clip-rotate la-dark la-sm">
                                    <div></div>
                                </div>
                            </div>
                        </div>
                        <x-search-input />
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">เลขที่ใบสั่งขาย</th>
                                <th scope="col">วันที่ใบสั่งขาย</th>
                                <th scope="col">ผู้ซื้อ</th>
                                <th scope="col">ยอดเงิน</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salesOrders as $salesOrder)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $salesOrders->firstitem()-1  }}</td>
                                <td scope="col">{{ $salesOrder->snumber }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($salesOrder->sodate)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $salesOrder->name }} </td>
                                <td scope="col">{{ number_format($salesOrder->sototal,2) }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $salesOrder->snumber }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                    <a href="" wire:click.prevent="confirmDelete('{{ $salesOrder->snumber }}')">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-between">
                        {{ $salesOrders->links() }} จำนวน {{ number_format($salesOrders->Total(),0) }} รายการ
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.List Sales Order -->

    <!-- .Model Form Add/Edit -->
    <div class="modal fade bd-example-modal-xl" id="soDeliveryTaxForm" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
        <div class="modal-dialog" style="max-width: 95%;">
            <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            @if($showEditModal)
                            แก้ไขใบสั่งขาย
                            @else
                            สร้างใบสั่งขาย
                            @endif
                        </h5>
                        <div class="float-right">
                            <button type="button" class="btn btn-secondary" wire:click.prevent="generateGl">
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
                        <div class="row mb-0">
                            <div class="col">
                                <label class="mb-0">เลขที่ใบสั่งขาย:</label>
                                <input type="text" class="form-control mb-1" required readonly
                                    wire:model.defer="soHeader.snumber">
                            </div>
                            <div class="col">
                                <label class="mb-0">วันที่ใบสั่งขาย:</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'"
                                        required />
                                </div>
                            </div>
                            <div class="col">
                                <label class="mb-0">เลขที่ใบสำคัญ:</label>
                                <input type="text" class="form-control mb-1" wire:model.defer="soHeader.invoiceno">
                            </div>
                            <div class="col">
                                <label class="mb-0">วันที่ใบสำคัญ:</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="soHeader.sodate" id="soDate" :error="'date'"
                                        required />
                                </div>
                            </div>
                        </div>
                        <div class="row mb-0">
                            <div class="col">
                                <label class="mb-0">เลขที่ใบกำกับ:</label>
                                <input type="text" class="form-control mb-1" required
                                    wire:model.defer="soHeader.deliveryno">
                            </div>
                            <div class="col">
                                <label class="mb-0">วันที่ใบกำกับ:</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="soHeader.deliverydate" id="deliveryDate"
                                        :error="'date'" required />
                                </div>
                            </div>
                            <div class="col">
                                <label class="mb-0">วันที่ครบกำหนด:</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="soHeader.duedate" id="dueDate" :error="'date'"
                                        required />
                                </div>
                            </div>
                            <div class="col">
                                <label class="mb-0">การชำระเงิน:</label>
                                <select class="form-control mb-1" wire:model.defer="soHeader.payby">
                                    <option value="0">ยังไม่ชำระ</option>
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
                            <div class="col-4">
                                <label class="mb-0">ชื่อ:</label>
                                <input type="text" class="form-control mb-1" readonly required
                                    wire:model.defer="soHeader.shipname">
                            </div>
                            <div class="col">
                                <label class="mb-0">ที่อยู่:</label>
                                <textarea class="form-control mb-1" rows="2"
                                    wire:model.defer="soHeader.full_address"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                        wire:model.defer="soHeader.exclusivetax" wire:change="checkExclusiveTax">
                                    <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                        wire:model.defer="soHeader.taxontotal" wire:change="checkTaxOnTotal">
                                    <label class="form-check-label" for="taxOnTotal">ภาษีจากยอดรวม</label>
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
                                            <th scope="col">บัญชีขาย</th>
                                            <th scope="col" style="width: 7%;">จำนวน</th>
                                            <th scope="col">ต่อหน่วย</th>
                                            <th scope="col">รวม</th>
                                            <th scope="col">ส่วนลด</th>
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
                                                <select class="form-control " required
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
                                                <select class="form-control"
                                                    wire:model.lazy="soDetails.{{$index}}.salesac">
                                                    <option value="">--- โปรดเลือก ---</option>
                                                    @foreach($salesAcs_dd as $salesAc_dd)
                                                    <option value="{{ $salesAc_dd->account }}">
                                                        {{ $salesAc_dd->account }}:
                                                        {{ $salesAc_dd->accnameother }}
                                                    </option>
                                                    @endforeach
                                                </select>
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
                                                    wire:model="soDetails.{{$index}}.netamount">
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
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" readonly
                                                    style="text-align: right;" wire:model="sumQuantity">
                                            </td>
                                            <td></td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" readonly
                                                    style="text-align: right;" wire:model="sumAmount">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" readonly
                                                    style="text-align: right;" wire:model="sumDiscountAmount">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control" readonly
                                                    style="text-align: right;" wire:model="sumNetAmount">
                                            </td>
                                            <td></td>
                                        <tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label class="mb-0">ส่วนลด:</label>
                                <input type="number" step="0.01" class="form-control" required
                                    style="text-align: right;" wire:model.defer="soHeader.discountamount">
                            </div>
                            <div class="col">
                                <label class="mb-0">ค่าขนส่ง:</label>
                                <input type="number" step="0.01" class="form-control" required
                                    style="text-align: right;">
                            </div>
                            <div class="col">
                                <label class="mb-0">อัตราภาษี:</label>
                                <select class="form-control" wire:model.lazy="soHeader.taxrate">
                                    <option value="">--- โปรดเลือก ---</option>
                                    @foreach($taxRates_dd as $taxRate_dd)
                                    <option value="{{ $taxRate_dd->taxrate }}">
                                        {{ $taxRate_dd->code }}
                                        ({{ number_format($taxRate_dd->taxrate,2) }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label class="mb-0">ภาษีขาย:</label>
                                <input type="number" step="0.01" class="form-control" required
                                    style="text-align: right;" wire:model.defer="soHeader.salestax">
                            </div>
                            <div class="col">
                                <label class="mb-0">ยอดสุทธิ:</label>
                                <input type="number" step="0.01" class="form-control" required
                                    style="text-align: right;" wire:model.defer="soHeader.sototal">
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

    <!-- Test -->
    <div class="modal" id="myModal2" data-backdrop="static">
        <div class="modal-dialog" style="max-width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">การบันทึกบัญชี</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="container"></div>
                <div class="modal-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">รหัสบัญชี</th>
                                <th scope="col">ชื่อบัญชี</th>
                                <th scope="col">เดบิต</th>
                                <th scope="col">เครดิต</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($genGLs != Null)
                                @foreach ($genGLs as $genGL)
                                <tr>
                                    <td scope="col">{{ $loop->iteration  }}</td>
                                    <td scope="col">{{ $genGL['glaccount'] }}</td>
                                    <td scope="col">{{ $genGL['glaccname'] }}</td>
                                    <td scope="col">{{ $genGL['gldebit'] }}</td>
                                    <td scope="col">{{ $genGL['glcredit'] }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">Close</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Test -->
    <!-- /.Model Form Add/Edit -->

    @include('livewire.accstar._mypopup')
    @include('livewire.accstar._mycss')
</div>