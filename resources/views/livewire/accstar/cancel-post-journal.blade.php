<div>
    <x-loading-indicator target="cancelPostJournal" />
    
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
                    <h1 class="m-0 text-dark">ยกเลิกผ่านรายการบัญชี</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ยกเลิกผ่านรายการบัญชี</li>
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
                    <input type="text" class="form-control" wire:model.defer="journalNoFrom">
                </div>
                <div class="col">
                    <label class="mb-0">ถึง:</label>
                    <input type="text" class="form-control" wire:model.defer="journalNoTo">
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
                        <x-datepicker wire:model.defer="journalDateFrom"
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
                    <label class="mb-0">วันที่ผ่านรายการ:</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="postDateFrom"
                            id="postDateFrom" :error="'date'" required />
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
                        <x-datepicker wire:model.defer="postDateTo" id="postDateTo" :error="'date'" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-primary" wire:click.prevent="cancelPostJournal">
                        ยกเลิกผ่านรายการบัญชี</button>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col">
                    @if ($countCancelPostPass)
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa fa-check-circle mr-1"></i> จำนวนรายการที่ยกเลิก Post ได้ {{ $countCancelPostPass }} รายการ
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>

    @include('livewire.accstar._mycss')
</div>
