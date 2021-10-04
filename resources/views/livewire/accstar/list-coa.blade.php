<div>
    <button type="button" class="btn btn-primary mt-2 ml-2" wire:click.prevent="chartofaccounts">
        ผังบัญชี</button>
    <div class="container">
        {!! $coa !!}
    </div>
</div>

@push('styles')
<style>
    ul { 
   list-style-type: disc; 
   list-style-position: inside; 
    }
    ol { 
    list-style-type: decimal; 
    list-style-position: inside; 
    }
    ul ul, ol ul { 
    list-style-type: circle; 
    list-style-position: inside; 
    margin-left: 15px; 
    }
    ol ol, ul ol { 
    list-style-type: lower-latin; 
    list-style-position: inside; 
    margin-left: 15px; 
    }
</style>
@endpush