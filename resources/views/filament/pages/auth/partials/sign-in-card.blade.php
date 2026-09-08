<div class="mm-card">
    <div>
        <div class="mm-card-header">
            <h2 class="mm-card-title">{{ $this->getHeading() }}</h2>
            <p class="mm-card-subtitle">{{ $this->getSubheading() }}</p>
        </div>

        <form wire:submit="authenticate" class="space-y-6">
            {{ $this->form }}

            <div style="margin-top: 1.75rem;">
                <x-filament::button
                    type="submit"
                    color="primary"
                    class="w-full"
                    size="lg"
                    wire:loading.attr="disabled"
                    wire:target="authenticate"
                >
                    <span wire:loading.remove wire:target="authenticate">Sign In</span>
                    <span wire:loading wire:target="authenticate">Authenticating...</span>
                </x-filament::button>
            </div>
        </form>
    </div>
</div>
