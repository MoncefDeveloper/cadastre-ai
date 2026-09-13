<!-- Header & Actions -->
<div class="flex items-center justify-between p-4 shrink-0">
    <div class="flex items-center gap-3">
        <h2 class="font-bold text-xl text-gray-900 dark:text-white tracking-tight">Inbox</h2>
    </div>

    <div class="flex items-center gap-2">
        <button
            wire:click="toggleSort"
            class="flex items-center justify-center w-7 h-7 rounded-md border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors shadow-sm"
            title="Sort by Date">
            <x-dynamic-component :component="$sortDesc ? 'heroicon-o-bars-arrow-down' : 'heroicon-o-bars-arrow-up'" class="w-4 h-4"
                wire:loading.remove
                wire:target="toggleSort" />
            <x-filament::loading-indicator
                wire:loading
                wire:target="toggleSort"
                class="w-4 h-4 text-gray-400" />
        </button>

        <button
            @click="sidebarOpen = false"
            class="flex items-center justify-center w-7 h-7 rounded-md border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors shadow-sm"
            title="Collapse Inbox">
            <x-heroicon-o-chevron-double-left class="w-4 h-4" />
        </button>
    </div>
</div>

<!-- Search Bar (Live Debounce for performance) -->
<div class="px-4 py-3 border-t border-gray-200 dark:border-white/10 shrink-0">
    <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
        <x-filament::input
            type="search"
            wire:model.live.debounce.300ms="searchQuery"
            placeholder="Search emails or clients..." />
    </x-filament::input.wrapper>
</div>

<!-- Tabs: Brand Primary Active State + Amber Alert for Unread -->
<div class="grid grid-cols-3 gap-3.5 p-4 border-y border-gray-200 dark:border-white/10 shrink-0">
    <!-- 'All' Filter -->
    <div class="relative">
        <x-filament::button
            wire:click="setTab('all')"
            size="xs"
            icon="heroicon-m-inbox-stack"
            :color="$activeTab === 'all' ? 'primary' : 'gray'"
            :outlined="$activeTab !== 'all'"
            class="w-full justify-center">
            All
        </x-filament::button>
        <span class="absolute -top-1.5 -right-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-500 px-1 text-[9px] font-extrabold text-white shadow-sm ring-2 ring-white dark:ring-gray-900 pointer-events-none">
            {{ $this->allCount }}
        </span>
    </div>

    <!-- 'Unread' Filter (Amber High-Visibility Action Beacon) -->
    <div class="relative">
        <x-filament::button
            wire:click="setTab('unread')"
            size="xs"
            icon="heroicon-m-envelope"
            :color="$activeTab === 'unread' ? 'primary' : 'gray'"
            :outlined="$activeTab !== 'unread'"
            class="w-full justify-center">
            Unread
        </x-filament::button>
        @if($this->unreadCount > 0)
        <span class="absolute -top-1.5 -right-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-warning-500 px-1 text-[9px] font-extrabold text-white shadow-sm ring-2 ring-white dark:ring-gray-900 pointer-events-none animate-pulse">
            {{ $this->unreadCount }}
        </span>
        @endif
    </div>

    <!-- 'Read' Filter -->
    <div class="relative">
        <x-filament::button
            wire:click="setTab('read')"
            size="xs"
            icon="heroicon-m-envelope-open"
            :color="$activeTab === 'read' ? 'primary' : 'gray'"
            :outlined="$activeTab !== 'read'"
            class="w-full justify-center">
            Read
        </x-filament::button>
        <span class="absolute -top-1.5 -right-1.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-gray-500 dark:bg-gray-600 px-1 text-[9px] font-extrabold text-white shadow-sm ring-2 ring-white dark:ring-gray-900 pointer-events-none">
            {{ $this->readCount }}
        </span>
    </div>
</div>

<!-- Scrollable List -->
<div wire:poll.10s class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-white/5">
    @forelse($this->threads as $thread)
    <button
        type="button"
        wire:key="thread-{{ $thread->id }}"
        wire:click="loadThread({{ $thread->id }})"
        wire:loading.attr="disabled"
        class="w-full text-left group relative flex gap-4 p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-white/5 transition-colors {{ $activeThreadId === $thread->id ? 'bg-primary-50/70 dark:bg-primary-500/15' : '' }} disabled:opacity-50 disabled:cursor-not-allowed">

        @if($activeThreadId === $thread->id)
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary-600 dark:bg-primary-400 rounded-r-full"></div>
        @endif

        <!-- Avatar (5-Palette Contrast Compliant) -->
        <div class="relative shrink-0 mt-1">
            <div class="flex items-center justify-center w-10 h-10 rounded-full font-bold text-sm {{ $this->getAvatarColor($thread->client->id) }}">
                {{ strtoupper(substr($thread->client->first_name, 0, 1)) }}{{ strtoupper(substr($thread->client->last_name ?? '', 0, 1)) }}
            </div>
            <div class="absolute -bottom-1 -right-1 bg-white dark:bg-gray-900 rounded-full p-0.5 shadow-sm">
                <x-heroicon-s-envelope class="w-3.5 h-3.5 text-gray-400" />
            </div>
        </div>

        <!-- Content -->
        <div class="flex-1 min-w-0">
            <div class="flex justify-between items-baseline mb-0.5">
                <h3 class="truncate pr-2 text-sm {{ $thread->is_unread ? 'font-bold text-gray-900 dark:text-white' : 'font-medium text-gray-700 dark:text-gray-300' }}"
                    title="{{ $thread->client->first_name }} {{ $thread->client->last_name }}">
                    {{ $thread->client->first_name }} {{ $thread->client->last_name }}
                </h3>
                <span class="shrink-0 text-[11px] text-gray-500 whitespace-nowrap">
                    {{ $thread->last_message_at?->format('M d') }}
                </span>
            </div>

            <div class="flex items-center justify-between gap-2">
                <p class="text-[13px] leading-tight truncate {{ $thread->is_unread ? 'font-semibold text-gray-800 dark:text-gray-200' : 'text-gray-500 dark:text-gray-400' }}"
                   title="{{ $thread->subject ?? 'No Subject' }}">
                    {{ str($thread->subject ?? 'No Subject')->limit(30) }}
                </p>

                <!-- Unread Indicator (Amber Pulse Beacon) -->
                @if($thread->is_unread)
                <div class="shrink-0 w-2 h-2 rounded-full bg-primary-600 shadow-[0_0_8px_rgba(var(--primary-600),0.6)]"></div>
                @endif
            </div>
        </div>
    </button>
    @empty
    <div class="flex flex-col items-center justify-center h-48 text-gray-400">
        <x-heroicon-o-inbox class="w-10 h-10 mb-3 opacity-30" />
        <p class="text-sm">No conversations found.</p>
    </div>
    @endforelse
</div>

<!-- Pagination Footer (Docked at bottom) -->
@if($this->threads->hasPages())
<div class="p-3 border-t border-gray-200 dark:border-white/10 shrink-0">
    <x-filament::pagination :paginator="$this->threads" />
</div>
@endif
