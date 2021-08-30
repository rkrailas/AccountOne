<div class="modal" id="modalCustomer" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ลูกค้า</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div>
                    <input wire:model.lazy="query" type="text" class="form-control"
                        placeholder="Search"> <!-- lazy=Lost Focus ถึงจะ Postback  -->
                    <div wire:loading.delay wire:target="searchTerm">
                        <div class="la-ball-clip-rotate la-dark la-sm">
                            <div></div>
                        </div>
                    </div>
                </div>
                <x-search-input />
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">รหัสลูกค้า</th>
                            <th scope="col">ชื่อ</th>
                            <th scope="col">taxid</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($customers != Null)
                        @foreach ($customers as $customer)
                        <tr>
                            <td scope="col">{{ $loop->iteration }}</td>
                            <td scope="col">{{ $customer->customerid }}</td>
                            <td scope="col">{{ $customer->name }}</td>
                            <td scope="col">{{ $customer->taxid }}</td>
                            <td scope="col"></td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
            </div>
        </div>
    </div>
</div>