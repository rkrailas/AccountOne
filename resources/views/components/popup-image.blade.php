@push('js')
<script>
  window.addEventListener('popup-image',function(e) {
        Swal.fire({
            imageUrl: e.detail.imageUrl,
            imageHeight: 400,
        });
    });
</script>
@endpush