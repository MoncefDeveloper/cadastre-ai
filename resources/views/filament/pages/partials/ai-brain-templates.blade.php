<div x-show="aiBrainTab === 'templates'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-4 pb-6">

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
            <!-- Interactive Card with Smooth Inline Blueprint Accordion -->
            <div x-data="{ expanded: false }" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl overflow-hidden shadow-xs hover:shadow-md transition-shadow">

                <div class="p-4">
                    <!-- Top Row: Category Badge (size="md") & Channel Icon -->
                    <div class="flex items-center justify-between gap-2 mb-2">
                        @php
                            $catColor = $template->category?->color ?? '#64748b';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold"
                              style="background-color: {{ $catColor }}18; color: {{ $catColor }}; border: 1px solid {{ $catColor }}30;">
                            {{ $template->category?->name ?? 'Global Template' }}
                        </span>

                        <div class="shrink-0">
                            @if($template->channel->value === 1)
                                <x-heroicon-m-envelope class="w-4 h-4 text-gray-400" title="Email Template" />
                            @elseif($template->channel->value === 2)
                                <x-heroicon-m-chat-bubble-left-right class="w-4 h-4 text-emerald-500" title="WhatsApp Template" />
                            @endif
                        </div>
                    </div>

                    <!-- Template Title & Inline Toggle Button -->
                    <div class="mb-2">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white leading-tight flex items-center justify-between gap-2">
                            <span class="truncate">{{ $template->name }}</span>

                            <button type="button"
                                    @click="expanded = !expanded"
                                    class="text-gray-400 hover:text-primary-500 p-1 rounded-md transition-colors shrink-0"
                                    :title="expanded ? 'Hide Blueprint' : 'View Blueprint'">
                                <x-heroicon-m-information-circle class="w-4 h-4" />
                            </button>
                        </h4>
                        <p class="text-xs text-gray-500 truncate mt-1">{{ $template->prompt }}</p>
                    </div>

                    <!-- Expandable Inline Blueprint Drawer (Works on Mobile & Desktop) -->
                    <div x-show="expanded" x-collapse x-cloak class="my-3 p-3 rounded-lg bg-gray-50 dark:bg-white/[0.03] border border-gray-200 dark:border-white/5 text-xs space-y-2">
                        <div>
                            <span class="font-bold uppercase text-[10px] text-primary-600 dark:text-primary-400 tracking-wider">System Identity:</span>
                            <p class="italic text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">{{ $template->system_instructions ?? 'Standard Real Estate Advisor' }}</p>
                        </div>
                        <div>
                            <span class="font-bold uppercase text-[10px] text-primary-600 dark:text-primary-400 tracking-wider">Prompt Blueprint:</span>
                            <p class="text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">{{ $template->prompt }}</p>
                        </div>
                    </div>

                    <!-- Bottom Bar: Usage Metric & Apply Button -->
                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-white/5 mt-2">
                        <span class="text-[10px] font-medium text-gray-500 bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md flex items-center gap-1.5 whitespace-nowrap">
                            <x-heroicon-m-fire class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                            Used {{ $template->usage_count }} times
                        </span>

                        <x-filament::button wire:click="applyTemplate({{ $template->id }})" size="xs" color="primary" class="!px-3 shrink-0">
                            Apply
                        </x-filament::button>
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

    <!-- Full-Width Outlined Action Button to Manage & Add Templates -->
    @can('ViewAny:Template')
    <div class="pt-2">
        <x-filament::button
            tag="a"
            href="{{ \App\Filament\Resources\Templates\TemplateResource::getUrl('index') }}"
            target="_blank"
            color="primary"
            outlined
            size="sm"
            icon="heroicon-m-plus"
            class="w-full justify-center">
            Manage & Add Templates
        </x-filament::button>
    </div>
    @endcan

</div>
