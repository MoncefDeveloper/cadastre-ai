<div class="cad-auth-wrapper"
     x-data="{
         activeRoleKey: 'super_admin',
         quickLogin(email) {
             $wire.quickLogin(email);
         },
         copyToClipboard(email, event) {
             navigator.clipboard.writeText(email);
             $wire.set('data.email', email);
             $wire.set('data.password', 'password');
             $wire.set('data.remember', true);

             let btn = event.currentTarget;
             let originalText = btn.innerText;
             btn.innerText = 'Copied!';

             setTimeout(() => {
                 btn.innerText = originalText;
             }, 1500);
         }
     }">

    <div class="cad-auth-container">

        <!-- Stacked Brand Logo Header -->
        <!-- <div class="text-center mb-1">
            <img src="{{ asset('images/cadastre-stacked-light.svg') }}" class="h-16 w-auto mx-auto" alt="Cadastre AI" />
        </div> -->

        <!-- 50/50 Dual Cards Grid -->
        <div class="cad-auth-grid">
            @include('filament.pages.auth.partials.demo-credentials-card')
            @include('filament.pages.auth.partials.sign-in-card')
        </div>

        <!-- 1-LINE FOOTER -->
        <footer class="w-full border-t border-gray-200/80 dark:border-white/10 pt-4 mt-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 font-medium">
            <!-- Left: Sandbox Live Status -->
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="font-bold text-gray-900 dark:text-gray-100">Sandbox Active</span>
                <span class="text-gray-300 dark:text-gray-700 select-none">&bull;</span>
                <span>Base Seeds Protected</span>
                <span class="text-gray-300 dark:text-gray-700 select-none">&bull;</span>
                <span class="text-danger-600 dark:text-danger-400 font-semibold">Auto-Pruned Every 30m</span>
            </div>

            <!-- Right: Copyright -->
            <div class="text-[11px] opacity-80">
                &copy; {{ date('Y') }} Cadastre AI &bull; Autonomous Real Estate Intelligence
            </div>
        </footer>

    </div>

    <!-- Slide-Over Modal -->
    @include('filament.pages.auth.partials.role-permissions-modal')

</div>
