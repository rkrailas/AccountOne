<div>
    <button type="button" class="btn btn-primary"
        wire:click.prevent="modelTest">
        Primary
    </button>
</div>

<div class="modal" id="modeltest555" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">ทดสอบ</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <select id="select1">
                    @foreach($customers_dd as $customer)
                    <option value="{{ $customer->customerid }}">
                        {{ $customer->customerid . ": " . $customer->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tail.select@0.5.15/css/bootstrap4/tail.select-default.min.css"
    rel="stylesheet">
@endpush


@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tail.select@0.5.15/js/tail.select-full.min.js"></script>
<script>
tail.select('#select1', {
    search: true,
});
</script>
@endpush

@include('livewire.accstar._mycss')