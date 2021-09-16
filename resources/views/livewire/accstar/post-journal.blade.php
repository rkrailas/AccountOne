<div>
    <x-loading-indicator />
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
                    <h1 class="m-0 text-dark">ผ่านรายการบัญชี</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ผ่านรายการบัญชี</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="content">
        <div class="container">
            <div class="row mb-2">
                <div class="col">
                    <label class="mb-0">เลขที่ใบสำคัญ:</label>
                    <input type="text" class="form-control mb-1" wire:model.defer="journalNoFrom">
                </div>
                <div class="col">
                    <label class="mb-0">ถึง:</label>
                    <input type="text" class="form-control mb-1" wire:model.defer="journalNoTo">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label class="mb-0">วันที่ใบกำกับ:</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="journalDateFrom" wire:keydown.enter="changeDateFrom"
                            id="journalDateFrom" :error="'date'" required />
                    </div>
                </div>
                <div class="col">
                    <label class="mb-0">ถึง:</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="journalDateTo" id="journalDateTo" :error="'date'" required />
                    </div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <label class="mb-0">สมุดรายวัน:</label>
                    <select class="form-control mb-1" wire:model.defer="journalType">
                        <option value="" selected>ทั้งหมด</option>
                        @foreach ($journalTypes_dd as $item)
                        <option value="{{ $item->code }}" selected>{{ $item->other }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">

                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-primary" wire:click.prevent="postJournal">
                        ผ่านรายการบัญชี</button>
                </div>
            </div>
            <div class="row mt-5">
                <table class="table w-75">
                    <thead>
                        <tr>
                            <th scope="col">ผลการผ่านรายการ</th>
                            <th scope="col" class="w-20">จำนวนใบสำคัญ</th>
                            <th scope="col" class="w-50">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-success">
                            <td>ผ่านรายการแล้ว</td>
                            <td style="text-align: right; padding-right:50px;">
                                {{ number_format($totalPass,0) }}</td>
                            <td></td>
                        </tr>
                        <tr class="table-danger">
                            <td>ผ่านรายการไม่ได้</td>
                            <td style="text-align: right; padding-right:50px;">
                                {{ number_format($totalFailed,0) }}</td>
                            <td>
                                <ul>
                                    @foreach ($listFailed as $item)
                                    <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('livewire.accstar._mycss')
</div>