<div class="container">
    <x-loading-indicator target="getAccount" />
    <div class="row">
        <div class="col">
            <button type="button" class="btn btn-danger mt-2" wire:click.prevent="getAccount">
                ผ่านรายการบัญชี</button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col">
            <input list="customers" value="" class="col-sm-6 custom-select custom-select-sm" wire:model.lazy="selectCustomer">
            <datalist id="customers">
                @foreach ($customer as $item)
                <option value="{{ $item->customerid }}"> {{ $item->name }}
                @endforeach
            </datalist>
            <button type="button" class="btn btn-primary mt-2" wire:click.prevent="clearSelectCustomer">
                Reset Customer</button>
        </div>
        <div class="col">
            <label>Customer :</label> <h3>{{ $selectCustomer }}   </h3>         
        </div>
    </div>

</div>