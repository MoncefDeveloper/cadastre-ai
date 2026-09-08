<!-- ONE STRICT ROOT ELEMENT FOR LIVEWIRE TO TRACK -->
<div class="flex flex-col flex-1 h-full w-full relative">

    <div wire:loading.flex wire:target="loadThread" class="absolute inset-0 z-50 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm items-center justify-center rounded-xl">
        <x-filament::loading-indicator class="w-10 h-10 text-primary-500" />
    </div>

    @if($this->activeThread())
    <div wire:key="chat-view-{{ $this->activeThreadId }}" class="flex flex-col flex-1 h-full w-full">

        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 z-10 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <button
                    x-show="!sidebarOpen"
                    @click="sidebarOpen = true"
                    x-transition:enter="transition ease-out duration-300 delay-200"
                    x-transition:enter-start="opacity-0 scale-75 -translate-x-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-x-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-x-0"
                    x-transition:leave-end="opacity-0 scale-75 -translate-x-4"
                    class="shrink-0 flex items-center justify-center w-8 h-8 rounded-md border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors shadow-sm"
                    title="Open Inbox"
                    type="button">
                    <x-heroicon-o-bars-3 class="w-4 h-4" />
                </button>
                <div class="min-w-0 flex-1">
                    <h2 class="font-bold text-base text-gray-900 dark:text-white leading-tight truncate">
                        {{ $this->activeThread()->subject }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        {{ $this->activeThread()->client->first_name }} <span class="opacity-70">&lt;{{ $this->activeThread()->client->email }}&gt;</span>
                    </p>
                </div>
            </div>

            <!-- DESKTOP ACTIONS (>= 1280px) -->
            <!-- DESKTOP ACTIONS (>= 1280px) — Fully Unified Neutral & Primary Styling -->
            <div class="hidden xl:flex items-center gap-2 shrink-0 ml-4">

                <!-- 1. Unread Button -->
                <button
                    type="button"
                    wire:click="markAsUnread"
                    class="group flex items-center gap-1.5 h-9 px-3 rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-300 dark:hover:border-primary-800/50 transition-all shadow-sm text-xs font-bold">
                    <x-heroicon-m-envelope class="w-4 h-4 text-gray-400 group-hover:text-primary-500 transition-colors" />
                    <span>Unread</span>
                </button>

                <!-- 2. AI Button -->
                <button
                    type="button"
                    @click="aiBrainOpen = true; aiBrainTab = 'ai'"
                    class="group flex items-center gap-1.5 h-9 px-3 rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-300 dark:hover:border-primary-800/50 transition-all shadow-sm text-xs font-bold"
                    title="Open AI Copilot Insights">
                    <x-heroicon-o-cpu-chip class="w-4 h-4 text-primary-500 group-hover:animate-pulse" />
                    <span>AI</span>
                </button>

                <!-- 3. Client Info Button -->
                <button
                    type="button"
                    @click="aiBrainOpen = true; aiBrainTab = 'info'"
                    class="group flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-300 dark:hover:border-primary-800/50 transition-all shadow-sm"
                    title="Client Info">
                    <x-heroicon-o-user class="w-4 h-4 text-gray-400 group-hover:text-primary-500 transition-colors" />
                </button>

                <!-- 4. 👋 Waving Hand Guide Trigger -->
                <button
                    type="button"
                    @click="$dispatch('open-modal', { id: 'sandbox-welcome-modal' })"
                    class="group relative flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-300 dark:hover:border-primary-800/50 transition-all shadow-sm"
                    title="Testing Guide & Sandbox Architecture">
                    <x-heroicon-o-hand-raised class="w-4 h-4 text-amber-500 animate-waving-hand group-hover:scale-110" />

                    <!-- Subtle Pulsing Live Indicator -->
                    <span class="absolute -top-0.5 -right-0.5 flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                </button>

            </div>

            <!-- RESPONSIVE MOBILE DROPDOWN (< 1280px) -->
            <div class="xl:hidden shrink-0 ml-2">
                <x-filament::dropdown placement="bottom-end">
                    <x-slot name="trigger">
                        <x-filament::icon-button icon="heroicon-m-ellipsis-vertical" color="gray" />
                    </x-slot>
                    <x-filament::dropdown.list>
                        <x-filament::dropdown.list.item wire:click="markAsUnread" icon="heroicon-m-envelope">
                            Mark Unread
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item @click="aiBrainOpen = true; aiBrainTab = 'ai'" icon="heroicon-m-cpu-chip">
                            AI Insights
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item @click="aiBrainOpen = true; aiBrainTab = 'info'" icon="heroicon-m-user">
                            Client Info
                        </x-filament::dropdown.list.item>
                        <x-filament::dropdown.list.item @click="$dispatch('open-modal', { id: 'sandbox-welcome-modal' })" icon="heroicon-m-hand-raised">
                            Testing Guide
                        </x-filament::dropdown.list.item>
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            </div>
        </div>

        <!-- Chat History (Email Cards) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50/50 dark:bg-[#0f1115]">
            @foreach($this->activeThread()->messages as $message)
            @if($message->is_draft) @continue @endif

            <div wire:key="msg-{{ $message->id }}" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm overflow-hidden">
                <div class="flex justify-between items-start p-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/30 dark:bg-white/[0.02]">
                    <div class="flex items-center gap-3">
                        @if($message->direction->value === 1)
                        <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold text-xs {{ $this->getAvatarColor($this->activeThread()->client->id) }}">
                            {{ strtoupper(substr($this->activeThread()->client->first_name, 0, 1)) }}
                        </div>
                        @else
                        <div class="flex items-center justify-center w-8 h-8 rounded-full font-bold text-xs bg-gray-800 text-white dark:bg-gray-100 dark:text-gray-900">You</div>
                        @endif
                        <div class="leading-tight">
                            <p class="font-bold text-sm text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                {{ $message->direction->value === 1 ? $this->activeThread()->client->first_name : 'You' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                to {{ $message->direction->value === 1 ? 'You' : $this->activeThread()->client->first_name }}
                            </p>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 whitespace-nowrap">{{ $message->created_at->format('M d, g:i A') }}</div>
                </div>
                <div class="p-5 text-sm text-gray-700 dark:text-gray-300 w-full overflow-hidden"
                    x-data="{ html: @js($message->body_html ?? nl2br(e($message->body_text))) }"
                    x-init="
                        const shadow = $el.attachShadow({ mode: 'open' });
                        shadow.innerHTML = `
                            <style>
                                :host { display: block; font-family: inherit; color: inherit; line-height: 1.6; }
                                div, p, span, font, td, th {
                                    color: inherit !important;
                                    font-family: inherit !important;
                                }
                                p { margin-top: 0; margin-bottom: 1em; }
                                a { color: #3b82f6 !important; text-decoration: underline !important; }
                                ul, ol { margin-top: 0; margin-bottom: 1em; padding-left: 20px; }
                                h1, h2, h3, h4, h5, h6 { font-weight: bold; margin-bottom: 0.5em; }
                                img { max-width: 100%; height: auto; display: block; }
                            </style>
                            ` + html;
                     ">
                </div>
            </div>
            @endforeach
        </div>

        <!-- Dynamic Border UI -->
        <div
            x-data="{
                composerHeight: null,
                dragging: false,
                isAuditing: false
            }"
            x-on:trigger-grade-draft.window="isAuditing = true; $wire.rateCurrentDraft().finally(() => isAuditing = false)"
            :style="
                `max-height: 45vh;` +
                (composerHeight ? `height: ${composerHeight}px;` : 'height: auto;') +
                (@js($ratingResult) ? `border: 2px solid hsl(${$wire.ratingResult.score * 12}, 75%, 45%) !important;` : '')
            "
            class="composer-container bg-white dark:bg-gray-900 shadow-[0_-4px_10px_rgba(0,0,0,0.02)] flex flex-col relative shrink-0 overflow-hidden {{ !$ratingResult ? 'border-t border-gray-200 dark:border-white/10' : 'rounded-b-xl' }}">

            <!-- 🔄 Blocking & Loading Overlay (Triggers from BOTH native click and toast notification) -->
            <div
                x-show="isAuditing"
                x-cloak
                wire:loading.flex
                wire:target="rateCurrentDraft"
                class="absolute inset-0 z-40 bg-gray-900/10 dark:bg-black/20 backdrop-blur-sm flex items-center justify-center cursor-not-allowed transition-opacity">
                <div class="flex items-center gap-2 bg-white dark:bg-gray-800 px-4 py-2 rounded-full shadow-lg ring-1 ring-gray-900/5 dark:ring-white/10">
                    <x-filament::loading-indicator class="w-5 h-5 text-primary-500" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Auditing Compliance...</span>
                </div>
            </div>

            <div @mousedown="if (!composerHeight) { composerHeight = $el.parentElement.offsetHeight; } dragging = true; document.body.style.cursor = 'row-resize'; $event.preventDefault();" @mouseup.window="if(dragging) { dragging = false; document.body.style.cursor = 'default'; }" @mousemove.window="if(dragging) { composerHeight = Math.max(140, composerHeight - $event.movementY); }" class="absolute top-0 left-0 right-0 h-4 -mt-2 cursor-row-resize z-20 flex justify-center items-center group">
                <div class="w-12 h-1 bg-gray-300 dark:bg-gray-700 rounded-full group-hover:bg-primary-500 transition-colors"></div>
            </div>

            <form wire:submit="approveAndSend"
                x-data="{
                      hasContent: false,
                      init() {
                          this.hasContent = $el.querySelector('trix-editor')?.editor?.toString().trim().length > 0;

                          this.$watch('$wire.draftData.body_html', value => {
                              if (!value) {
                                  this.hasContent = false;
                                  return;
                              }
                              const rawText = Array.isArray(value) ? value.join(' ') : String(value);
                              this.hasContent = rawText.replace(/<[^>]*>/g, '').trim().length > 0;
                          });
                      }
                  }"
                @trix-change="hasContent = $event.target.editor.toString().trim().length > 0"
                @input="hasContent = ($el.querySelector('.tiptap')?.textContent.trim().length > 0 || $el.querySelector('trix-editor')?.editor.toString().trim().length > 0)"
                class="flex flex-col h-full p-4 relative z-10">
                <div class="mb-2 shrink-0 flex items-center h-7 gap-3 pb-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Reply to {{ $this->activeThread()->client->first_name }}</span>
                    <x-filament::button outlined color="gray" size="xs" icon="heroicon-m-arrow-path" tooltip="Reset height" @click="composerHeight = null" x-show="composerHeight !== null" x-cloak class="scale-85" type="button" />

                    <!-- Header Actions Group -->
                    <div class="ml-auto flex items-center gap-2">
                        @if($ratingResult)
                        <!-- 1. Rules Button -->
                        <x-filament::button
                            outlined
                            color="gray"
                            size="xs"
                            icon="heroicon-m-book-open"
                            x-on:click="$dispatch('open-modal', { id: 'compliance-rules-modal' })"
                            type="button">
                            View Rules
                        </x-filament::button>

                        <!-- 2. Evaluation Button with High-Contrast Dynamic Badge -->
                        <x-filament::button
                            outlined
                            size="xs"
                            icon="heroicon-m-shield-check"
                            color="{{ $ratingResult['score']<1 ? 'gray' : ($ratingResult['score'] >= 8 ? 'success' : ($ratingResult['score'] >= 5 ? 'warning' : 'danger'))}}"
                            x-on:click="$dispatch('open-modal', { id: 'rate-details-modal' })"
                            type="button">
                            Evaluation
                            <x-slot name="badge">
                                {{ $ratingResult['score'] * 10 }}%
                            </x-slot>
                        </x-filament::button>
                        @endif

                        <!-- 3. Grade Draft / Re-Grade Button -->
                        <x-filament::button
                            wire:click="rateCurrentDraft"
                            wire:loading.attr="disabled"
                            wire:target="rateCurrentDraft"
                            x-bind:disabled="!hasContent || $wire.isRating"
                            tooltip="Evaluate draft against FHA & Quality Rules"
                            outlined color="info" size="xs" icon="heroicon-m-cpu-chip" class="{{ $ratingResult ? 'scale-90' : '' }}"
                            type="button">
                            {{ $ratingResult ? 'Re-Grade' : 'Grade Draft' }}
                        </x-filament::button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-1 -m-1 min-h-0">
                    {{ $this->draftForm }}
                </div>

                <div class="flex justify-between items-center mt-3 shrink-0 pt-2 border-t border-gray-100 dark:border-white/5">
                    <div class="text-xs text-gray-400 flex items-center gap-1">
                        <x-heroicon-o-sparkles class="w-3.5 h-3.5" />
                        Open 'AI Insights' to generate or paste a draft.
                    </div>
                    <x-filament::button
                        type="submit"
                        color="primary"
                        size="sm"
                        icon="heroicon-m-paper-airplane"
                        wire:loading.attr="disabled"
                        wire:target="approveAndSend, rateCurrentDraft, handleNotificationGradeDraft"
                        x-bind:disabled="!hasContent || $wire.isRating @if(!auth()->user()->can('bypass_compliance_gate')) || !$wire.ratingResult || !$wire.ratingResult.is_compliant @endif">
                        Send Reply
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- EVALUATION MODAL -->
        <x-filament::modal id="rate-details-modal" width="2xl">
            @if($ratingResult)
            <x-slot name="heading">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl text-white font-black text-xl shadow-inner" style="background-color: hsl({{ $ratingResult['score'] * 12 }}, 75%, 45%);">
                        {{ $ratingResult['grade'] }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Evaluation Results</h3>
                        <p class="text-sm font-medium text-gray-500">Score: {{ $ratingResult['score'] * 10 }}% / 100%</p>
                    </div>
                </div>
            </x-slot>

            <div class="space-y-6">
                <!-- Compliance Alert -->
                @if(!$ratingResult['is_compliant'])
                <div class="p-4 rounded-xl bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-900/50">
                    <div class="flex gap-3">
                        <x-heroicon-s-exclamation-triangle class="w-6 h-6 text-danger-600 dark:text-danger-400 shrink-0" />
                        <div>
                            <h4 class="text-sm font-bold text-danger-800 dark:text-danger-300">Compliance Violation Detected</h4>
                            <p class="text-sm text-danger-700 dark:text-danger-400 mt-1 leading-relaxed">
                                {{ $ratingResult['compliance_warning'] }}
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="p-3 rounded-lg bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-900/50 flex items-center gap-2">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-success-600 dark:text-success-400" />
                    <span class="text-sm font-medium text-success-800 dark:text-success-300">FHA Compliant - No critical violations.</span>
                </div>
                @endif

                <!-- Critique Section -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Editor's Critique</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed italic border-l-2 border-gray-300 dark:border-gray-600 pl-4">
                        "{{ $ratingResult['critique'] }}"
                    </p>
                </div>

                <!-- Summary Section -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Summary</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-white/5 p-4 rounded-xl border border-gray-200 dark:border-white/10">
                        {{ $ratingResult['summary'] }}
                    </p>
                </div>
            </div>

            <x-slot name="footerActions">
                <div class="flex items-center justify-end gap-2 w-full">
                    <x-filament::button color="gray" x-on:click="close()">
                        Close
                    </x-filament::button>

                    <!-- 🧠 Instant jump to AI Insights when a draft fails -->
                    <x-filament::button
                        color="primary"
                        icon="heroicon-m-sparkles"
                        outlined
                        x-on:click="close(); aiBrainOpen = true; aiBrainTab = 'ai'">
                        Use AI Suggested Draft
                    </x-filament::button>
                </div>
            </x-slot>
            @endif
        </x-filament::modal>

        <!-- THE EXPLANATORY MODAL (SSOT RULES DISPLAY) -->
        <x-filament::modal id="compliance-rules-modal" width="3xl" slide-over>
            <x-slot name="heading">
                Compliance & Quality Rules
            </x-slot>
            <x-slot name="description">
                These are the strict guidelines used by the AI to grade your drafts.
            </x-slot>

            <div class="space-y-4 pr-2 pb-6">
                @foreach(config('matchmaker-rules', []) as $section)
                <x-filament::section icon="heroicon-o-shield-check" collapsible>
                    <x-slot name="heading">
                        {{ $section['title'] }}
                    </x-slot>
                    <div class="space-y-4 divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($section['rules'] as $rule)
                        <div class="pt-4 first:pt-0">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ $rule['name'] }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 leading-relaxed">{{ $rule['action'] }}</p>
                            <div class="flex items-start gap-2 bg-gray-50 dark:bg-white/5 p-3 rounded-lg border border-gray-200 dark:border-white/10">
                                <x-heroicon-s-information-circle class="w-5 h-5 text-primary-500 shrink-0 mt-0.5" />
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $rule['tooltip'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </x-filament::section>
                @endforeach
            </div>

            <x-slot name="footerActions">
                <x-filament::button color="gray" x-on:click="close()" class="w-full">
                    Close
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

    </div>
    @else
    <div wire:key="chat-empty-state" class="flex-1 flex flex-col items-center justify-center text-gray-400">
        <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-full mb-4">
            <x-heroicon-o-envelope class="w-10 h-10 opacity-50" />
        </div>
        <p class="font-medium text-gray-500 dark:text-gray-400 text-sm">Select an email to view thread</p>
    </div>
    @endif
</div>
