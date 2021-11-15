@props(['id'])

{{-- {{ $attributes }} --}}
<div wire:ignore>
    <select id="{{ $id }}" style="width: 100%"> 
        {{ $slot }}
    </select>
</div>

@once
    @push('styles')
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

        <!-- Customize hight = form-control-sm -->
        <style>
        .select2-selection__rendered {
            /* line-height: 21px !important; */
            line-height: calc(1.8125rem + 2px);
        }
        .select2-container .select2-selection--single {
            height: calc(1.8125rem + 2px) !important;
        }
        .select2-selection__arrow {
            height: 0px !important;
        }
        </style>
    @endpush
@endonce

@once
    @push('js')
        <!-- Select2 -->
        <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>

        <script>
            function clearSelect2(id){
                $('#'+id).select2('val', ' ');
            }
        </script>
    @endpush
@endonce

@push('js')
    <script>       
        $(function(){
            $('#{{ $id }}').select2({
                theme: 'bootstrap4',
            }).on('change',function(){
                @this.set('{{ $attributes->whereStartsWith('wire:model')->first() }}', $(this).val());
            });
        })
    </script>
@endpush