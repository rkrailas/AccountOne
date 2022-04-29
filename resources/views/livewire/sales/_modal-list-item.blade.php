<div class="modal" id="itemListForm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <form autocomplete="off">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        รายการสินค้า
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> 
                            <i class="fa fa-times mr-1"></i>Closed
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col">
                            <input wire:model.lazy="searchItem" type="text" class="form-control form-control-sm border-0" id="searchItem"
                                {{-- wire:keydown.enter="searchItemInModal('{{ $searchItem }}')" 1/12/64 เปลี่ยนไปใช้ Tab แทน--}}
                                placeholder="ค้นหา">
                            <div wire:loading.delay wire:target="searchItem">
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
                                        <th scope="col">รหัสสินค้า</th>
                                        <th scope="col">คำอธิบาย</th>
                                        <th scope="col">คงเหลือ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($listItem)
                                        @foreach($listItem as $index => $row)
                                        <tr class="align-middle text-center">
                                            <td>
                                                <a style="cursor: pointer;"
                                                    wire:click.prevent="selectedItem('{{ $workingRow }}','{{ $listItem[$index]['itemid'] }}')">
                                                    <i class="far fa-check-square"></i>
                                                </a>
                                            </td>
                                            <td scope="row"> {{ $loop->iteration }} </td>
                                            <td>{{ $listItem[$index]['itemid'] }}</td>
                                            <td>{{ $listItem[$index]['description'] }}</td>
                                            <td>{{ $listItem[$index]['instock'] }}</td>
                                        </tr>
                                        @endforeach
                                    @else 
                                        <tr>
                                            <td colspan="5">
                                                No results found
                                            </td>                                            
                                        </tr>                                        
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
    window.addEventListener('show-itemListForm', event => {
        $('#itemListForm').modal('show');
    })

    window.addEventListener('hide-itemListForm', event => {
        $('#itemListForm').modal('hide');
    })

    $(document).ready(function(){
        $("#itemListForm").on('shown.bs.modal', function(){
            $(this).find('#searchItem').focus();
        });
    })
</script>

@endpush