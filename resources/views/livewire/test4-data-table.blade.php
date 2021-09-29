<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card-body">
                <table id="example1" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>เลขที่ใบสำคัญรับ</th>
                            <th>วันที่ใบสำคัญรับ</th>
                            <th>ผู้ซื้อ</th>
                            <th>ยอดเงิน</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recieptJournals as $recieptJournal)
                        <tr>
                            <td scope="col">{{ $recieptJournal->gltran }} </td>
                            <td scope="col">{{ \Carbon\Carbon::parse($recieptJournal->gjournaldt)->format('Y-m-d') }}
                            </td>
                            <td scope="col">{{ $recieptJournal->customername }} </td>
                            <td scope="col">{{ number_format($recieptJournal->amount,2) }} </td>
                            <td>
                                <a href="" wire:click.prevent="edit('{{ $recieptJournal->gltran }}')">
                                    <i class="fa fa-edit mr-2"></i>
                                </a>
                                <a href="" wire:click.prevent="confirmDelete('{{ $recieptJournal->gltran }}')">
                                    <i class="fa fa-trash text-danger"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
</script>
@endpush