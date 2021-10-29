@push('js')
<script>
  window.addEventListener('delete-confirmation', event => {
    Swal.fire({
      title: 'คุณต้องการลบ/ยกเลิก ?',
      text: 'คุณจะไม่สามารถย้อนกลับได้!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'ตกลง',
      cancelButtonText: 'ยกเลิก',
    }).then((result) => {
      if (result.isConfirmed) {
        Livewire.emit('deleteConfirmed')
      }
    })
  })
</script>
@endpush


{{-- ็How to use
===In Blade===
<a href="" wire:click.prevent="confirmDelete('{{ $idforDelete }}')">
  <i class="fa fa-trash text-danger"></i>
</a>

===In Controller===
protected $listeners = ['deleteConfirmed' => 'delete'];

public function confirmDelete($gltran)
    {
        $this->sNumberDelete = $gltran;
        $this->dispatchBrowserEvent('delete-confirmation');
    }

public function delete()
{   
    //Delete Process
} --}}