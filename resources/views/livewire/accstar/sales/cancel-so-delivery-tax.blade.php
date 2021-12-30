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
                    <h1 class="m-0 text-dark">ยกเลิกส่งสินค้าพร้อมใบกำกับ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">ระบบขาย</li>
                        <li class="breadcrumb-item">ยกเลิก</li>
                        <li class="breadcrumb-item active">ยกเลิกส่งสินค้าพร้อมใบกำกับ</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <button type="button" class="btn btn-sm btn-danger" {{ $btnDelete ? '' : 'disabled' }} 
                        wire:click="confirmDelete">
                        <i class="fas fa-trash-alt mr-1"></i>ยกเลิกเอกสาร</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" wire:click="pressCancel">
                        <i class="fa fa-times mr-1"></i>ยกเลิก</button>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col">
                    <label class="">เลขที่ใบกำกับภาษี</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm mb-1" placeholder="ค้นหา"
                            wire:model.defer="deleteNumber" wire:keydown.enter="searchDoc('{{ $deleteNumber }}')">
                        <div class="input-group-append">
                          <button class="btn btn-primary form-control-sm" type="button" wire:click.prevent="searchDoc('{{ $deleteNumber }}')">
                            <i class="fas fa-search"></i>
                          </button>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <label class="">วันที่ใบกำกับภาษี</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="soHeader.taxdate" id="taxdate" :error="'date'"
                        disabled />
                    </div>
                </div>
                <div class="col">
                    <label class="">เลขที่ใบสั่งขาย</label>
                    <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.snumber" readonly>
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
            </div>

            <div class="row mb-2">
                <div class="col">
                    <label class="">เลขที่ใบสำคัญ</label>
                    <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="soHeader.gltran" readonly>
                </div>
                <div class="col">
                    <label class="">วันที่สำคัญ</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="soHeader.journaldate" id="journaldate" :error="'date'"
                        disabled />
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
    </div>
</div>
