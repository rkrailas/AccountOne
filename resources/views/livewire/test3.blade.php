<div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select myOption</label>
                        <x-select2 wire:model="myOption" placeholder="Select My Options" id="myOption">
                            <option value=" ">---โปรดเลือก---</option>
                            <option value="Option1">Option1</option>
                            <option value="Option2">Option2</option>
                            <option value="Option3">Option3</option>
                            <option value="Option4">Option4</option>
                            <option value=" Option5 ">Option5</option>
                        </x-select2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select Team Members2</label>
                    <x-select2 wire:model="myOption2" placeholder="Select My Options2" id="myOption2">
                        <option value=" ">---โปรดเลือก---</option>
                        <option>Option11</option>
                        <option>Option22</option>
                        <option>Option33</option>
                        <option>Option44</option>
                        <option>Option55</option>
                    </x-select2>
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
        clearSelect2('myOption');
    })
</script>
@endpush