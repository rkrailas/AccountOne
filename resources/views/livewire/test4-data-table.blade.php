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
                                <h4 class="m-0 text-dark">ข้อมูลลูกค้า</h4>
                            </div><!-- /.col -->
                            <div class="col-sm-6 text-right">
                                <button wire:click.prevent="addNew" class="btn btn-primary btn-sm"><i
                                        class="fa fa-plus-circle" mr-1></i>
                                    สร้างลูกค้า</button>
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div>
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>รหัส</th>
                                    <th>ชื่อ</th>
                                    <th>ผู้ติดต่อ</th>
                                    <th>โทรศัพท์</th>
                                    <th>เลขที่ผู้เสียภาษี</th>
                                    <th scope="col">ลูกหนี้</th>
                                    <th scope="col">เจ้าหนี้</th>
                                    <th scope="col">นิติบุคคล</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                <tr>
                                    <td scope="col">{{ $customer->customerid }} </td>
                                    <td scope="col">{{ $customer->name }} </td>
                                    <td scope="col">{{ $customer->contact1 }} </td>
                                    <td scope="col">{{ $customer->phone1 }} </td>
                                    <td scope="col">{{ $customer->taxid }} </td>
                                    <td scope="col" style="text-align:center"> @if($customer->debtor ) <i
                                            class="fas fa-check"></i> @endif </td>
                                    <td scope="col" style="text-align:center"> @if($customer->creditor ) <i
                                            class="fas fa-check"></i> @endif </td>
                                    <td scope="col" style="text-align:center"> @if($customer->corporate ) <i
                                            class="fas fa-check"></i> @endif </td>
                                    <td>
                                        <a href="" wire:click.prevent="edit('{{ $customer->customerid }}')">
                                            <i class="fa fa-edit mr-2"></i>
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