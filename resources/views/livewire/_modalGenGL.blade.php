<div class="modal" id="myModal2" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" style="font-size: 20px;">การบันทึกบัญชี</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body2">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">รหัสบัญชี</th>
                            <th scope="col">ชื่อบัญชี</th>
                            <th scope="col">เดบิต</th>
                            <th scope="col">เครดิต</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($genGLs != Null)
                        @foreach ($genGLs as $genGL)
                        <tr>
                            <td scope="col">{{ $loop->iteration  }}</td>
                            <td scope="col">{{ $genGL['glaccount'] }}</td>
                            <td scope="col">{{ $genGL['glaccname'] }}</td>
                            <td scope="col" style="text-align: right;">{{ number_format($genGL['gldebit'],2) }}</td>
                            <td scope="col" style="text-align: right;">{{ number_format($genGL['glcredit'],2) }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="text-align: right; color: blue; font-weight: bold;">
                            <td></td>
                            <td></td>
                            <td>ยอดรวม</td>
                            <td>{{ number_format($sumDebit,2) }}</td>
                            <td>{{ number_format($sumCredit,2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                            <i class="fa fa-times mr-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    window.addEventListener('show-myModal2', event => {
        $('#myModal2').modal('show');
    })
</script>
@endpush