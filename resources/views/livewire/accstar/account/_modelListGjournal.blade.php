<div class="modal fade bd-example-modal-xl" id="formJournal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-xl">
        <form autocomplete="off" wire:submit.prevent="createUpdateJournal">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        @if($showEditModal)
                        แก้ไขใบสำคัญ
                        @else
                        สร้างใบสำคัญ
                        @endif
                    </h5>
                    <div class="float-right">
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
                <div class="modal-body">
                    <div class="row">
                        <div class="col">
                            <label>เลขใบสำคัญ:</label>
                            <input type="text" class="form-control form-control-sm" required wire:model.defer="gltranNo2">
                        </div>
                        <div class="col">
                            <label>วันที่ใบสำคัญ:</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="gjournaldt2" id="gjournalDate" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col">
                            <label>สมุดรายวัน:</label>
                            <select class="form-control form-control-sm" required wire:model.defer="gjournal2">
                                <option value="">--- โปรดเลือก ---</option>
                                @foreach ($journals as $journal)
                                <option value="{{ $journal->code }}">{{ $journal->other }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <label>ชนิดการจัดสรร:</label>
                            <select class="form-control form-control-sm" wire:model.defer="department2">
                                <option value="">--- โปรดเลือก ---</option>
                                @foreach ($allocations as $allocation)
                                <option value="{{ $allocation->code }}">{{ $allocation->description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group row">
                                <label class="col-sm-1 col-form-label">คำอธิบาย:</label>
                                <div class="col-sm-11">
                                    <input type="text" class="form-control form-control-sm" wire:model.defer="gldescription2">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- .Grid -->
                    <div class="row">
                        <div class="col">
                            <table class="table table-striped myGridTB">
                                <thead>
                                    <tr>
                                        <th scope="col">
                                            <button class="btn btn-sm btn-primary" wire:click.prevent="addRow">+Add</button>
                                        </th>
                                        <th scope="col">บัญชี</th>
                                        <th scope="col">เดบิต</th>
                                        <th scope="col">เครดิต</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($journalDetails != null)
                                    @foreach($journalDetails as $index => $journalDetail)
                                    <tr>
                                        <td scope="row">
                                            <center>{{ $loop->iteration }}</center>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm" required wire:model.defer="journalDetails.{{$index}}.glaccount">
                                                <option value="">--- โปรดเลือก ---</option>
                                                @foreach($accountNos as $accountNo)
                                                <option value="{{ $accountNo->account }}">{{ $accountNo->account }}:
                                                    {{ $accountNo->accname }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" style="text-align: right;" wire:model.lazy="journalDetails.{{$index}}.gldebit">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" style="text-align: right;" wire:model.lazy="journalDetails.{{$index}}.glcredit">
                                        </td>
                                        <td>
                                            <center>
                                                <a href="" wire:click.prevent="removeRow({{ $index }})">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </center>

                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input wire:model="sumGldebit" class="form-control form-control-sm" type="number" style="text-align: right;" readonly>
                                        </td>
                                        <td>
                                            <input wire:model="sumGlcredit" class="form-control form-control-sm" type="number" style="text-align: right;" readonly>
                                        </td>
                                        <td>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
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

@push('js')

<script>
    window.addEventListener('show-formJournal', event => {
        $('#formJournal').modal('show');
    })

    window.addEventListener('hide-formJournal', event => {
        $('#formJournal').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })
</script>

@endpush