<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>AdminLTE 3 | Starter</title>

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('backend/dist/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="{{ asset('backend/plugins/toastr/toastr.min.css') }}">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('backend/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <livewire:styles />
    @stack('styles')

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.partials.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layouts.partials.aside')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            {{ $slot }}
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        @include('layouts.partials.footer')
    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="{{ asset('backend/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('backend/dist/js/adminlte.min.js') }}"></script>

    <script type="text/javascript" src=" {{ asset('backend/plugins/toastr/toastr.min.js') }}"></script>

    <script type="text/javascript" src="https://unpkg.com/moment"></script>
    <script type="text/javascript"
        src=" {{ asset('backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js" integrity="sha512-HWlJyU4ut5HkEj0QsK/IxBCY55n5ZpskyjVlAoV9Z7XQwwkqXoYdCIC93/htL3Gu5H3R4an/S0h2NXfbZk3g7w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
    $(document).ready(function() {
        toastr.options = {
            "positionClass": "toast-bottom-right",
            "progressBar": true,
        }

        window.addEventListener('hide-form', event => {
            $('#form').modal('hide');
            toastr.success(event.detail.message, 'success!');
        })

        window.addEventListener('hide-form555', event => {
            $('#form555').modal('hide');
            toastr.success(event.detail.message, 'success!');
        })
    });
    </script>

    <script>
    window.addEventListener('show-form', event => {
        $('#form').modal('show');
    })

    window.addEventListener('show-delete-modal', event => {
        $('#confirmationModal').modal('show');
    })

    window.addEventListener('hide-delete-modal', event => {
        $('#confirmationModal').modal('hide');
        if(event.detail.message != undefined){
            toastr.success(event.detail.message, 'success!');
        }        
    })

    window.addEventListener('show-infor-modal', event => {
        $('#informationModal').modal('show');
    })

    window.addEventListener('show-form555', event => {
        $('#form555').modal('show');
    })

    window.addEventListener('show-delete-modal555', event => {
        $('#confirmationModal555').modal('show');
    })

    window.addEventListener('hide-delete-modal555', event => {
        $('#confirmationModal555').modal('hide');
    })

    window.addEventListener('alert', event => {
        toastr.success(event.detail.message, 'success!');
    })
    </script>

    <!-- For Accstar -->
    <script>
    window.addEventListener('show-formJournal', event => {
        $('#formJournal').modal('show');
    })

    window.addEventListener('hide-formJournal', event => {
        $('#formJournal').modal('hide');
        toastr.success(event.detail.message, 'Success!');
    })

    window.addEventListener('show-customerForm', event => {
        $('#customerForm').modal('show');
    })

    window.addEventListener('hide-customerForm', event => {
        $('#customerForm').modal('hide');
    })

    window.addEventListener('show-soDeliveryTaxForm', event => {
        $('#soDeliveryTaxForm').modal('show');
    })

    window.addEventListener('hide-soDeliveryTaxForm', event => {
        $('#soDeliveryTaxForm').modal('hide');
    })

    window.addEventListener('show-myModal2', event => {
        $('#myModal2').modal('show');
    })
    </script>

    <script type="text/javascript">
    function canNotEmpty(inputtx) {
        if (inputtx.value.length == 0) {
            alert("กรุณาป้อนค่า !");
            return false;
        }
        return true;
    }
    </script>
    <!-- /For Accstar -->

    @stack('js')
    <livewire:scripts />

</body>

</html>