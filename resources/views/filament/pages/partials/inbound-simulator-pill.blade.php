<div class="fixed bottom-5 left-1/2 -translate-x-1/2 z-40 pointer-events-auto">
    <button
        type="button"
        @click="$dispatch('open-modal', { id: 'inbound-simulator-modal' })"
        class="group flex items-center gap-3 px-4 py-2 rounded-full border border-gray-200/80 dark:border-white/10 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md shadow-2xl hover:shadow-amber-500/10 hover:border-amber-500/40 dark:hover:border-amber-500/40 transition-all duration-200 hover:scale-[1.03] active:scale-[0.98] cursor-pointer"
    >
        <!-- Radar Pulse Indicator -->
        <div class="flex items-center gap-2">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
            </span>
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Live Webhook Pipeline</span>
        </div>

        <!-- Divider -->
        <span class="text-gray-300 dark:text-gray-700 select-none">&bull;</span>

        <!-- Action Trigger with Amber Accents -->
        <div class="flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400 group-hover:text-amber-500">
            <x-heroicon-m-bolt class="w-4 h-4 group-hover:animate-bounce" />
            <span>Simulate Inbound Lead (Webhook)</span>
            <x-heroicon-m-arrow-up-right class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
        </div>
    </button>
</div>
