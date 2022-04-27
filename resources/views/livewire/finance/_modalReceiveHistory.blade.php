<div class="modal" id="receiveHistoryForm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form autocomplete="off" wire:submit.prevent="createUpdateReceiveOnSales">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        ใบสำคัญรับเงิน
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn  btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Close</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-1 form-group">
                            <label class=" mr-1">ชื่อ:</label>
                                <input type="text" class="form-control form-control-sm" readonly
                                    wire:model.defer="bankHeader.customername">
                        </div>
                        <div class="col-6 mb-1 form-group">
                            <label class=" mr-1">ภาษีถูกหัก:</label>
                            <div class="form-inline d-flex justify-content-between">
                                <input type="text" class="form-control form-control-sm" readonly
                                    wire:model.defer="bankHeader.taxscheme">
                                <input type="number" step="0.01" class="form-control form-control-sm text-right" readonly
                                    style="width: 30%;" wire:model.defer="bankHeader.witholdamt">
                                <input type="number" step="0.01" class="form-control form-control-sm text-right" readonly
                                    style="width: 30%;" wire:model.defer="bankHeader.witholdtax">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-1 form-group">
                            <label class="mr-1">เลขที่:</label>
                            <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly
                                wire:model.defer="bankHeader.gltran">
                        </div>
                        <div class="col mb-1 form-group">
                            <label class=" mr-1">วันที่:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="bankHeader.gjournaldt" id="่jrDate" :error="'date'"
                                readonly />
                            </div>
                        </div>
                        <div class="col-6 mb-1 form-group">
                            <label class=" mr-1">ภาษีถูกหัก-1:</label>
                            <div class="form-inline d-flex justify-content-between">
                                <input type="text" class="form-control form-control-sm" readonly
                                    wire:model.defer="bankHeader.taxscheme1">
                                <input type="number" step="0.01" class="form-control form-control-sm text-right" readonly
                                    style="width: 30%;" wire:model.lazy="bankHeader.witholdamt1">
                                <input type="number" step="0.01" class="form-control form-control-sm text-right" readonly
                                    style="width: 30%;" wire:model.defer="bankHeader.witholdtax1">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3 mb-1 form-group">
                            <label class=" mr-1">เลขอ้างอิง:</label>
                            <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly
                                wire:model.defer="bankHeader.documentref">
                        </div>
                        <div class="col-3 mb-1 form-group"">
                                    <label class=" mr-1">เลขภาษีหัก:</label>
                            <input type="text" class="form-control form-control-sm mb-1 mr-1" readonly
                                wire:model.defer="bankHeader.taxrunningno">
                        </div>
                        <div class="col-6">
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
                                        <th scope="col" style="text-align: right; width: 15%;"></th>
                                        <th scope="col" style="text-align: right; width: 15%;">ยอดเงิน</th>
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
                                        <td>
                                        </td>
                                        <td class="align-middle" style="text-align: right;">
                                            {{ number_format($bankDetails[$index]['balance'],2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td></td>
                                        <td class="align-middle">รวม</td>
                                        <td class="align-middle"></td>
                                        <td class="align-middle">{{ number_format($sumBalance,2) }}</td>
                                    <tr>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td></td>
                                        <td class="align-middle" style="color: red;">หัก ณ ที่จ่าย</td>
                                        <td class="align-middle"></td>
                                        <td class="align-middle"><span style="color: red; font-weight: bold; text-align: right;">{{
                                                number_format($sumWitholdTax,2) }}</td>
                                    <tr>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td></td>
                                        <td class="align-middle">เพิ่ม/<span style="color: red;">หัก</span></td>
                                        <td class="align-middle">{{ number_format($sumPlus,2) }}</td>
                                        <td class="align-middle"><span style="color: red;">{{
                                                number_format($sumDeduct,2) }}</td>
                                    <tr>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td></td>
                                        <td class="align-middle">รับสุทธิ</td>
                                        <td></td>
                                        <td class="align-middle">{{ number_format($netAmount,2) }}</td>
                                    <tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- /.Grid -->
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
    window.addEventListener('show-receiveHistoryForm', event => {
            $('#receiveHistoryForm').modal('show');
        })

        window.addEventListener('hide-receiveHistoryForm', event => {
            $('#receiveHistoryForm').modal('hide');
        })
</script>

@endpush