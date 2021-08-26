<div>
    <!-- <x-loading-indicator /> -->

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Accounting</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">AccStar</a></li>
                        <li class="breadcrumb-item active">Accounting</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <label>เลขที่ใบสำคัญ:</label>
                    <select class="form-control" wire:change="getGltran" wire:model="gltranNo">
                        <option value="">เลือก</option>
                        @foreach ($gltrans as $gltran)
                        <option value="{{ $gltran->gltran }}">{{ $gltran->gltran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label>สมุดรายวัน:</label>
                    <select class="form-control">
                        <option value="">เลือก</option>
                        @foreach ($journals as $journal)
                        <option value="{{ $journal->code }}">{{ $journal->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label>วันที่ใบสำคัญ:</label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker id="appointmentDate" :error="'date'" />
                    </div>
                </div>
                <div class="col">
                    <label>ชนิดการจัดสรร:</label>
                    <select class="form-control">
                        <option value="">เลือก</option>
                        @foreach ($allocations as $allocation)
                        <option value="{{ $allocation->code }}">{{ $allocation->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- Grid -->
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">บัญชี</th>
                                <th scope="col">รายละเอียด</th>
                                <th scope="col">เดบิต</th>
                                <th scope="col">เครดิต</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gltran_details as $index => $gltran_detail)
                            <tr>
                                <th scope="row">{{ $loop->iteration }}</th>
                                <td>
                                    <select class="form-control" wire:model.defer="gltran_details.{{$index}}.glaccount"
                                        wire:change="">
                                        @foreach($accountNos as $accountNo)
                                            <option value="{{ $accountNo->account }}">{{ $accountNo->account }}: {{ $accountNo->accname }} 
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" 
                                            wire:model.defer="gltran_details.{{$index}}.gldescription" />
                                  
                                </td>
                                <td>
                                    <input type="number" class="form-control" style="text-align: right;"
                                            wire:model.lazy="gltran_details.{{$index}}.gldebit">
                                </td>
                                <td>
                                    <input type="number" class="form-control" style="text-align: right;"
                                            wire:model.lazy="gltran_details.{{$index}}.glcredit">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        @if($gltranNo != "")
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <input wire:model="sumGldebit" class="form-control" type="text" 
                                        style="text-align: right;" readonly>
                                    </td>
                                    <td>
                                        <input wire:model="sumGlcredit" class="form-control" type="text" 
                                        style="text-align: right;" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            <!-- /Grid -->
        </div>
    </div>
</div>