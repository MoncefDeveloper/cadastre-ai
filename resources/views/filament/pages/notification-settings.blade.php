<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">

        {{ $this->form }}

        <!-- Clean, flat, perfectly styled save button with native loading state -->
        <div class="flex justify-end pt-4">
            <x-filament::button
                type="submit"
                size="md"
                color="primary"
                outlined="true"
                icon="heroicon-m-bookmark"
                wire:target="save"
            >
                Save Preferences
            </x-filament::button>
        </div>

    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
