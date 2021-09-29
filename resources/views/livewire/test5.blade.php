<div>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card-header">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <!-- .ปุ่มซ่อนเมนู -->
                                <div class="float-left d-none d-sm-inline">
                                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                                            class="fas fa-bars"></i></a>
                                </div>
                                <!-- /.ปุ่มซ่อนเมนู -->
                                <h4 class="m-0 text-dark">งบทดลอง</h4>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover small">
                            <thead>
                                <tr>
                                    <th>รหัสบัญชี</th>
                                    <th>ชื่อบัญชั</th>
                                    <th>ยกมาเดบิต</th>
                                    <th>ยกมาเครดิต</th>
                                    <th>เดบิต</th>
                                    <th>เครดิต</th>
                                    <th>ยอดปัจจุบัน-Dr</th>
                                    <th>ยอดปัจจุบัน-Dr</th>
                                    <th>ยอดปัจจุบัน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trialBalance as $item)
                            <tr style="text-align: right">
                                    <td scope="col" style="text-align: left">{{ $item['account'] }} </td>
                                    <td scope="col" style="text-align: left">{{ $item['accnameother'] }} </td>
                                    <td scope="col">{{ number_format($item['begindebit'],2) }} </td>
                                    <td scope="col">{{ number_format($item['begincredit'],2) }} </td>
                                    <td scope="col">{{ number_format($item['currentdebit'],2) }} </td>
                                    <td scope="col">{{ number_format($item['currentcredit'],2) }} </td>
                                    <td scope="col">{{ number_format($item['nowdr'],2) }} </td>
                                    <td scope="col">{{ number_format($item['nowcr'],2) }} </td>
                                    <td scope="col">{{ number_format($item['currentbal'],2) }} </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="text-align: right; font-weight: bold;">
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col">{{ number_format($sumBeginDebit,2) }}</td>
                                    <td scope="col">{{ number_format($sumBeginCredit,2) }}</td>
                                    <td scope="col">{{ number_format($sumCurrentDebit,2) }}</td>
                                    <td scope="col">{{ number_format($sumCurrentCredit,2) }}</td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                    <td scope="col"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('js')
<script>
    $(function () {
      $("#example1").DataTable({
        "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
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

@push('styles')
<style>
    th {
        white-space: nowrap;
    }
</style>
@endpush
