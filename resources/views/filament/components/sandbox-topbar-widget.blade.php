<div
    x-data="{
        timeRemaining: '30:00',
        init() {
            this.updateCountdown();
            setInterval(() => this.updateCountdown(), 1000);
        },
        updateCountdown() {
            const now = new Date();
            const currentMinute = now.getMinutes();
            const currentSecond = now.getSeconds();

            const remainingMinutes = 29 - (currentMinute % 30);
            const remainingSeconds = 59 - currentSecond;

            const mins = String(remainingMinutes).padStart(2, '0');
            const secs = String(remainingSeconds).padStart(2, '0');

            this.timeRemaining = `${mins}:${secs}`;
        }
    }"
    x-on:close-modal.window="if ($event.detail.id === 'sandbox-inspector-modal') $wire.set('inspectorOpen', false, false)"
    wire:poll.30s
    class="flex items-center ml-3 shrink-0">
    <!-- THE 3-PART OUTLINED CAPSULE (Responsive Margin) -->
    <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white/60 dark:bg-gray-900/60 backdrop-blur-md shadow-xs ml-3 sm:ml-5">

        <!-- 1. TITLE (Always Visible) -->
        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 whitespace-nowrap">
            Next Cleanup
        </span>

        <!-- 2. TIME CHIP -->
        <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200 dark:border-white/10 bg-gray-100/80 dark:bg-white/5 font-mono text-xs font-bold text-gray-900 dark:text-white shrink-0">
            <x-heroicon-m-clock class="w-3.5 h-3.5 text-amber-500 shrink-0" />
            <span x-text="timeRemaining"></span>
        </div>

        <!-- 3. NATIVE FILAMENT BUTTON WITH DYNAMIC STATUS COLOR & ICON -->
        <x-filament::button
            type="button"
            wire:click="openInspector"
            size="xs"
            :color="$this->statusColor"
            outlined
            :icon="$this->statusColor === 'danger' ? 'heroicon-m-exclamation-triangle' : ($this->statusColor === 'warning' ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')"
            :badge-color="$this->statusColor"
            class="shadow-xs">
            @if($this->totalPendingActionsCount > 0)
            <x-slot name="badge">
                {{ $this->totalPendingActionsCount }}
            </x-slot>
            @endif
        </x-filament::button>

    </div>

    <!-- SLIDE-OVER SANDBOX INSPECTOR MODAL -->
    <x-filament::modal id="sandbox-inspector-modal" width="3xl" slide-over>
        <x-slot name="heading">
            <div class="flex items-center gap-2.5">
                <div class="w-12 h-12 rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                    <x-heroicon-m-cpu-chip class="w-7 h-7" />
                </div>
                <div>
                    <span class="font-bold text-xl text-gray-900 dark:text-white">Live Sandbox Lifecycle Inspector</span>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Real-time telemetry of protected seed baselines and visitor data aging</p>
                </div>
            </div>
        </x-slot>

        <div class="space-y-5 pr-1 pb-4 text-gray-800 dark:text-gray-200">
            @if($this->inspectorOpen)
            <!-- SECTION A: Countdown Banner (Aligned to Primary Carmine) -->
            <x-filament::callout color="primary" icon="heroicon-o-clock">
                <x-slot name="heading">
                    <span class="font-bold text-md">Next Automatic Self-Healing Cycle</span>
                </x-slot>
                <x-slot name="description">
                    <div class="flex items-center justify-between text-xs mt-1">
                        <span>Executing in: <strong class="font-mono text-primary-600 dark:text-primary-400 text-sm" x-text="timeRemaining"></strong></span>
                        <span class="opacity-75">30-Min Sliding Window Threshold</span>
                    </div>
                </x-slot>
            </x-filament::callout>

            <!-- SECTION B: High-Level Metric Stat Cards (4 Cards) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] flex flex-col items-center text-center">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Protected Base</span>
                    <span class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $this->inspectorData['total_baseline'] }}</span>
                    <span class="text-[9px] text-gray-400">Immutable</span>
                </div>

                <div class="p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] flex flex-col items-center text-center">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Modified Base</span>
                    <span class="text-lg font-extrabold text-amber-600 dark:text-amber-400 mt-0.5">{{ $this->inspectorData['total_modified'] }}</span>
                    <span class="text-[9px] text-gray-400">Self-Healing</span>
                </div>

                <div class="p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] flex flex-col items-center text-center">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">In Grace</span>
                    <span class="text-lg font-extrabold text-amber-500 dark:text-amber-300 mt-0.5">{{ $this->inspectorData['total_grace'] }}</span>
                    <span class="text-[9px] text-gray-400">Age &lt; 30m</span>
                </div>

                <div class="p-3 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/[0.02] flex flex-col items-center text-center">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pending Purge</span>
                    <span class="text-lg font-extrabold text-rose-600 dark:text-rose-400 mt-0.5">{{ $this->inspectorData['total_expired'] }}</span>
                    <span class="text-[9px] text-gray-400">Age &ge; 30m</span>
                </div>
            </div>

            <!-- SECTION C: Model-by-Model Interactive Tree (Includes AI Modifiers) -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Model-by-Model Breakdown</h4>

                @foreach($this->inspectorData['models'] as $label => $info)
                <x-filament::section collapsible collapsed>
                    <x-slot name="heading">
                        <div class="flex items-center justify-between w-full pr-2">
                            <div class="flex items-center gap-3">
                                <x-filament::icon :icon="$info['icon']" class="w-6 h-6 text-gray-400" />
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $label }}</span>
                                <span class="text-[11px] text-gray-400 font-medium">({{ $info['limit'] }} Protected Base)</span>

                                @if(!empty($info['index_url']))
                                <a href="{{ $info['index_url'] }}"
                                    target="_blank"
                                    @click.stop
                                    class="text-gray-400 hover:text-primary-500 transition-colors p-0.5"
                                    title="Open {{ $label }} Table in new tab">
                                    <x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="w-3.5 h-3.5" />
                                </a>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5">
                                <x-filament::badge color="success" size="md">{{ $info['baseline_count'] }} Base</x-filament::badge>

                                @if(!empty($info['modified_count']) && $info['modified_count'] > 0)
                                <x-filament::badge color="warning" size="md">{{ $info['modified_count'] }} Updated</x-filament::badge>
                                @endif

                                @if($info['grace_count'] > 0)
                                <x-filament::badge color="primary" size="md">{{ $info['grace_count'] }} Active</x-filament::badge>
                                @endif
                                @if($info['expired_count'] > 0)
                                <x-filament::badge color="danger" size="md">{{ $info['expired_count'] }} Expired</x-filament::badge>
                                @endif
                            </div>
                        </div>
                    </x-slot>

                    <div class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($info['records'] as $rec)
                        <div class="py-2 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2 min-w-0 pr-2">
                                <span class="font-mono text-gray-400 text-[11px]">#{{ $rec['id'] }}</span>

                                @if(!empty($rec['url']))
                                <a href="{{ $rec['url'] }}"
                                    target="_blank"
                                    class="font-medium text-gray-800 dark:text-gray-200 hover:text-primary-500 dark:hover:text-primary-400 hover:underline truncate transition-colors">
                                    {{ $rec['identifier'] }}
                                </a>
                                @else
                                <span class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $rec['identifier'] }}</span>
                                @endif
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($rec['status'] !== 'baseline')
                                <span class="text-[11px] text-gray-400 font-mono">{{ $rec['age_minutes'] }}m ago</span>
                                @elseif(!empty($rec['is_modified']))
                                <span class="text-[11px] text-gray-400 font-mono">{{ $rec['updated_age_minutes'] }}m ago</span>
                                @endif

                                @if($rec['status'] === 'baseline')
                                <x-filament::badge color="success" size="md">Protected</x-filament::badge>
                                @if(!empty($rec['is_modified']))
                                <x-filament::badge color="warning" size="md">Updated</x-filament::badge>
                                @endif
                                @elseif($rec['status'] === 'grace')
                                <x-filament::badge color="primary" size="md">Grace Window</x-filament::badge>
                                @else
                                <x-filament::badge color="danger" size="md">Will Purge</x-filament::badge>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400 italic py-2">No records found.</p>
                        @endforelse
                    </div>
                </x-filament::section>
                @endforeach
            </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-between w-full">
                {{ $this->cleanupAction }}

                <x-filament::button wire:click="closeInspector" color="gray" size="md">
                    Close Inspector
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
    <x-filament-actions::modals />

</div>
