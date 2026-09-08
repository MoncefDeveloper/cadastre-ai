<x-filament-panels::page>
    <style>
        .composer-container .tiptap,
        .composer-container trix-editor {
            min-height: 100px !important;
        }

        .dark .ai-generated-prose [style] {
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #e5e7eb !important;
        }

        .dark .ai-generated-prose h1,
        .dark .ai-generated-prose h2,
        .dark .ai-generated-prose h3,
        .dark .ai-generated-prose h4 {
            color: #f9fafb !important;
        }

        .dark .ai-generated-prose a {
            color: #60a5fa !important;
        }

        .ai-generated-prose div[style*="border"] {
            padding: 1.25rem !important;
            border-radius: 0.75rem !important;
            margin: 1rem 0 !important;
        }
    </style>

    <!-- App Container -->
 <div
        x-data="{ sidebarOpen: true, aiBrainOpen: false, aiBrainTab: 'info' }"
        x-on:open-ai-tab.window="aiBrainOpen = true; aiBrainTab = 'ai'"
        class="flex w-full gap-4 h-[calc(100vh-8rem)] relative">
        <!-- COLUMN 1: INBOX LIST (Absolute on mobile, relative on large screens) -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-full"
            class="absolute lg:relative z-30 inset-y-0 left-0 w-80 lg:w-1/4 lg:min-w-[300px] flex flex-col bg-white dark:bg-gray-900 rounded-xl shadow-2xl lg:shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
            @include('filament.pages.partials.inbox-list')
        </div>

        <!-- COLUMN 2: CHAT & COMPOSER -->
        <div class="flex-1 flex flex-col bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl relative overflow-hidden">
            @include('filament.pages.partials.chat-thread')

            <!-- COLUMN 3: AI BRAIN (Absolute overlay for all screens, responsive width) -->
            <div
                x-show="aiBrainOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-12 scale-95"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 translate-x-12 scale-95"
                class="absolute inset-0 md:inset-auto md:right-4 md:top-4 md:bottom-4 md:w-[430px] xl:w-[480px] z-50 flex flex-col bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-2xl shadow-2xl ring-1 ring-gray-200 dark:ring-white/10 overflow-hidden">
                @include('filament.pages.partials.ai-brain')
            </div>
        </div>

        <!-- Mobile Overlay Backdrop for Column 1 -->
        <div
            x-show="sidebarOpen"
            @click="sidebarOpen = false"
            x-transition.opacity
            class="absolute inset-0 z-20 bg-gray-900/50 backdrop-blur-sm lg:hidden"></div>
    </div>
    @include('filament.pages.partials.sandbox-welcome-modal')

    @include('filament.pages.partials.inbound-simulator-modal')

</x-filament-panels::page>
