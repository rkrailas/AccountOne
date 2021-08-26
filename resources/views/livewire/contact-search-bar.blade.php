<div>
    <div class="container">
        <input type="text" class="form-control" placeholder="Search Contacts..." 
            wire:model="query"
            wire:keydown.escape="reset1" 
            wire:keydown.tab="reset1"
            wire:keydown.arrow-up="decrementHighlight"
            wire:keydown.arrow-down="incrementHighlight"
            wire:keydown.enter="selectContact"
        >

        <div wire:loading.delay class="absolute z-10 list-group bg-white w-full rounded-t-none shadow-lg">
            <div class="list-group-item list-group-item-action"><b>Search...</b></div>
        </div>


        @if(!empty($query))
        <div class="absolute z-10 list-group bg-white w-full rounded-t-none shadow-lg">
            @if(!empty($contacts))
                @foreach($contacts as $i => $contact)
                <a 
                    href="#" 
                    class="list-group-item list-group-item-action {{ $highlightIndex == $i ? 'active' : '' }}"
                    wire:click="selectContact({{ $i }})"> 
                    {{ $contact['name'] }}
                </a>
                @endforeach
            @else
            <div class="list-group-item list-group-item-action"><b>No Results!</b></div>
            @endif
        </div>
        @endif



    </div>
    
</div>