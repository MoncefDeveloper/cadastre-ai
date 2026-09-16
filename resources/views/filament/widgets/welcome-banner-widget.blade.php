<x-filament-widgets::widget class="col-span-full w-full" style="grid-column: 1 / -1 !important;">
    <div class="cad-hero-banner">
        <!-- Ultra-Subtle Blueprint Grid -->
        <div class="cad-hero-grid"></div>

        <!-- Ambient Light Orb -->
        <div class="cad-hero-glow"></div>

        <div class="cad-hero-layout">
            <!-- Left Side: Header, Title, Subtitle, Pills -->
            <div class="cad-hero-left">
                <!-- Top Badge Pill -->
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.18); display: flex; align-items: center; justify-content: center;">
                        <x-heroicon-s-chart-bar style="width: 18px; height: 18px; color: #ffffff;" />
                    </div>
                    <span class="cad-hero-badge-tag">
                        Dashboard Overview
                    </span>
                </div>

                <!-- Headline -->
                <h1 class="cad-hero-title">
                    Welcome back, {{ $firstName }}!
                </h1>

                <!-- Subtitle -->
                <p class="cad-hero-subtitle">
                    {{ $subtitle }}
                </p>

                <!-- Status Pills -->
                <div class="cad-hero-pill-group">
                    <!-- Pill 1: System Online -->
                    <div class="cad-hero-pill">
                        <span style="position: relative; display: flex; width: 8px; height: 8px;">
                            <span style="position: absolute; width: 100%; height: 100%; border-radius: 9999px; background: #34d399; opacity: 0.75; animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;"></span>
                            <span style="position: relative; width: 8px; height: 8px; border-radius: 9999px; background: #10b981;"></span>
                        </span>
                        <span>System Online</span>
                    </div>

                    <!-- Pill 2: FHA Certified -->
                    <div class="cad-hero-pill">
                        <x-heroicon-m-shield-check style="width: 15px; height: 15px; color: #6ee7b7;" />
                        <span>FHA Certified</span>
                    </div>

                    <!-- Pill 3: Live Date -->
                    <div class="cad-hero-pill">
                        <x-heroicon-m-calendar style="width: 15px; height: 15px; color: rgba(255,255,255,0.7);" />
                        <span>{{ $currentDate }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Floating Glass Card (Exact Match to Reference) -->
            <div class="cad-hero-card-right">
                <span style="font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.75); text-transform: uppercase; letter-spacing: 0.05em;">
                    Top Match Score
                </span>

                <div class="cad-hero-stat-number">
                    {{ $topMatchScore }}
                </div>

                <div style="font-size: 0.75rem; font-weight: 700; color: #6ee7b7; display: flex; align-items: center; gap: 0.35rem;">
                    <x-heroicon-m-arrow-trending-up style="width: 14px; height: 14px; color: #6ee7b7;" />
                    <span>Active System ({{ $activeListingsCount }})</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
