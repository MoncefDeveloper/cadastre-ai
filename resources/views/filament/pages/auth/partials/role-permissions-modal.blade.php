<x-filament::modal
    id="role-details-modal"
    width="3xl"
    slide-over>
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-lg font-bold">Role Permission Profile</span>
        </x-slot>
        <x-slot name="description">
            Specific authorization scope and capability comparison for this persona.
        </x-slot>

        <div class="text-gray-900 dark:text-gray-100">
            @foreach($this->getRoleDirectory() as $key => $details)
            <div x-show="activeRoleKey === '{{ $key }}'" class="space-y-4" style="display: none;">

                <!-- Native Filament Role Summary Callout -->
                <x-filament::callout
                    :color="$details['color']"
                    :icon="$details['icon']"
                    icon-size="xl">
                    <x-slot name="heading">
                        <span class="font-bold text-lg">{{ $details['title'] }}</span>
                    </x-slot>

                    <x-slot name="controls">
                        <x-filament::badge :color="$details['color']" size="md">
                            {{ $details['badge'] }}
                        </x-filament::badge>
                    </x-slot>

                    <x-slot name="description">
                        <p class="text-sm leading-relaxed mt-1">
                            {{ $details['scope'] }}
                        </p>
                    </x-slot>

                    <x-slot name="footer">
                        <x-filament::badge color="gray" size="md">
                            {{ $details['email'] }}
                        </x-filament::badge>
                    </x-slot>
                </x-filament::callout>

                <!-- Business Gates -->
                <x-filament::section icon="heroicon-o-lock-closed" collapsible>
                    <x-slot name="heading">
                        Core Business Gates
                    </x-slot>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($details['business_gates'] as $gate)
                        <x-filament::callout
                            :color="$gate['is_granted'] ? 'success' : 'danger'"
                            :icon="$gate['is_granted'] ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'"
                            class="p-2.5!">
                            <x-slot name="heading">
                                <span class="text-sm font-bold">{{ $gate['gate'] }}</span>
                            </x-slot>

                            <x-slot name="controls">
                                <x-filament::badge :color="$gate['is_granted'] ? 'success' : 'danger'" size="md">
                                    {{ $gate['status'] }}
                                </x-filament::badge>
                            </x-slot>

                            <x-slot name="description">
                                <span class="text-[12px] leading-tight block mt-0.5 opacity-80">{{ $gate['desc'] }}</span>
                            </x-slot>
                        </x-filament::callout>
                        @endforeach
                    </div>
                </x-filament::section>

                <!-- Model Permissions Categorized -->
                <x-filament::section icon="heroicon-o-key" collapsible collapsed>
                    <x-slot name="heading">
                        Granted Spatie Permissions by Domain
                    </x-slot>
                    <div class="flex gap-6 flex-col">
                        @foreach($details['categories'] as $category => $permissions)
                        <div>
                            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <x-filament::icon icon="heroicon-m-folder" class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                                <span>{{ $category }}</span>
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($permissions as $perm)
                                <x-filament::badge color="gray" size="md">
                                    {{ $perm }}
                                </x-filament::badge>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-filament::section>

                <!-- Focused Comparison Table for This Role -->
                <x-filament::section icon="heroicon-o-table-cells" collapsible collapsed>
                    <x-slot name="heading">
                        Feature Comparison Table
                    </x-slot>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
                            <thead>
                                <tr class="text-left font-bold text-gray-500 dark:text-gray-400">
                                    <th class="py-3 pr-4">Feature / Gate</th>
                                    <th class="py-3 text-right">Capability Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-white/5 font-medium">
                                @foreach($details['matrix_comparison'] as $row)
                                <tr>
                                    <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $row['feature'] }}</td>
                                    <td class="py-3 text-right">
                                        <x-filament::badge :color="$row['color']" size="md">
                                            {{ $row['capability'] }}
                                        </x-filament::badge>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-filament::section>

            </div>
            @endforeach
        </div>

    </x-filament::section>

    <x-slot name="footer">
        <x-filament::button color="gray" x-on:click="close()" class="w-full">
            Close Profile
        </x-filament::button>
    </x-slot>
</x-filament::modal>
