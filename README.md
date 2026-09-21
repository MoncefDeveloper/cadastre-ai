<div align="center">

# Cadastre AI
### Enterprise AI Shared Inbox & Compliance CRM for Real Estate Brokerages

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v5.x-F59E0B?style=flat-square&logo=filament&logoColor=white)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Google Gemini](https://img.shields.io/badge/Google_Gemini-3.5_Flash--Lite-8E75C2?style=flat-square&logo=google&logoColor=white)](https://ai.google.dev)
[![Postmark](https://img.shields.io/badge/Postmark-Inbound_%26_Outbound-FFE01B?style=flat-square&logo=postmark&logoColor=black)](https://postmarkapp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-BE123C?style=flat-square)](LICENSE)

<p align="center">
  A high-scale real estate shared inbox and CRM engine that automates lead intake, extracts buyer search criteria via Google Gemini AI, matches inventory in real time, and audits outgoing agent replies for Fair Housing Act (FHA) regulatory compliance.
</p>

[Architecture](#system-architecture) • [Live Marketing Frontend](https://cadastre.framer.ai) • [Headless APIs](#public-headless-apis-v1) • [Demo Accounts](#demo-personas--role-directory) • [Setup Guide](#local-installation)

</div>

---

## The Problem & The Solution

High-volume real estate brokerages face two primary operational bottlenecks:
1. **Inbound Lead Triage:** Inquiries come in through email, webforms, and portals at all hours. Manually reading, extracting budgets, locations, and property requirements, and cross-referencing available inventory takes hours of agent time.
2. **Regulatory & Compliance Risk:** Under Title VIII of the Civil Rights Act (Fair Housing Act), agents who use subjective neighborhood descriptors (e.g., *"safe"*, *"quiet"*, *"family-friendly"*) or steer clients based on demographics face severe legal liability and license revocation.

**Cadastre AI** automates this entire pipeline:
* **Ingestion:** Inbound emails are received via Postmark webhooks and parsed into unified conversation threads.
* **Extraction:** Google Gemini AI extracts buyer parameters (budget, target location, bedrooms, property types) into structured JSON.
* **Inventory Match:** Active listings are matched against extracted requirements with real-time compatibility scores (85%–98%).
* **Compliance Gate:** Outbound email drafts are automatically graded for FHA compliance before sending, flagging steering or restricted descriptors.

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
│ Postmark Inbound MX   ├───────►│ API Perimeter (CORS & Throttle) │
└───────────────────────┘        └────────────────┬────────────────┘
                                                  │
                                                  ▼
                                 ┌─────────────────────────────────┐
                                 │ Laravel 13 Core Application     │
                                 ├─────────────────────────────────┤
                                 │ • Database-Backed Cache & Queue │
                                 │ • 30-Minute Self-Healing Engine │
                                 │ • Postmark Zero-Bounce Guard    │
                                 └────────┬───────────────┬────────┘
                                          │               │
                     Criteria Extraction  │               │ FHA Compliance Audit
                                          ▼               ▼
                                 ┌─────────────────────────────────┐
                                 │ Google Gemini 3.5 Flash-Lite    │
                                 │ (Structured JSON Directives)    │
                                 └─────────────────────────────────┘
                                                  ▲
                                                  │
                                 ┌────────────────┴────────────────┐
                                 │ Filament v5 CRM Control Plane   │
                                 │ (Spatie Shield Granular RBAC)   │
                                 └─────────────────────────────────┘
```

---

## Key Features

### 1. Autonomous AI Inbox Copilot
* **Multichannel Threading:** Inbound Postmark emails, portal webforms, and simulated leads are threaded into clean conversation timelines.
* **Contextual Draft Generation:** Gemini AI drafts replies in the client's language, incorporating matched property links and executive brokerage tone.
* **Prompt Modifiers:** Agents can apply instant AI modifiers (*"Make it Shorter"*, *"Executive Luxury Tone"*) with one click.

### 2. Fair Housing Act (FHA) Compliance Engine
* Outgoing drafts are evaluated against regulatory rules in `config/cadastre-rules.php`.
* Flags demographic steering, protected class mentions, and restricted subjective descriptors.
* Grades drafts from **A to F** with an actionable critique and blocks non-compliant replies from being sent unless authorized.

### 3. Self-Healing Live Demo Sandbox
* Designed for safe public evaluation without data corruption.
* Seeded baseline records (`id <= limit`) are protected against accidental deletion.
* Any visitor-created records or edits operate on a **30-minute sliding window** and are automatically pruned by a scheduled background worker (`demo:cleanup`).

### 4. Deliverability Safeguards (Zero-Bounce Sinkhole)
* Outbound emails sent to test domains (`.test`, `.example`, `.invalid`, `.localhost`) are intercepted before reaching Postmark.
* Preserves production sender reputation and prevents bounce penalties during team evaluation.

---

## Public Headless APIs (v1)

Built to feed the live marketing landing page ([cadastre.framer.ai](https://cadastre.framer.ai)) without direct database exposure.

### 1. Commercial Pricing Plans
`GET /api/v1/plans` — *Rate limit: 60 req/min (Cached)*

Delivers active subscription tiers, pricing in cents and dollars, dynamic annual discount calculations (*"2 Months Free"*), feature bullets, and operational limits.

```json
{
  "data": [
    {
      "id": 2,
      "name": "Professional Broker",
      "slug": "professional-broker",
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
`GET /api/v1/faqs` — *Rate limit: 60 req/min (Cached)*

Exposes published global FAQs. Internal agent documentation and billing notes are scoped out. XSS filters strip executable scripts while preserving rich text formatting.

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

Handles public webform submissions from the marketing site with pre-validation input sanitization, anti-spam honeypot protection, and automatic Filament admin notifications.

```json
// Request Payload:
{
  "name": "Karim Benali",
  "email": "karim@diaspora-invest.test",
  "phone": "+213 656 711 226",
  "subject": "Diplomatic Villa in Hydra — Viewing Request",
  "message": "Coordinating an on-site confidential architectural inspection.",
  "_cadastre_hp": "" // Anti-spam honeypot (bots that fill this are silently dropped)
}

// Response (201 Created):
{
  "success": true,
  "message": "Advisory inquiry transmitted successfully. Our private office will respond within 24 business hours."
}
```

---

## Demo Personas & Role Directory

The login screen includes pre-configured personas to test authorization gates:

| Role | Demo Account | Capabilities |
| :--- | :--- | :--- |
| **Admin** | `admin@cadastre.test` | Full platform control, SaaS billing, global AI modifiers, compliance bypass. |
| **Manager** | `manager@cadastre.test` | Compliance Director. Supervises threads, can override FHA compliance, manage listings. |
| **Agent** | `agent@cadastre.test` | Day-to-day CRM actions. Must pass FHA compliance before sending replies. Price overrides locked. |
| **Guest** | `guest@cadastre.test` | Read-only inspection mode. All create, edit, and delete actions locked. |

*Default password for all personas:* `password`

---

## Local Installation

### Prerequisites
* PHP 8.3+ with `pdo_mysql`, `mbstring`, `intl`, `bcmath`
* Composer 2.7+
* Node.js 20+ & npm
* MySQL 8.0+ or SQLite 3.35+

### Setup Commands

```bash
# 1. Clone repository & enter directory
git clone https://github.com/MoncefDeveloper/cadastre-ai.git
cd cadastre-ai

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations and database seeders
php artisan migrate:fresh --seed

# 5. Build frontend assets and create storage symlink
npm run build
php artisan storage:link

# 6. Start the local server and background queue
php artisan serve
php artisan queue:listen --tries=1
```

Open `http://localhost:8000/admin` to access the CRM dashboard.

---

## License

Open-sourced software licensed under the [MIT License](LICENSE).

<div align="center">
  <sub>Architected & Engineered by <strong>Moncef Dev</strong> (<a href="https://moncefdev.me">moncefdev.me</a>)</sub>
</div>
