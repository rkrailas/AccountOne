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
                    <h1 class="m-0 text-dark">ใบสำคัญ</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ใบสำคัญ</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- /.List Journal -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle"
                                mr-1></i>
                            สร้างใบสำคัญ</button>
                        <div clasee="btn-group">
                            <button wire:click="filterJournalByBook" type="button"
                                class="btn {{ is_null($book) ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ทั้งหมด</span>
                            </button>
                            <button wire:click="filterJournalByBook('GL')" type="button"
                                class="btn {{ $book=='GL' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ทั่วไป</span>
                            </button>
                            <button wire:click="filterJournalByBook('PO')" type="button"
                                class="btn {{ $book=='PO' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ซื้อ</span>
                            </button>
                            <button wire:click="filterJournalByBook('SO')" type="button"
                                class="btn {{ $book=='SO' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">ขาย</span>
                            </button>
                            <button wire:click="filterJournalByBook('JP')" type="button"
                                class="btn {{ $book=='JP' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">จ่าย</span>
                            </button>
                            <button wire:click="filterJournalByBook('JR')" type="button"
                                class="btn {{ $book=='JR' ? 'btn-secondary' : 'btn-default'}}">
                                <span class="mr-1">รับ</span>
                            </button>
                        </div>
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-0 pl-0">
                            <input wire:model.lazy="searchTerm" type="text" class="form-control form-control-sm border-0"
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
                    <table class="table table-md table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">เลขที่ใบสำคัญ
                                    <span wire:click="sortBy('gltran.gltran')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'gltran.gltran' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'gltran.gltran' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">วันที่
                                    <span wire:click="sortBy('gltran.gjournaldt')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'gltran.gjournaldt' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'gltran.gjournaldt' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">คำอธิบาย
                                    <span wire:click="sortBy('gltran.gldescription')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'gltran.gldescription' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'gltran.gldescription' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">สมุดรายวัน
                                    <span wire:click="sortBy('misctable.other')" class="float-right text-sm" style="cursor: pointer;">
                                        <i class="fa fa-arrow-up {{ $sortBy === 'misctable.other' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                        <i class="fa fa-arrow-down {{ $sortBy === 'misctable.other' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                    </span>
                                </th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($gltrans) > 0)
                            @foreach ($gltrans as $gltran)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $gltrans->firstitem()-1  }}</td>
                                <td scope="col">{{ $gltran->gltran }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($gltran->gjournaldt)->format('Y-m-d') }} </td>
                                <td style="width:50%" scope="col">{{ $gltran->gldescription }} </td>
                                <td scope="col">{{ $gltran->other }} </td>
                                <td>
                                    <a href="" wire:click.prevent="edit('{{ $gltran->gltran }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                    <a href="" wire:click.prevent="confirmDelete('{{ $gltran->gltran }}')">
                                        <i class="fa fa-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $gltrans->links() }} <span
                        class="ml-2">จำนวน {{ number_format($gltrans->Total(),0) }} รายการ</span>
                    <div class="col">
                        <select class="form-control form-control-sm" style="width: 80px;" wire:model.lazy="numberOfPage">
                            <option value="10" selected>10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.List Journal -->

    @include('livewire.accstar.account._modelListGjournal')
</div>