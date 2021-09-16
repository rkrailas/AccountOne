<div class="container">
    <x-loading-indicator />
    <div class="row">
        <button type="button" class="btn btn-primary mt-2" wire:click.prevent="getCustomer">
            รายชื่อลูกค้า</button>
    </div>
    <div class="row mt-2">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Customer ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Phone</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($myCustomer as $item)
                <tr>
                    <th scope="row">1</th>
                    <td>{{ $item->customerid }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->phone1 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>