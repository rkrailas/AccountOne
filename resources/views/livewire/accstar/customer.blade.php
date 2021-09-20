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
                    <h3 class="m-0 text-dark">ข้อมูลลูกค้า</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">AccStar</li>
                        <li class="breadcrumb-item active">ข้อมูลลูกค้า</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- .List Customer -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <div class="d-flex justify-content-between mb-2">
                        <button wire:click.prevent="addNew" class="btn btn-primary"><i class="fa fa-plus-circle"
                                mr-1></i>
                            สร้างลูกค้า</button>
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
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">รหัส
                                    <a href="" wire:click.prevent="sortBy('customer.customerid')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ชื่อ
                                    <a href="" wire:click.prevent="sortBy('customer.name')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ผู้ติดต่อ
                                    <a href="" wire:click.prevent="sortBy('customer.contact1')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">โทรศัพท์
                                    <a href="" wire:click.prevent="sortBy('customer.phone1')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">เลขที่ผู้เสียภาษี
                                    <a href="" wire:click.prevent="sortBy('customer.taxid')">
                                        <i class="fa fa-sort" aria-hidden="true"></i>
                                    </a>
                                </th>
                                <th scope="col">ลูกหนี้</th>
                                <th scope="col">เจ้าหนี้</th>
                                <th scope="col">นิติบุคคล</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($customers) > 0)
                            @foreach ($customers as $customer)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $customers->firstitem()-1  }}</td>
                                <td scope="col">{{ $customer->customerid }} </td>
                                <td scope="col">{{ $customer->name }} </td>
                                <td scope="col">{{ $customer->contact1 }} </td>
                                <td scope="col">{{ $customer->phone1 }} </td>
                                <td scope="col">{{ $customer->taxid }} </td>
                                <td scope="col" style="text-align:center"> @if($customer->debtor ) <i
                                        class="fas fa-check"></i> @endif </td>
                                <td scope="col" style="text-align:center"> @if($customer->creditor ) <i
                                        class="fas fa-check"></i> @endif </td>
                                <td scope="col" style="text-align:center"> @if($customer->corporate ) <i
                                        class="fas fa-check"></i> @endif </td>
                                <td>
                                    <!-- <a href="{{ route('accstar.customer.form', $customer->customerid) }}">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a> -->
                                    <a href="" wire:click.prevent="edit('{{ $customer->customerid }}')">
                                        <i class="fa fa-edit mr-2"></i>
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $customers->links() }} <span
                        class="ml-2">จำนวน {{ number_format($customers->Total(),0) }} รายการ</span>
                    <div class="col">
                        <select class="form-control" style="width: 80px;" wire:model.lazy="numberOfPage">
                            <option value="10" selected>10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.List Customer -->

    @include('livewire.accstar._modelCustomer')
    @include('livewire.accstar._mycss')
</div>