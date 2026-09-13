<div x-show="aiBrainTab === 'info'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    <!-- Sleek Profile Card -->
    <div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5">
        <div class="flex items-center justify-center w-12 h-12 rounded-full font-bold text-base mt-1 {{ $this->getAvatarColor($this->activeThread()->client->id) }}">
            {{ strtoupper(substr($this->activeThread()->client->first_name, 0, 1)) }}
        </div>
        <div class="flex flex-col items-start">
            <h4 class="font-bold text-gray-900 dark:text-white">{{ $this->activeThread()->client->first_name }} {{ $this->activeThread()->client->last_name }}</h4>
            <p class="text-xs text-gray-500 mb-2">{{ $this->activeThread()->client->email }}</p>

            <!-- Dynamic Badges -->
            <div class="flex flex-wrap gap-1.5">
                @if($this->activeThread()->extracted_criteria['is_property_inquiry'] ?? false)
                <x-filament::badge color="success" size="md">Qualified Lead</x-filament::badge>
                @endif

                @if(isset($this->activeThread()->extracted_criteria['listing_type']))
                @php $type = $this->activeThread()->extracted_criteria['listing_type']; @endphp
                <x-filament::badge color="{{ $type === 'sale' ? 'info' : 'warning' }}" size="md">
                    {{ ucfirst($type) }}
                </x-filament::badge>
                @endif
            </div>
        </div>
    </div>

    <!-- Extracted Criteria -->
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Extracted Requirements</h4>
        @if($this->activeThread()->extracted_criteria)
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden shadow-sm">
            <div class="divide-y divide-gray-100 dark:divide-white/5 grid grid-cols-1">
                @php
                $expectedKeys = [
                'property_type' => 'heroicon-o-tag',
                'city' => 'heroicon-o-map-pin',
                'budget_max' => 'heroicon-o-banknotes',
                'bedrooms' => 'heroicon-o-home',
                ];
                $criteria = $this->activeThread()->extracted_criteria ?? [];
                @endphp

                @foreach($expectedKeys as $key => $icon)
                @php $value = $criteria[$key] ?? null; @endphp
                <div class="flex flex-col gap-1.5 p-3.5 text-sm hover:bg-gray-50 dark:hover:bg-white/[0.02] transition-colors">
                    <span class="text-gray-500 capitalize flex items-center gap-1.5 text-xs font-medium">
                        <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 opacity-70" />
                        {{ str_replace('_', ' ', $key) }}
                    </span>

                    @if($value !== null)
                    <span class="font-semibold text-gray-900 dark:text-gray-100 text-base">
                        {{ $key === 'budget_max' ? '$' . number_format($value) : ucfirst((string)$value) }}
                    </span>
                    @else
                    <span class="self-start text-xs font-medium px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 dark:bg-white/5 dark:text-gray-400 italic mt-0.5">
                        Not specified
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-sm text-gray-400 italic bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-dashed border-gray-200 dark:border-white/10">No criteria extracted yet.</p>
        @endif
    </div>
</div>
