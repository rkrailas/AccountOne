<div class="modal fade bd-example-modal-xl" id="serialNoForm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" wire:ignore.self>
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
                    <!-- Display Error -->
                    @if ($duplicate_sn)
                    <div class="row mb-2">
                        <div class="col">
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="far fa-times-circle mr-1"></i> มีเลขที่ Serial No นี้ในสต็อก! </strong>
                                <ul>
                                    @foreach ($duplicate_sn as $item)
                                    <li> {{ $item->serialno }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>                    
                    @endif

                    <!-- .Grid -->
                    <div class="row mb-2">
                        <div class="col">
                            <table class="table table-striped myGridTB" id="table">
                                <thead>
                                    <tr class="text-center">
                                        <th scope="col">
                                            <button class="btn btn-sm btn-primary" wire:click.prevent="addRowInGrid">+Add</button>
                                        </th>
                                        <th scope="col">Serial No</th>
                                        <th scope="col">สถานที่เก็บ</th>
                                        <th scope="col">ต้นทุน</th>
                                        <th scope="col">สี</th>
                                        <th scope="col">อ้างอิง 1</th>
                                        <th scope="col">อ้างอิง 2</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($serialDetails)
                                        @foreach($serialDetails as $index => $serialDetail)
                                        <tr>
                                            <td scope="row" class="align-middle text-center">
                                                {{ $loop->iteration }}
                                            </td>                                        
                                            <td>
                                                <input type="text" class="form-control form-control-sm" 
                                                    wire:model.defer="serialDetails.{{$index}}.serialno">
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm" required
                                                    wire:model.defer="serialDetails.{{$index}}.location">
                                                    <option value="">--- โปรดเลือก ---</option>
                                                    @foreach($location_dd as $row)
                                                    <option value="{{ $row->code }}">{{ $row->code }}:
                                                        {{ $row->other }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control form-control-sm" required style="text-align: right;" 
                                                wire:model.lazy="serialDetails.{{$index}}.cost">
                                            </td>
                                            <td>
                                                <select class="form-control form-control-sm" required
                                                    wire:model.defer="serialDetails.{{$index}}.color">
                                                    <option value="">--- โปรดเลือก ---</option>
                                                    @foreach($color_dd as $row)
                                                    <option value="{{ $row->code }}">{{ $row->code }}:
                                                        {{ $row->other }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" wire:model.defer="serialDetails.{{$index}}.reference1">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm" wire:model.defer="serialDetails.{{$index}}.reference2">
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="" wire:click.prevent="removeRowInGrid({{ $index }})">
                                                    <i class="fa fa-trash text-danger"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr style="text-align: right; color: blue; font-weight: bold;">
                                        <td></td>
                                        <td>ยอดรวม</td>
                                        <td>{{ number_format($sumSerialQty,2) }}</td>
                                        <td>{{ number_format($sumSerialCost,2) }}</td>
                                        <td colspan="4"></td>
                                    <tr>
                                </tfoot>
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
    window.addEventListener('show-serialNoForm', event => {
        $('#serialNoForm').modal('show');
    })

    window.addEventListener('hide-serialNoForm', event => {
        $('#serialNoForm').modal('hide');
    })
</script>
@endpush