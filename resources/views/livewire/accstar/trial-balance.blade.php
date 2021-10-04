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
                    <h1 class="m-0 text-dark">งบทดลอง</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">งบทดลอง</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .แสดงรายการใบสำคัญรับ -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-end mb-2">
                        <div class="d-flex justify-content-center align-items-center border bg-while pr-0 pl-0">
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
                    <table class="table table-hover w-100 small">
                    
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">เลขที่บัญชี
                                    <a href="" wire:click.prevent="sortBy('account')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ชื่อบัญชี
                                    <a href="" wire:click.prevent="sortBy('accnameother')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ยกมาเดบิต
                                    <a href="" wire:click.prevent="sortBy('begindebit')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>                                    
                                <th scope="col">ยกมาเครดิต
                                    <a href="" wire:click.prevent="sortBy('begincredit')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">เดบิต
                                    <a href="" wire:click.prevent="sortBy('currentdebit')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">เครดิต
                                    <a href="" wire:click.prevent="sortBy('currentcredit')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ยอดปัจจุบัน-Dr
                                    <a href="" wire:click.prevent="sortBy('nowdr')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ยอดปัจจุบัน-Cr
                                    <a href="" wire:click.prevent="sortBy('nowcr')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ยอดปัจจุบัน
                                    <a href="" wire:click.prevent="sortBy('currentbal')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trialBalance as $item)
                            <tr style="text-align: right">
                                <td scope="col" style="text-align: center">{{ $loop->iteration }}</td>
                                <td scope="col" style="text-align: left">{{ $item['account'] }} </td>
                                <td scope="col" style="text-align: left">{{ $item['accnameother'] }} </td>
                                <td scope="col">{{ number_format($item['begindebit'],2) }} </td>
                                <td scope="col">{{ number_format($item['begincredit'],2) }} </td>
                                <td scope="col">{{ number_format($item['currentdebit'],2) }} </td>
                                <td scope="col">{{ number_format($item['currentcredit'],2) }} </td>
                                <td scope="col">{{ number_format($item['nowdr'],2) }} </td>
                                <td scope="col">{{ number_format($item['nowcr'],2) }} </td>
                                <td scope="col">{{ number_format($item['currentbal'],2) }} </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="text-align: right; font-weight: bold;">
                                <td scope="col"></td>
                                <td scope="col"></td>
                                <td scope="col"></td>
                                <td scope="col">{{ number_format($sumBeginDebit,2) }}</td>
                                <td scope="col">{{ number_format($sumBeginCredit,2) }}</td>
                                <td scope="col">{{ number_format($sumCurrentDebit,2) }}</td>
                                <td scope="col">{{ number_format($sumCurrentCredit,2) }}</td>
                                <td scope="col"></td>
                                <td scope="col"></td>
                                <td scope="col"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>           
        </div>
    </div>
    <!-- /.แสดงรายการใบสำคัญรับ -->
</div>
