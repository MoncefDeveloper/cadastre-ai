<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">

        {{ $this->form }}

        <div class="flex justify-end pt-4">
            <x-filament::button
                type="submit"
                size="md"
                color="primary"
                outlined
                icon="heroicon-o-bookmark"
                wire:loading.attr="disabled"
                wire:target="save"
            >
                Save Preferences
            </x-filament::button>
        </div>

    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
