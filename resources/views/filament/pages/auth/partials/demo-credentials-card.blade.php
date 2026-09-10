<div class="cad-card">
    <div>
        <div class="cad-card-header">
            <h2 class="cad-card-title">
                Demo Credentials
            </h2>
            <p class="cad-card-subtitle">Try different user roles instantly</p>
        </div>

        <!-- 2x2 Outlined Grid Buttons -->
        <div class="cad-role-grid">
            @foreach($this->getDemoAccounts() as $acc)
            <x-filament::button
                type="button"
                :color="$acc['color']"
                :icon="$acc['icon']"
                size="md"
                outlined
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
