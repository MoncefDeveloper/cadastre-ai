<div x-show="aiBrainTab === 'templates'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 pb-10">

    <!-- Filters -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <x-filament::input.wrapper prefix-icon="heroicon-m-tag">
            <x-filament::input.select wire:model.live="templateCategoryFilter">
                <option value="">All Categories</option>
                @foreach($this->templateCategories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>

        <x-filament::input.wrapper prefix-icon="heroicon-m-signal">
            <x-filament::input.select wire:model.live="templateChannelFilter">
                <option value="1">Email</option>
                <option value="2">WhatsApp</option>
                <option value="3">Webform</option>
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>

    <!-- Templates List -->
    <div class="space-y-3">
        @forelse($this->availableTemplates as $template)
            <!-- Tooltip Wrapper (Alpine) - Added dynamic z-index so hover overlaps elements below -->
            <div x-data="{ showPreview: false }" class="relative" @mouseenter="showPreview = true" @mouseleave="showPreview = false" :class="{ 'z-50': showPreview, 'z-10': !showPreview }">

                <!-- The Card -->
                <div class="flex bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">

                    <!-- Left Color Block (Category) - Fixed Text Orientation -->
                    <div class="w-8 flex items-center justify-center bg-{{ $template->category?->color ?? 'gray' }}-50 dark:bg-{{ $template->category?->color ?? 'gray' }}-900/30 border-r border-gray-100 dark:border-white/5 shrink-0 py-3">
                        <span class="text-[9px] font-bold text-{{ $template->category?->color ?? 'gray' }}-600 dark:text-{{ $template->category?->color ?? 'gray' }}-400 uppercase tracking-widest truncate max-h-full" style="writing-mode: vertical-lr; transform: rotate(180deg);">
                            {{ $template->category?->name ?? 'Global' }}
                        </span>
                    </div>

                    <!-- Right Content -->
                    <div class="p-3 flex-1 flex flex-col justify-between min-w-0">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-tight flex items-center gap-1.5 cursor-help truncate">
                                    <span class="truncate">{{ $template->name }}</span>
                                    <x-heroicon-o-information-circle class="w-4 h-4 text-gray-400 hover:text-primary-500 transition-colors shrink-0" />
                                </h4>
                                <!-- Channel Icon -->
                                <div class="shrink-0 ml-2">
                                    @if($template->channel->value === 1)
                                        <x-heroicon-m-envelope class="w-4 h-4 text-gray-400" title="Email Template" />
                                    @elseif($template->channel->value === 2)
                                        <x-heroicon-m-chat-bubble-left-right class="w-4 h-4 text-green-500" title="WhatsApp Template" />
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 truncate">{{ $template->prompt }}</p>
                        </div>

                        <!-- Fixed Badge Wrapping -->
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-50 dark:border-white/5">
                            <span class="text-[10px] font-medium text-gray-500 bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md flex items-center gap-1.5 whitespace-nowrap">
                                <x-heroicon-m-fire class="w-3 h-3 text-orange-500 shrink-0" />
                                Used {{ $template->usage_count }} times
                            </span>

                            <x-filament::button wire:click="applyTemplate({{ $template->id }})" size="xs" color="primary" class="!px-3 shrink-0 ml-2">
                                Apply
                            </x-filament::button>
                        </div>
                    </div>
                </div>

                <!-- Hover Preview Popover (Fixed Position to drop down) -->
                <div x-show="showPreview" x-transition.opacity.duration.200ms x-cloak class="absolute left-0 top-full mt-2 w-full bg-gray-900 dark:bg-gray-800 text-white text-xs rounded-xl p-4 shadow-2xl ring-1 ring-white/10 pointer-events-none">
                    <!-- Arrow pointer -->
                    <div class="absolute -top-1.5 left-8 w-3 h-3 bg-gray-900 dark:bg-gray-800 rotate-45 ring-1 ring-white/10 ring-b-0 ring-r-0"></div>

                    <div class="relative z-10">
                        <p class="font-bold text-primary-400 mb-1 uppercase tracking-wider text-[10px]">System Instructions:</p>
                        <p class="mb-3 italic text-gray-300 leading-relaxed">{{ str($template->system_instructions)->limit(120) }}</p>

                        <p class="font-bold text-primary-400 mb-1 uppercase tracking-wider text-[10px]">Prompt Rules:</p>
                        <p class="text-gray-300 leading-relaxed">{{ str($template->prompt)->limit(180) }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-400 border border-dashed border-gray-200 dark:border-white/10 rounded-xl">
                <x-heroicon-o-document-duplicate class="w-8 h-8 mx-auto mb-2 opacity-50" />
                <p class="text-sm">No templates match these filters.</p>
            </div>
        @endforelse
    </div>
</div>
