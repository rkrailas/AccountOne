<!-- /.Model Form Delete -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>โปรดยืนยัน</h5>
            </div>
            <div class="modal-body">
                <h4>{{ $modelMessage }}</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Cancel</button>
                <button type="button" wire:click.prevent="delete" class="btn btn-danger">
                    <i class="fa fa-trash mr-1"></i>Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- /.Model Form Delete -->

<!-- /.Model Form Information -->
<div class="modal fade" id="informationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5>ข้อมูล</h5>
            </div>
            <div class="modal-body">
                <h4>{{ $modelMessage }}</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i>Close</button>
            </div>
        </div>
    </div>
</div>
<!-- /.Model Form Information -->