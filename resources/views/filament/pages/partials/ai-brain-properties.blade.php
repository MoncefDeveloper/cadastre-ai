<div x-show="aiBrainTab === 'properties'" x-cloak class="space-y-4">
    <div class="p-6 bg-gradient-to-br from-primary-50/20 via-white to-gray-50/50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900/90 border border-gray-200 dark:border-white/10 rounded-2xl shadow-xs flex flex-col items-center text-center">
        <div class="w-14 h-14 rounded-2xl bg-primary-500/10 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 flex items-center justify-center mb-3 shadow-inner">
            <x-heroicon-o-home-modern class="w-8 h-8" />
        </div>
        <h4 class="text-base font-bold text-gray-900 dark:text-white mb-1.5">Autonomous Portfolio Match Engine</h4>
        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed max-w-sm mb-4">
            AI telemetry continuously cross-referencing buyer budget, preferred districts, and architectural criteria against verified active and off-market inventory.
        </p>

        @if($this->activeThread() && $this->activeThread()->propertyMatches->isNotEmpty())
            @php $topMatch = $this->activeThread()->propertyMatches->first(); @endphp
            <div class="w-full p-3.5 rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/10 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 min-w-0 pr-2">
                    <x-heroicon-s-sparkles class="w-4 h-4 text-primary-500 shrink-0" />
                    <span class="font-bold text-gray-900 dark:text-white truncate">
                        {{ $topMatch->property?->title ?? 'Curated Match' }}
                    </span>
                </div>
                <x-filament::badge color="success" size="md">
                    {{ $topMatch->match_score }}% Match
                </x-filament::badge>
            </div>
        @else
            <div class="w-full p-3 rounded-xl bg-gray-50 dark:bg-white/[0.02] border border-dashed border-gray-200 dark:border-white/10 text-xs text-gray-500">
                Awaiting criteria extraction from latest client message.
            </div>
        @endif
    </div>
</div>
