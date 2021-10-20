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
                    <h1 class="m-0 text-dark">ยกเลิกใบสั่งขาย</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">ระบบขาย</li>
                        <li class="breadcrumb-item">ยกเลิก</li>
                        <li class="breadcrumb-item active">ใบสั่งขาย</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <button type="button" class="btn btn-danger" {{ $btnDelete ? '' : 'disabled' }} wire:click="confirmDelete">
                        <i class="fas fa-trash-alt mr-1"></i>ลบใบสั่งขาย</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" wire:click="pressCancel">
                        <i class="fa fa-times mr-1"></i>ยกเลิก</button>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <label class="">เลขที่ใบสั่งขาย</label>                    
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="sNumber"
                            placeholder="ค้นหาเลขที่ใบสั่งขาย">
                        <div class="input-group-append">
                          <button class="btn btn-primary form-control-sm" type="button" wire:click.prevent="searchSO('{{ $sNumber }}')">
                            <i class="fas fa-search"></i>
                          </button>
                        </div>
                      </div>
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
                    <label class="">ใช้ได้จนถึง</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="soHeader.expirydate" id="expiryDate" :error="'date'" disabled />
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
                        <x-datepicker wire:model.defer="soHeader.deliverydate" id="deliverydate" :error="'date'"
                        disabled />
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <label class="">วันที่ครบกำหนดชำระ</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="soHeader.duedate" id="duedatedate" :error="'date'" disabled />
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
                    <input type="text" class="form-control form-control-sm mb-1" readonly
                        wire:model.defer="soHeader.shipname">
                </div>
                <div class="col">
                    <label class="">ที่อยู่</label>
                    <textarea class="form-control form-control-sm mb-1" rows="2" readonly
                        wire:model.defer="soHeader.full_address"></textarea>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" wire:model.defer="soHeader.exclusivetax" disabled
                            wire:change="checkExclusiveTax">
                        <label class="form-check-label" for="exclusiveTax">ราคาไม่รวมภาษี</label>
                    </div>
                </div>
            </div>

            <!-- .Grid -->
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-striped myGridTB" id="table">
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
                                    <input type="text" class="form-control form-control-sm" readonly
                                        wire:model.defer="soDetails.{{$index}}.itemid">
                                </td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" readonly
                                        wire:model.defer="soDetails.{{$index}}.description">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.quantity">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        style="text-align: right;" wire:model.lazy="soDetails.{{$index}}.unitprice">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        readonly style="text-align: right;" wire:model="soDetails.{{$index}}.amount">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        style="text-align: right;" wire:model="soDetails.{{$index}}.discountamount">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        style="text-align: right;" wire:model="soDetails.{{$index}}.taxrate">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        style="text-align: right;" wire:model="soDetails.{{$index}}.taxamount">
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control form-control-sm" readonly
                                        style="text-align: right;" wire:model="soDetails.{{$index}}.netamount">
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
    </div>
</div>