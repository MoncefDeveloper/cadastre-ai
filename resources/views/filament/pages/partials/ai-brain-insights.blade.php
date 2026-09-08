<div x-show="aiBrainTab === 'ai'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5" @if($isGeneratingDraft) wire:poll.2s="checkDraftStatus" @endif>

    <!-- Gradient Summary Box -->
    <div class="rounded-xl p-[1px] bg-gradient-to-r from-purple-300 to-pink-300 dark:from-purple-700/50 dark:to-pink-700/50 shadow-sm">
        <div class="rounded-[11px] bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-900 dark:to-gray-900 p-4 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/10 to-pink-900/10 hidden dark:block pointer-events-none"></div>

            <div class="relative z-10">
                <h4 class="flex items-center gap-1.5 text-purple-700 dark:text-purple-400 font-bold text-xs uppercase tracking-wider mb-2">
                    <x-heroicon-s-sparkles class="w-4 h-4" />
                    AI Summary
                </h4>
                <p class="text-sm text-gray-800 dark:text-gray-300 leading-relaxed">
                    Client is inquiring about a {{ $this->activeThread()->extracted_criteria['property_type'] ?? 'property' }} in {{ $this->activeThread()->extracted_criteria['city'] ?? 'an unspecified location' }}. Their maximum budget is ${{ number_format($this->activeThread()->extracted_criteria['budget_max'] ?? 0) }}.
                </p>
            </div>
        </div>
    </div>

    <!-- Suggested Reply Card -->
    <div>
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Suggested Reply</h4>

        @if($isGeneratingDraft)
        <div class="relative p-[1px] rounded-xl bg-gradient-to-r from-purple-500 via-pink-500 to-amber-500 animate-pulse shadow-sm">
            <div class="bg-white dark:bg-gray-900 rounded-xl p-8 flex flex-col items-center justify-center text-center">
                <x-heroicon-s-sparkles class="w-8 h-8 text-purple-500 mb-3 animate-bounce" />
                <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">AI is writing...</h4>
                <p class="text-xs text-gray-500 mb-5">Analyzing context and matching properties</p>

                <div class="w-full max-w-xs" x-data="{ progress: 0 }" x-init="setInterval(() => { progress = Math.min(95, progress + Math.floor(Math.random() * 10) + 1) }, 500)">
                    <div class="flex justify-between text-[10px] font-semibold text-gray-400 mb-1">
                        <span>Generating Response</span>
                        <span x-text="progress + '%'"></span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>
        </div>
        @elseif($activeDraft)
        <div wire:key="ai-draft-active-{{ $activeDraft->id }}" class="border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden shadow-sm bg-white dark:bg-gray-900 flex flex-col">
            <div class="p-5 text-sm text-gray-700 dark:text-gray-300 prose prose-sm dark:prose-invert max-w-none ai-generated-prose flex-1">
                {!! $activeDraft->body_html !!}
            </div>
            <div class="p-3 bg-gray-50 dark:bg-white/5 border-t border-gray-200 dark:border-white/10 flex justify-end gap-2 shrink-0">
                <x-filament::button wire:click="regenerateDraft" color="gray" variant="outline" size="sm" icon="heroicon-m-arrow-path" class="!border-gray-200 dark:!border-white/10">Regenerate</x-filament::button>
                <x-filament::button wire:click="copyAiDraftToComposer" @click="aiBrainOpen = false" color="warning" size="sm" icon="heroicon-m-clipboard-document-check">Copy to Composer</x-filament::button>
            </div>
        </div>
        @else
        <div wire:key="ai-draft-empty-{{ $this->activeThreadId }}" class="p-5 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-white/5 text-center flex flex-col items-center justify-center">
            <x-heroicon-o-document-text class="w-8 h-8 text-gray-400 mb-2" />
            <p class="text-sm text-gray-500 dark:text-gray-400">No draft has been generated yet.</p>
            <x-filament::button wire:click="regenerateDraft" color="gray" variant="outline" size="xs" class="mt-3">Generate Draft</x-filament::button>
        </div>
        @endif
    </div>

    <!-- NEW SEPARATE AI MODIFIERS SECTION -->
    @if($activeDraft && !$isGeneratingDraft && $this->activeModifiers->count() > 0)
    <div wire:key="ai-modifiers-section">
        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">✨ Refine Draft</h4>
        <div class="p-4 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm bg-white dark:bg-gray-900 flex flex-wrap gap-2">
            @foreach($this->activeModifiers as $modifier)
                <button type="button" wire:click="applyAiModifier({{ $modifier->id }})" class="focus:outline-none hover:scale-105 transition-transform active:scale-95" title="{{ $modifier->instruction }}">
                    <x-filament::badge color="{{ $modifier->color }}" size="md">
                        {{ $modifier->label }}
                    </x-filament::badge>
                </button>
            @endforeach
        </div>
    </div>
    @endif

    @can('apply_legally_binding_templates')
    <div class="pt-2">
        <button type="button" @click="aiBrainTab = 'templates'" class="w-full flex items-center justify-between p-4 bg-white hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl transition-all shadow-sm group">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg">
                    <x-heroicon-o-document-duplicate class="w-5 h-5" />
                </div>
                <div class="text-left">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">Browse Response Templates</h4>
                    <p class="text-xs text-gray-500">Apply a specific strategy to this client</p>
                </div>
            </div>
            <x-heroicon-m-chevron-right class="w-5 h-5 text-gray-400 group-hover:text-primary-500 transition-colors" />
        </button>
    </div>
    @endcan

</div>
