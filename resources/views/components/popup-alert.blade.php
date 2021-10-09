@push('js')
<script>
  window.addEventListener('popup-alert',function(e) {
        Swal.fire({
            title:  e.detail.title,
            icon: 'warning',
        });
    });
</script>
@endpush


{{-- ็How to use
===In Controller===
if($data){
    $this->dispatchBrowserEvent('popup-alert', [
        'title' => 'รหัสนี้มีอยู่แล้ว',
    ]);
}else{
    //Some Process
} --}}