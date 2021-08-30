<div class="container">
    <label>customer</label>
    <div wire:ignore>
        <select id="customer-dropdown" class="form-control" wire:model="customer">
            @foreach($customers_dd as $customer_dd)
            <option value="{{ $customer_dd->customerid }}">
                {{ $customer_dd->customerid . ": " . $customer_dd->name }}
            </option>
            @endforeach
        </select>
    </div>
</div>


@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css"
    integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"
    integrity="sha512-2ImtlRlf2VVmiGZsjm9bEyhjGW4dU7B6TNwh/hx/iSByxNENtj3WVE6o/9Lj4TJeVXPi4bnOIMXFIJJAeufa0A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
$(document).ready(function() {
    $('#customer-dropdown').select2();
    $('#customer-dropdown').on('change', function(e) {
        let data = $(this).val();
        @this.set('customer', data);
    });

});
</script>
@endpush