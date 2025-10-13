<x-filament::page>
    <div class="flex flex-col items-center justify-center w-full gap-10">
        <form wire:submit.prevent="create"  class="lg:w-[50%] w-full">
            {{ $this->form }}
            <x-filament::button type="submit" form="create" >
                {{ __('Create report') }}
            </x-filament::button>
        </form>
    </div>
</x-filament::page>
