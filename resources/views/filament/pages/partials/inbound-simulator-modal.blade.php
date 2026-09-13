<x-filament::modal id="inbound-simulator-modal" width="3xl" slide-over>
    <x-slot name="heading">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                <x-heroicon-m-bolt class="w-5 h-5" />
            </div>
            <div>
                <span class="font-bold text-lg text-gray-900 dark:text-white">Inbound Lead Simulator</span>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Test real-time Postmark webhooks, Gemini AI criteria extraction & matching</p>
            </div>
        </div>
    </x-slot>

    <div x-data="{ simTab: 'presets' }" class="space-y-5 pr-1 pb-4">

        <!-- Tab Selector -->
        <div class="flex border-b border-gray-200 dark:border-white/10 gap-6">
            <button type="button"
                @click="simTab = 'presets'"
                :class="simTab === 'presets' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border-b-2 border-transparent'"
                class="pb-2.5 text-sm transition-colors flex items-center gap-2">
                <x-heroicon-m-sparkles class="w-4 h-4" />
                1-Click Presets (95%+ Matches)
            </button>
            <button type="button"
                @click="simTab = 'custom'"
                :class="simTab === 'custom' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border-b-2 border-transparent'"
                class="pb-2.5 text-sm transition-colors flex items-center gap-2">
                <x-heroicon-m-code-bracket class="w-4 h-4" />
                Custom Webhook Composer
            </button>
        </div>

        <!-- TAB 1: 1-CLICK PRESETS (Tokenized Carmine Borders) -->
        <div x-show="simTab === 'presets'" class="space-y-4">

            <!-- Preset 1: Hydra Diplomatic Estate -->
            <div class="p-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] hover:border-primary-500/40 dark:hover:border-primary-500/40 transition flex flex-col justify-between gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-base">🇩🇿</span>
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white">Algiers Diplomatic Estate Inquiry</h4>
                            <x-filament::badge color="success" size="md">98% Match Target</x-filament::badge>
                        </div>
                        <p class="text-xs text-gray-500 font-mono">From: info+hydra@moncefdev.me &bull; $5,000,000 Budget</p>
                    </div>
                    <x-filament::button outlined wire:click="simulatePreset('hydra')" size="xs" color="primary" icon="heroicon-m-bolt">
                        Simulate
                    </x-filament::button>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 italic border-l-2 border-primary-500/50 pl-3">
                    "Seeking a 6-bedroom ambassadorial compound in Hydra with high security and heated pool..."
                </p>
            </div>

            <!-- Preset 2: Paris Avenue Montaigne -->
            <div class="p-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] hover:border-primary-500/40 dark:hover:border-primary-500/40 transition flex flex-col justify-between gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-base">🇫🇷</span>
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white">Paris Avenue Montaigne Penthouse</h4>
                            <x-filament::badge color="success" size="md">98% Match Target</x-filament::badge>
                        </div>
                        <p class="text-xs text-gray-500 font-mono">From: info+paris@moncefdev.me &bull; €4,000,000 Budget</p>
                    </div>
                    <x-filament::button outlined wire:click="simulatePreset('paris')" size="xs" color="primary" icon="heroicon-m-bolt">
                        Simulate
                    </x-filament::button>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 italic border-l-2 border-primary-500/50 pl-3">
                    "Searching for an exclusive 4-bedroom Haussmannian corner penthouse in the Golden Triangle..."
                </p>
            </div>

            <!-- Preset 3: Miami Brickell Penthouse -->
            <div class="p-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] hover:border-primary-500/40 dark:hover:border-primary-500/40 transition flex flex-col justify-between gap-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-base">🇺🇸</span>
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white">Miami Brickell Sky Penthouse</h4>
                            <x-filament::badge color="success" size="md">95% Match Target</x-filament::badge>
                        </div>
                        <p class="text-xs text-gray-500 font-mono">From: info+miami@moncefdev.me &bull; $3,000,000 Cash</p>
                    </div>
                    <x-filament::button outlined wire:click="simulatePreset('miami')" size="xs" color="primary" icon="heroicon-m-bolt">
                        Simulate
                    </x-filament::button>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 italic border-l-2 border-primary-500/50 pl-3">
                    "Looking for a 3-bedroom luxury sky mansion in Brickell Avenue with Biscayne Bay views..."
                </p>
            </div>

        </div>

        <!-- TAB 2: CUSTOM WEBHOOK COMPOSER -->
        <div x-show="simTab === 'custom'" class="space-y-4" style="display: none;">
            {{ $this->simulatorForm }}
        </div>
    </div>

    <x-slot name="footer">
        <x-filament::button color="gray" x-on:click="close()" class="w-full">
            Close Simulator
        </x-filament::button>
    </x-slot>
</x-filament::modal>
