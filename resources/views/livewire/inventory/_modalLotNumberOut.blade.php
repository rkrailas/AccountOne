<div class="modal" id="lotNumberOutForm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form autocomplete="off">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        สินค้าแบบมี Lot Number
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Closed</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col">
                            <input wire:model.lazy="searchLotNumber" type="text" class="form-control form-control-sm border-0" id="searchLotNumber"
                                placeholder="ค้นหา">
                            <div wire:loading.delay wire:target="searchSN">
                                <div class="la-ball-clip-rotate la-dark la-sm">
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped myGridTB" id="table" style="width: 700px;">
                                <thead>
                                    <tr class="text-center">
                                        <th></th>
                                        <th scope="col">#</th>
                                        <th scope="col">Lot Number</th>
                                        <th scope="col">คงเหลือ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($lotNumbers)
                                        @foreach($lotNumbers as $index => $row)
                                        <tr>
                                            <td>
                                                <a style="cursor: pointer;"
                                                    wire:click.prevent="selectedLotNumber('{{ $lotNumbers[$index]['lotnumber'] }}')">
                                                    <i class="far fa-check-square"></i>
                                                </a>
                                            </td>
                                            <td scope="row" class="align-middle text-center">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>{{ $lotNumbers[$index]['lotnumber'] }} </td>
                                            <td class="text-right pr-5">{{ number_format($lotNumbers[$index]['instock'],2) }} </td>
                                        </tr>
                                        @endforeach
                                    @endif                                    
                                </tbody>
                                {{-- <tfoot>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td>ยอดรวม</td>
                                        <td>{{ number_format($sumSerialQty,2) }}</td>
                                        <td>{{ number_format($sumSerialCost,2) }}</td>
                                        <td colspan="3"></td>
                                    <tr>
                                </tfoot> --}}
                            </table>
                        </div>
                    </div>
                    <!-- /.Grid -->
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </form>
    </div>
</div>


@push('js')
<script>
    window.addEventListener('show-lotNumberOutForm', event => {
        $('#lotNumberOutForm').modal('show');
    })

    window.addEventListener('hide-lotNumberOutForm', event => {
        $('#lotNumberOutForm').modal('hide');
    })

    $(document).ready(function(){
        $("#lotNumberOutForm").on('shown.bs.modal', function(){
            $(this).find('#searchLotNumber').focus();
        });
    })
</script>
@endpush