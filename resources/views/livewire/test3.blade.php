<div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select Team Members</label>
                    <x-select2 wire:model="myOption" placeholder="Select My Options" id="myOption">
                        <option value=" ">---โปรดเลือก---</option>
                        <option>Option1</option>
                        <option>Option2</option>
                        <option>Option3</option>
                        <option>Option4</option>
                        <option>Option5</option>
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
                <button class="btn btn-danger" onclick="clearSelect2('myOption');">Clear Value</button>
            </div>
        </div>
    </div>
</div>