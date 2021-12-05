<div class="modal fade bd-example-modal-xl" id="serialNoOutForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable" style="max-width: 85%;">
        <form autocomplete="off">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        กำหนดสินค้าแบบมี Serial No
                    </h5>
                    <div class="float-right">
                        <button type="button" class="btn btn-secondary" wire:click.prevent="closedModalSerialNo">
                            <i class="fa fa-times mr-1"></i>Closed</button>
                    </div>
                </div>
                <div class="modal-body">
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
                                        <th scope="col">ต้นทุน</th>
                                        <th scope="col">สี</th>
                                        <th scope="col">อ้างอิง 1</th>
                                        <th scope="col">อ้างอิง 2</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($serialDetails as $index => $row)
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <div d-inline ml-2>
                                                <input wire:model="selectedRows" type="checkbox" value="{{ $serialDetails[$index]['serialno'] }}"
                                                    id="{{ $serialDetails[$index]['serialno'] }}">
                                                <label for="{{ $serialDetails[$index]['serialno'] }}"></label>
                                            </div>
                                        </td>
                                        <td scope="row" class="align-middle text-center">
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>{{ $serialDetails[$index]['serialno'] }} </td>
                                        <td>{{ $serialDetails[$index]['location'] }} </td>
                                        <td class="text-right">{{ number_format($serialDetails[$index]['cost'],2) }} </td>
                                        <td>{{ $serialDetails[$index]['color'] }} </td>
                                        <td>{{ $serialDetails[$index]['reference1'] }} </td>
                                        <td>{{ $serialDetails[$index]['reference2'] }} </td>
                                    </tr>
                                    @endforeach
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
</script>
@endpush