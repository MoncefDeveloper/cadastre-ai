<div x-data="{
    init() {
        if (! sessionStorage.getItem('cadastre_welcome_shown')) {
            setTimeout(() => {
                $dispatch('open-modal', { id: 'sandbox-welcome-modal' });
                sessionStorage.setItem('cadastre_welcome_shown', 'true');
            }, 5000);
        }
    }
}">
    <x-filament::modal id="sandbox-welcome-modal" width="2xl">
        <x-slot name="heading">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                    <x-heroicon-m-sparkles class="w-5 h-5" />
                </div>
                <div>
                    <span class="font-bold text-lg text-gray-900 dark:text-white">Welcome to Cadastre AI Shared Inbox</span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sandbox Architecture & Evaluator Testing Guide</p>
                </div>
            </div>
        </x-slot>

        <div class="space-y-4 pr-1 pb-2">

            <!-- 1. Top Callout: Sandbox Status (Info) -->
            <x-filament::callout color="info" icon="heroicon-o-shield-check">
                <x-slot name="heading">
                    <span class="font-bold text-xs">Live Sandbox Playground Active</span>
                </x-slot>
                <x-slot name="description">
                    <span class="text-xs leading-relaxed">
                        To preserve baseline demo conversations for all evaluators, direct outbound email dispatch is protected on baseline threads (1–6).
                    </span>
                </x-slot>
            </x-filament::callout>

            <!-- 2. Middle: 2-Step Testing Guide Section -->
            <x-filament::section icon="heroicon-o-academic-cap" collapsible>
                <x-slot name="heading">
                    <span class="font-bold text-sm">How to Test the Platform</span>
                </x-slot>

                <div class="space-y-3 text-xs">
                    <!-- Step 1 -->
                    <div class="flex items-start gap-3 p-2.5 rounded-lg bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-white/5">
                        <div class="w-6 h-6 rounded-full bg-primary-500/10 text-primary-600 dark:text-primary-400 font-bold flex items-center justify-center shrink-0">1</div>
                        <div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-0.5">Test the AI Copilot on Existing Threads</h5>
                            <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                                Select any conversation (e.g., <em>Sultan Al-Otaibi</em>), open <strong>AI Insights</strong>, test generating drafts, applying modifiers like <em>"Make it Shorter"</em>, and evaluating FHA compliance.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-start gap-3 p-2.5 rounded-lg bg-gray-50 dark:bg-white/[0.02] border border-gray-100 dark:border-white/5">
                        <div class="w-6 h-6 rounded-full bg-primary-500/10 text-primary-600 dark:text-primary-400 font-bold flex items-center justify-center shrink-0">2</div>
                        <div>
                            <h5 class="font-bold text-gray-900 dark:text-white mb-0.5">Simulate New Inbound Webhooks for Live Sending</h5>
                            <p class="text-gray-500 dark:text-gray-400 leading-relaxed">
                                Use the <strong>Inbound Simulator</strong> to dispatch test leads (or type your own personal email). New conversations are 100% unrestricted with live Postmark email delivery unlocked.
                            </p>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            <!-- 3. Bottom Callout: Evaluator Notice (Warning / Amber) -->
            <x-filament::callout color="warning" icon="heroicon-o-exclamation-triangle">
                <x-slot name="heading">
                    <span class="font-bold text-xs">Evaluator Notice: Sandbox-Only Utilities</span>
                </x-slot>
                <x-slot name="description">
                    <span class="text-xs leading-relaxed">
                        This welcome guide, the waving helper trigger, and the Inbound Webhook Simulator are demo utilities built specifically for testing purposes and to streamline your evaluation. In production, conversations are ingested organically via live Postmark MX servers.
                    </span>
                </x-slot>
            </x-filament::callout>

        </div>

        <x-slot name="footer">
            <div class="flex items-center justify-end gap-2 w-full">
                <x-filament::button color="gray" x-on:click="close()">
                    Explore Inbox
                </x-filament::button>

                <x-filament::button outlined color="primary" icon="heroicon-m-bolt" x-on:click="close(); $dispatch('open-modal', { id: 'inbound-simulator-modal' })">
                    Open Lead Simulator
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</div>
