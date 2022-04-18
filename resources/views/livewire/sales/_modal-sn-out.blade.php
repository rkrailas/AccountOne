<div class="modal fade bd-example-modal-xl" id="serialNoOutForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 85%;">
        <form autocomplete="off">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        กำหนดสินค้าแบบมี Serial No
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Closed</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col">
                            <input wire:model.lazy="searchSN" type="text" class="form-control form-control-sm border-0" id="searchSN"
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
                            <table class="table table-striped myGridTB" id="table" style="width: 1000px;">
                                <thead>
                                    <tr class="text-center">
                                        <th></th>
                                        <th scope="col">#</th>
                                        <th scope="col">Serial No</th>
                                        <th scope="col">สถานที่เก็บ</th>
                                        <th scope="col">สี</th>
                                        <th scope="col">อ้างอิง 1</th>
                                        <th scope="col">อ้างอิง 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($serialDetails)
                                        @foreach($serialDetails as $index => $row)
                                        <tr class="align-middle text-center">
                                            <td style="vertical-align: middle;">
                                                <a style="cursor: pointer;"
                                                    wire:click.prevent="selectedSN('{{ $serialDetails[$index]['serialno'] }}')">
                                                    <i class="far fa-check-square"></i>
                                                </a>
                                            </td>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>{{ $serialDetails[$index]['serialno'] }}</td>
                                            <td>{{ $serialDetails[$index]['location'] }}</td>
                                            <td>{{ $serialDetails[$index]['color'] }}</td>
                                            <td>{{ $serialDetails[$index]['reference1'] }}</td>
                                            <td>{{ $serialDetails[$index]['reference2'] }}</td>
                                        </tr>
                                        @endforeach
                                    @else 
                                        <tr>
                                            <td colspan="8">
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
    window.addEventListener('show-serialNoOutForm', event => {
        $('#serialNoOutForm').modal('show');
    })

    window.addEventListener('hide-serialNoOutForm', event => {
        $('#serialNoOutForm').modal('hide');
    })

    $(document).ready(function(){
        $("#serialNoOutForm").on('shown.bs.modal', function(){
            $(this).find('#searchSN').focus();
        });
    })
</script>

@endpush