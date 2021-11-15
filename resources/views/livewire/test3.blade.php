<div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select myOption</label>
                    <x-select2 id="account-select2" wire:model="myaccount">
                        <option value=" ">---โปรดเลือก---</option>
                        @foreach($account_dd as $row)
                        <option value='{{ $row->account }}'
                            @if ($myaccount == $row->account)
                            selected="selected"
                            @endif
                            >
                            {{ $row->account . ' : ' .  $row->accnameother}}
                        </option>
                        @endforeach
                    </x-select2>
                    <select id="select-testing" class="selectpicker" data-live-search="true" title="Please select" wire:model="myaccount">
                        @foreach($account_dd as $row)
                        <option>
                            {{ $row->account . ' : ' .  $row->accnameother}}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary" wire:click="display">Display Value</button>
                <button class="btn btn-danger" wire:click="clearValue">Clear Value</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>

    window.addEventListener('clear-select2', event => {
        clearSelect2('account-select2');
    })
    
    window.addEventListener('addval-select2', event => {
        addValSelect2('account-select2');
    })
</script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
@endpush