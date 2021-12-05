<div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Select myOption</label>
                    <x-select2 id="account-select2" wire:model="myaccount">
                        <option value=" ">---โปรดเลือก---</option>
                        @foreach($account_dd as $row)
                        <option value='{{ $row->account }}' @if ($myaccount==$row->account)
                            selected="selected"
                            @endif
                            >
                            {{ $row->account . ' : ' . $row->accnameother}}
                        </option>
                        @endforeach
                    </x-select2>
                    {{-- <select id="select-testing" class="selectpicker" data-live-search="true" title="Please select"
                        wire:model="myaccount">
                        @foreach($account_dd as $row)
                        <option>
                            {{ $row->account . ' : ' . $row->accnameother}}
                        </option>
                        @endforeach
                    </select> --}}
                </div>
            </div>
        </div>
        <div class="row">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>
                            <x-select2 id="account-select22" wire:model="myaccount">
                                <option value=" ">---โปรดเลือก---</option>
                                @foreach($account_dd as $row)
                                <option value='{{ $row->account }}' @if ($myaccount==$row->account)
                                    selected="selected"
                                    @endif
                                    >
                                    {{ $row->account . ' : ' . $row->accnameother}}
                                </option>
                                @endforeach
                            </x-select2>
                        </td>
                        <td>Otto</td>
                        <td>@mdo</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Larry</td>
                        <td>the Bird</td>
                        <td>@twitter</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary" wire:click="display">Display Value</button>
                <button class="btn btn-danger" wire:click="clearValue">Clear Value</button>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-xl" id="soDeliveryTaxForm" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
        <div class="modal-dialog modal-dialog-scrollable" style="max-width: 95%;">
            <form autocomplete="off" wire:submit.prevent="createUpdateSalesOrder">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                            ส่งสินค้าพร้อมใบกำกับ
                        </h5>
                        <div class="float-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fa fa-times mr-1"></i>Cancel</button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <table class="table" id="myTable">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">
                                            <button class="btn btn-sm btn-primary"
                                                wire:click.prevent="addRowInGrid">+Add</button>
                                        </th>
                                        <th scope="col">รหัส</th>
                                        <th scope="col">Serial No</th>
                                        <th scope="col" style="width: 7%;">จำนวน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @for ($i = 0; $i <= count($soDetails) ; $i++)
                                        <tr>
                                            <td scope="row" class="align-middle text-center">
                                                1
                                            </td>
                                            <td>
                                                <x-select2-inmodal id="item{{$i}}-select2" modalName="soDeliveryTaxForm"
                                                    wire:model="soDetails.{{$i}}.itemid">
                                                    <option value="">--- โปรดเลือก ---</option>
                                                    @foreach($itemNos_dd as $itemNo_dd)
                                                    <option value="{{ $itemNo_dd->itemid }}">{{ $itemNo_dd->itemid
                                                        }}:
                                                        {{ $itemNo_dd->description }}
                                                    </option>
                                                    @endforeach
                                                </x-select2-inmodal>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control form-control-sm mb-1"
                                                        placeholder="Serial No"
                                                        wire:model.defer="soDetails.0.serialno">
                                                 </div>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control form-control-sm"
                                                    required style="text-align: right;"
                                                    wire:model.lazy="soDetails.0.quantity">
                                            </td>
                                        </tr>

                                            @if ($i = count($soDetails))
                                                @push('js')
                                                @endpush
                                            @endif
                                            
                                        @endfor
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



@push('js')
<script>
    window.addEventListener('show-myModal', event => {
        $('#soDeliveryTaxForm').modal('show');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('account-select2');
    })
    
    window.addEventListener('addval-select2', event => {
        addValSelect2('account-select2');
    })

    window.addEventListener('addblank', event => {
        $('#myTable').append('<tr><td>1</td><td>1</td><td>1</td></tr>');
    })
</script>

<script type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
@endpush

@push('styles')
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">
@endpush