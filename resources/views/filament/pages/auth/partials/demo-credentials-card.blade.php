<div class="cad-card">
    <div>
        <div class="cad-card-header">
            <h2 class="cad-card-title">
                Demo Credentials
            </h2>
            <p class="cad-card-subtitle">Try different user roles instantly</p>
        </div>

        <!-- 2x2 Outlined Grid Buttons with Instant Debounce & Loading Feedback -->
        <div class="cad-role-grid transition-opacity duration-200" :class="{ 'opacity-50 pointer-events-none': isLoggingIn }">
            @foreach($this->getDemoAccounts() as $acc)
            <x-filament::button
                type="button"
                :color="$acc['color']"
                :icon="$acc['icon']"
                size="md"
                outlined
                wire:loading.attr="disabled"
                wire:target="quickLogin, authenticate"
                x-bind:disabled="isLoggingIn"
                @click="quickLogin('{{ $acc['email'] }}')"
                class="w-full justify-center shadow-xs">
                {{ $acc['role'] }}
            </x-filament::button>
            @endforeach
        </div>

        <!-- Divider -->
        <div class="cad-divider">
            <span>Or copy credentials manually:</span>
        </div>

        <!-- Demo Password Helper Notice -->
        <div class="flex items-center justify-center gap-1.5 text-[11px] text-gray-500 dark:text-gray-400 mb-3 font-mono">
            <x-heroicon-m-key class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400 shrink-0" />
            <span>Default password for all personas: <strong class="text-gray-900 dark:text-white">password</strong></span>
        </div>

        <!-- Role Rows with Individual Action Buttons -->
        @foreach($this->getDemoAccounts() as $acc)
        <div class="cad-account-row">
            <div class="cad-account-info">
                <div class="cad-account-icon border {{ $acc['icon_classes'] }}">
                    <x-filament::icon
                        :icon="$acc['icon_o'] ?? $acc['icon']"
                        class="w-5 h-5" />
                </div>
                <div class="min-w-0">
                    <div class="cad-account-role">{{ $acc['role'] }}</div>
                    <div class="cad-account-email">{{ $acc['email'] }}</div>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <x-filament::button
                    type="button"
                    color="gray"
                    size="xs"
                    outlined
                    @click="copyToClipboard('{{ $acc['email'] }}', $event)">
                    Copy
                </x-filament::button>

                <div class="relative inline-flex">
                    <x-filament::button
                        type="button"
                        icon="heroicon-m-arrow-top-right-on-square"
                        :color="$acc['color']"
                        size="xs"
                        outlined
                        tooltip="View {{ $acc['role'] }} Permissions"
                        @click="activeRoleKey = '{{ $acc['key'] }}'; $dispatch('open-modal', { id: 'role-details-modal' })" />
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
