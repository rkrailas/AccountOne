<div class="container">
    <table class="table table-hover" data-toggle="table" data-pagination="true" data-search="true">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th data-sortable="true" scope="col">เลขที่ใบสั่งขาย</th>
                <th scope="col">วันที่ใบสั่งขาย</th>
                <th scope="col">ผู้ซื้อ</th>
                <th scope="col">ยอดเงิน</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salesOrders as $salesOrder)
            <tr>
                <td scope="col"></td>
                <td scope="col">{{ $salesOrder->snumber }} </td>
                <td scope="col">{{ \Carbon\Carbon::parse($salesOrder->sodate)->format('Y-m-d') }} </td>
                <td scope="col">{{ $salesOrder->name }} </td>
                <td scope="col">{{ number_format($salesOrder->sototal,2) }} </td>
                <td>
                    <a href="" >
                        <i class="fa fa-trash text-danger"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @include('livewire.accstar._modalSalesOrder')
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css">
@endpush

@push('js')
<script src="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.js"></script>
@endpush