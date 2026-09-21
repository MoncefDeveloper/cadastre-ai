<div align="center">

# Cadastre AI
### Enterprise Autonomous Real Estate Shared Inbox & Compliance CRM

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v5.x-F59E0B?style=for-the-badge&logo=filament&logoColor=white)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-v4.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Google Gemini](https://img.shields.io/badge/Google_Gemini-3.5_Flash--Lite-8E75C2?style=for-the-badge&logo=google&logoColor=white)](https://ai.google.dev)
[![Postmark](https://img.shields.io/badge/Postmark-Inbound_%26_Outbound-FFE01B?style=for-the-badge&logo=postmark&logoColor=black)](https://postmarkapp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-BE123C?style=for-the-badge)](LICENSE)

<p align="center">
  A high-scale, regulatory-compliant AI Shared Inbox and Brokerage CRM built for sovereign real estate firms. Features autonomous lead criteria extraction, dynamic inventory matching, Fair Housing Act (FHA) compliance grading, and headless API integration for Framer Edge frontends.
</p>

[Explore Architecture](#system-architecture) • [Live Marketing Frontend](https://cadastre.framer.ai) • [Demo Credentials](#demo-personas--role-directory)

</div>

---

## Executive Summary

**Cadastre AI** bridges the gap between high-volume inbound client communications and strict real estate regulatory compliance. Operating on top of **Google Gemini 3.5 Flash-Lite** and **Postmark**, the platform intercepts inbound emails, extracts structured buyer requirements (budget, target location, bedroom counts, property classifications), and calculates real-time inventory compatibility scores (85%–98%) against active portfolios.

Before an agent can dispatch an outgoing reply, the built-in **AI Compliance Officer** audits the draft against Title VIII of the Civil Rights Act (Fair Housing Act), scanning for prohibited demographic steering, protected class mentions, and restricted neighborhood descriptors.

---

## System Architecture

```text
                                 ┌─────────────────────────────────┐
                                 │   Framer Edge Marketing Site    │
                                 │    (https://cadastre.framer.ai) │
                                 └────────────────┬────────────────┘
                                                  │ Headless JSON APIs
                                                  ▼
┌───────────────────────┐        ┌─────────────────────────────────┐
│ Postmark MX Inbound   ├───────►│  API Perimeter (CORS / Throttle)│
└───────────────────────┘        └────────────────┬────────────────┘
                                                  │
                                                  ▼
                                 ┌─────────────────────────────────┐
                                 │ Laravel 13 Core Application     │
                                 ├─────────────────────────────────┤
                                 │ • ForceJsonResponse Middleware   │
                                 │ • Database Cache Layer          │
                                 │ • Database Queue Worker Engine  │
                                 │ • Hybrid 30m Sliding Sandbox    │
                                 └────────┬───────────────┬────────┘
                                          │               │
                     Criteria Extraction  │               │ FHA Regulatory Grading
                                          ▼               ▼
                                 ┌─────────────────────────────────┐
                                 │ Google Gemini 3.5 Flash-Lite    │
                                 │ (Structured System Directives)  │
                                 └─────────────────────────────────┘
                                                  ▲
                                                  │
                                 ┌────────────────┴────────────────┐
                                 │ Filament v5 CRM Control Plane   │
                                 │ (Spatie Shield Granular RBAC)   │
                                 └─────────────────────────────────┘
```

---

## Core Engineering Invariants & Architectural Patterns

The engine enforces strict architectural patterns across every layer:

* **Shadow DOM Email Isolation:** Inbound email rendering is encapsulated inside client-side Shadow DOM roots (`$el.attachShadow({ mode: 'open' })`) with `!important` style overrides to prevent third-party email CSS from leaking into Filament's dark chassis.
* **Recursive Array Flattener:** Custom recursive iterator sanitization protects Livewire rich editors against nested component hydration crashes during AI prompt and evaluation transfers.
* **Ghost Admin Pattern:** Master Root User `ID 1` is strictly excluded across all public presentations, Filament resource tables, global search indexes, and telemetry badges (`id != 1`).
* **Hybrid Sandbox Self-Healing:** Baseline records (`id <= limit`) are protected by the `ProtectsBaseline` trait. Visitor-created test records operate within a 30-minute sliding grace window, automatically purged by a background scheduler (`demo:cleanup`).
* **Zero-Bounce Outbound Interceptor:** Emails directed to `.test`, `.example`, `.invalid`, or `.localhost` domains are intercepted before hitting the Postmark API to protect external sender reputation scores.
* **Regulatory Compliance Engine (SSOT):** `config/cadastre-rules.php` serves as the atomic Single Source of Truth for Fair Housing Act compliance grading and automated quality scoring.
* **Perimeter Rate-Limiting & CORS Isolation:** Strict origin regex validation in `config/cors.php` authorizes `cadastre.framer.ai` and dynamic Framer preview subdomains with a 24-hour preflight cache (`86400` max-age).
* **Defensive Anti-Spam Honeypot:** Unauthenticated lead endpoints enforce zero-write silent drops on populated honeypot tokens (`_cadastre_hp`), returning simulated `201 Created` receipts.
* **Single-Envelope JSON Architecture:** Public read APIs deliver normalized `{"data": [...]}` envelopes directly to eliminate double-wrapping collisions on modern frontend consumers.
* **Zero-CDN Typography Integrity:** System fonts are served locally via Plus Jakarta Sans with OpenType tabular figures (`tabular-nums`) to prevent font metric jitter and external CDN telemetry leaks.

---

## Design System & Tokens

Cadastre AI features a custom high-contrast design system built specifically for mission-critical operations:

* **Primary Brand Accent:** Imperial Carmine (`#BE123C` / `oklch(0.48 0.22 18.5)`)
* **Signal Danger Accent:** Flame Vermilion (`#F95428` — 33° hue separation from Carmine)
* **Dark Chassis Surface:** Cold Obsidian (`#05070B` canvas / `#0D1117` elevated cards)
* **Light Chassis Surface:** Slate-50 (`#F8FAFC` canvas / `#FFFFFF` elevated cards)

---

## Demo Personas & Role Directory

The application includes four pre-seeded personas configured in Filament's custom dual-card authentication screen:

| Role | Demo Email | Access Scope |
| :--- | :--- | :--- |
| **Admin** | `admin@cadastre.test` | Platform Owner. Unrestricted CRUD, SaaS billing, AI modifier authoring, and compliance overrides. |
| **Manager** | `manager@cadastre.test` | Compliance Director. Full operational oversight, closed-deal reopening, and compliance override authority. |
| **Senior Agent** | `agent@cadastre.test` | Operational Broker. Full CRM actions, property management, subject to strict FHA compliance grading. |
| **Guest** | `guest@cadastre.test` | Read-Only Evaluator. Safe exploratory inspection mode with all creation, editing, and deletion gates locked. |

*Default password for all personas:* `password`

---

## Public Headless API Contracts (v1)

### 1. Commercial Subscription Plans
`GET /api/v1/plans` — *Cached via `saas_active_plans` (Rate limit: 60 req/min)*

```json
{
  "data": [
    {
      "id": 2,
      "name": "Professional Broker",
      "slug": "professional-broker",
      "description": "Complete AI shared inbox, unlimited draft refinements, and team collaboration.",
      "currency": "USD",
      "is_popular": true,
      "price": {
        "monthly": { "cents": 14900, "dollars": 149, "formatted": "$149" },
        "yearly": { "cents": 149000, "dollars": 1490, "formatted": "$1,490", "discount_label": "2 Months Free" }
      },
      "features": [
        "Unlimited AI Copilot Drafts",
        "50 Active Property Listings",
        "Strict FHA Compliance Auditing & Scoring",
        "3 Team Agent Seats Included"
      ],
      "limits": { "team_seats": 3, "active_listings": 50 },
      "cta": { "label": "Deploy Professional", "url": "http://localhost:8000/admin/login" }
    }
  ]
}
```

### 2. Knowledge Base FAQs
`GET /api/v1/faqs` — *Cached via `saas_active_faqs` (Rate limit: 60 req/min)*

```json
{
  "data": [
    {
      "id": 1,
      "question": "How does Cadastre AI automatically match client inquiries to properties?",
      "answer": "<p>Cadastre AI analyzes natural language via <strong>Google Gemini AI</strong>...</p>",
      "sort_order": 1
    }
  ]
}
```

### 3. Advisory Lead Ingestion
`POST /api/v1/contacts` — *Rate limit: 5 req/min per IP*

```json
// Request Payload:
{
  "name": "Karim Benali",
  "email": "karim@diaspora-invest.test",
  "phone": "+213 656 711 226",
  "subject": "Diplomatic Villa in Hydra — Viewing Request",
  "message": "Coordinating an on-site confidential architectural inspection.",
  "_cadastre_hp": ""
}

// Response (201 Created):
{
  "success": true,
  "message": "Advisory inquiry transmitted successfully. Our private office will respond within 24 business hours."
}
```

---

## Local Development & Setup

### Prerequisites
* PHP 8.3 or higher with `pdo_mysql`, `mbstring`, `bcmath`, `intl` extensions
* Composer 2.7+
* Node.js 20+ & npm
* MySQL 8.0+ or SQLite 3.35+

### Installation Steps

```bash
# 1. Clone repository
git clone https://github.com/MoncefDeveloper/cadastre-ai.git
cd cadastre-ai

# 2. Install PHP & JavaScript dependencies
composer install
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Database execution & pristine seeding
php artisan migrate:fresh --seed

# 5. Compile assets and link public storage
npm run build
php artisan storage:link

# 6. Run background workers and local server
php artisan serve
php artisan queue:listen --tries=1
```

Access the admin dashboard at `http://localhost:8000/admin`.

---

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).

<div align="center">
  <sub>Architected & Engineered by <strong>Moncef Dev</strong> (<a href="https://moncefdev.me">moncefdev.me</a>)</sub>
</div>
