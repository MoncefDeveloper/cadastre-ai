<!-- ONE STRICT ROOT ELEMENT FOR LIVEWIRE TO TRACK -->
<div class="flex flex-col h-full w-full" wire:key="ai-brain-wrapper-{{ $this->activeThreadId ?? 'empty' }}">
    @if($this->activeThread())
    <!-- Overlay Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 shrink-0">
        <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2">
            Copilot Dashboard
        </h3>
        <button @click="aiBrainOpen = false" class="p-1 rounded-md text-gray-400 hover:text-gray-900 hover:bg-gray-100 dark:hover:text-white dark:hover:bg-white/10 transition-colors">
            <x-heroicon-o-x-mark class="w-5 h-5" />
        </button>
    </div>

    <!-- Custom Alpine Tabs Navigation (Accessible High-Contrast Tokens) -->
    <div class="flex border-b border-gray-200 dark:border-white/10 px-4 pt-2 gap-6 shrink-0 bg-white dark:bg-gray-900 overflow-x-auto no-scrollbar"
        x-on:jump-to-ai-tab.window="aiBrainTab = 'ai'">
        <button @click="aiBrainTab = 'info'" :class="aiBrainTab === 'info' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border-b-2 border-transparent'" class="pb-2.5 text-sm transition-colors flex items-center gap-1.5 whitespace-nowrap">
            <x-heroicon-o-user class="w-4 h-4" /> Info
        </button>
        <button @click="aiBrainTab = 'ai'" :class="aiBrainTab === 'ai' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border-b-2 border-transparent'" class="pb-2.5 text-sm transition-colors flex items-center gap-1.5 whitespace-nowrap">
            <x-heroicon-o-cpu-chip class="w-4 h-4" /> AI Insights
        </button>
        <button @click="aiBrainTab = 'properties'" :class="aiBrainTab === 'properties' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border-b-2 border-transparent'" class="pb-2.5 text-sm transition-colors flex items-center gap-1.5 whitespace-nowrap">
            <x-heroicon-o-home-modern class="w-4 h-4" /> Properties
        </button>
        @can('apply_legally_binding_templates')
        <button @click="aiBrainTab = 'templates'" :class="aiBrainTab === 'templates' ? 'border-b-2 border-primary-500 text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 border-b-2 border-transparent'" class="pb-2.5 text-sm transition-colors flex items-center gap-1.5 whitespace-nowrap">
            <x-heroicon-o-document-duplicate class="w-4 h-4" /> Templates
        </button>
        @endcan
    </div>

    <!-- Scrollable Content Area -->
    <div class="flex-1 overflow-y-auto p-5 space-y-6 relative">
        @include('filament.pages.partials.ai-brain-info')
        @include('filament.pages.partials.ai-brain-insights')
        @include('filament.pages.partials.ai-brain-properties')
        @include('filament.pages.partials.ai-brain-templates')
    </div>
    @endif
</div>
