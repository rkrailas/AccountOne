<div class="modal" id="myModal2" data-backdrop="static">
    <div class="modal-dialog" style="max-width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">การบันทึกบัญชี</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="container"></div>
            <div class="modal-body">
                <table class="table table-hover">
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
                            <td scope="col">{{ $genGL['gldebit'] }}</td>
                            <td scope="col">{{ $genGL['glcredit'] }}</td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn">Close</a>
            </div>
        </div>
    </div>
</div>