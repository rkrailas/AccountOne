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
                    <h1 class="m-0 text-dark">ยกเลิกรับชำระเงินค่าบริการ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">ระบบการเงิน</li>
                        <li class="breadcrumb-item">ยกเลิก</li>
                        <li class="breadcrumb-item active">ยกเลิกรับชำระเงินค่าบริการ</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <button type="button" class="btn btn-danger" {{ $btnDelete ? '' : 'disabled' }} 
                        wire:click="confirmDelete">
                        <i class="fas fa-trash-alt mr-1"></i>ยกเลิกเอกสาร</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" wire:click="pressCancel">
                        <i class="fa fa-times mr-1"></i>ยกเลิก</button>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-3 mb-1 form-group">
                    <label class="mr-1">เลขที่ใบสำคัญ:</label>
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm mb-1" wire:model.defer="deleteNumber"
                            wire:keydown.enter="searchDoc('{{ $deleteNumber }}')"
                            placeholder="ค้นหา">
                        <div class="input-group-append">
                          <button class="btn btn-primary form-control-sm" type="button" wire:click.prevent="searchDoc('{{ $deleteNumber }}')">
                            <i class="fas fa-search"></i>
                          </button>
                        </div>
                    </div>
                </div>
                <div class="col-3 mb-1 form-group">
                    <label class=" mr-1">วันที่:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="bankHeader.gjournaldt" id="่jrDate" :error="'date'" readonly />
                    </div>
                </div>
                <div class="col-6 mb-1 form-group">
                    <label class=" mr-1">ภาษีถูกหัก:</label>
                    <div class="form-inline d-flex justify-content-between">
                        <input type="text" class="form-control form-control-sm" readonly value=" {{ $bankHeader['taxscheme'] }} " >
                        <input type="text" class="form-control form-control-sm text-right" readonly style="width: 30%;" value={{ number_format($bankHeader['witholdamt'],2) }}>
                        <input type="text" class="form-control form-control-sm text-right" readonly style="width: 30%;" value={{ number_format($bankHeader['witholdtax'],2) }}>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6 mb-1 form-group">
                    <label class=" mr-1">ชื่อ:</label>
                    <input type="text" class="form-control form-control-sm" readonly wire:model.defer="bankHeader.customername">
                </div>
                <div class="col-6 mb-1 form-group">
                    <label class=" mr-1">ภาษีถูกหัก-1:</label>
                    <div class="form-inline d-flex justify-content-between">
                        <input type="text" class="form-control form-control-sm" readonly value=" {{ $bankHeader['taxscheme1'] }} " >
                        <input type="text" class="form-control form-control-sm text-right" readonly style="width: 30%;" value={{ number_format($bankHeader['witholdamt1'],2) }}>
                        <input type="text" class="form-control form-control-sm text-right" readonly style="width: 30%;" value={{ number_format($bankHeader['witholdtax1'],2) }}>
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col mb-1 form-group">
                    <label class=" mr-1">ชำระโดย:</label>
                    <input type="text" class="form-control form-control-sm" readonly wire:model.defer="bankHeader.payby">
                </div>
                <div class="col mb-1 form-group">
                    <label class=" mr-1">เลขอ้างอิง:</label>
                    <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly wire:model.defer="bankHeader.documentref">
                </div>
                <div class="col mb-1 form-group"">
                    <label class=" mr-1">เลขภาษีหัก:</label>
                    <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly wire:model.defer="bankHeader.taxrunningno">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col mb-1">
                    <label class=" mr-1">รับเข้าบัญชี:</label>
                    <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly wire:model.defer="bankHeader.account">
                </div>
                <div class="col mb-1">
                    <label class=" mr-1">บัญชีลูกหนี้:</label>
                    <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly wire:model.defer="bankHeader.accountcus">
                </div>
            </div>
            <!-- .Grid -->
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-striped myGridTB">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
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
                                    {{ number_format($bankDetails[$index]['balance'],2) }}
                                </td>
                                <td class="align-middle" style="text-align: right;">
                                    {{ number_format($bankDetails[$index]['amount'],2) }}
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
    </div>
</div>
