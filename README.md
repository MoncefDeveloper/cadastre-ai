<div align="center">

# Cadastre AI
### Autonomous Real Estate AI Shared Inbox & Compliance CRM

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v5.x-F59E0B?style=flat-square&logo=filament&logoColor=white)](https://filamentphp.com)
[![Livewire](https://img.shields.io/badge/Livewire-v4.x-4E56A6?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Google Gemini](https://img.shields.io/badge/Google_Gemini-3.5_Flash--Lite-8E75C2?style=flat-square&logo=google&logoColor=white)](https://ai.google.dev)
[![Postmark](https://img.shields.io/badge/Postmark-Inbound_%26_Outbound-FFE01B?style=flat-square&logo=postmark&logoColor=black)](https://postmarkapp.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-BE123C?style=flat-square)](LICENSE)

<p align="center">
  An enterprise brokerage operating system that ingests unstructured client inquiries, extracts property search criteria via Google Gemini AI, matches active portfolios through a 3-tier fallback engine, and enforces Fair Housing Act (FHA) regulatory compliance before dispatching replies.
</p>

[AI Copilot Architecture](#1-autonomous-ai-copilot--intent-pipeline) • [Prompt & Template Engine](#2-ai-modifiers--prompt-blueprint-templates) • [Self-Healing Sandbox](#3-self-healing-hybrid-sandbox-engine) • [Compliance Guard](#4-regulatory-fha-compliance-evaluator) • [Local Setup](#local-development--setup)

</div>

---

## Technical Highlights

* **Autonomous Lead Extraction:** Inbound emails are parsed by Google Gemini 3.5 Flash-Lite to extract budgets, target cities, bedroom counts, and property types into structured JSON schemas.
* **3-Tier Fallback Inventory Matching:** Real-time matching algorithm that executes strict queries first, drops strict bedroom/bathroom limits second, and expands budget ceilings by 15% third to prevent zero-inventory dead ends.
* **Persona-Aware Dynamic Drafting:** Detects first-time vs. returning client relationship history to branch introductory branding vs. direct transaction updates automatically.
* **Runtime AI Modifiers & Prompt Blueprints:** Agents apply instant prompt rewrites (*"Make it Shorter"*, *"Executive Luxury Tone"*) and apply legally binding templates with tokenized variable interpolation (`{{client_name}}`, `{{property_title}}`).
* **Automated Fair Housing Act (FHA) Compliance Gate:** Audits outgoing drafts against Title VIII civil rights regulations, flagging demographic steering, protected class terms, and restricted subjective descriptors with an A–F grade.
* **4-Stage Self-Healing Sandbox:** Automated maintenance engine that isolates baseline demo records, snapshots active visitor edits, cascade-purges expired data, cleans physical storage diffs, and resets database state on a 30-minute sliding window.
* **Dual-Mailer Delivery Infrastructure:** Uses Postmark API with unique mailbox hashing (`th_...`) for client conversations and routes high-volume internal agent notifications through a secondary SMTP transport to protect API limits and deliverability reputation.

---

## System Architecture

```text
┌───────────────────────────┐      ┌───────────────────────────┐
│ Postmark Inbound Webhook  │      │ Framer Edge Marketing API│
└─────────────┬─────────────┘      └─────────────┬─────────────┘
              │ RFC 2822 Inbound                 │ CORS & Rate-Limited (v1)
              ▼                                  ▼
┌──────────────────────────────────────────────────────────────┐
│ Laravel 13 Core Infrastructure                               │
│ ├── Inbound Email Normalizer & Shadow DOM Body Sanitizer      │
│ ├── Asynchronous Database Queue (AnalyzeClientIntentJob)     │
│ ├── 3-Tier Fallback Property Match Engine                    │
│ └── 4-Stage Self-Healing Sandbox Maintenance (demo:cleanup)  │
└──────────────┬────────────────────────────────┬──────────────┘
               │                                │
    Criteria & │ Dynamic Persona     Draft Audit│ Compliance Grade
    Extraction │ Response Drafting    & Scoring │ (A–F Matrix)
               ▼                                ▼
┌──────────────────────────────────────────────────────────────┐
│ Google Gemini 3.5 Flash-Lite Engine                          │
│ └── Structured JSON System Instructions & Prompt Blueprints  │
└──────────────────────────────┬───────────────────────────────┘
                               │
                               ▼
┌──────────────────────────────────────────────────────────────┐
│ Filament v5 CRM & AI Shared Inbox                            │
│ ├── Livewire v4 Real-Time Conversation Timeline              │
│ ├── AI Brain: Modifiers, Blueprint Templates & Insights      │
│ ├── Outbound Compliance Gate with Manual Override Bypasses   │
│ └── Spatie Shield Role Directory (Admin, Manager, Agent)     │
└──────────────────────────────────────────────────────────────┘
```

---

## 1. Autonomous AI Copilot & Intent Pipeline

The AI Inbox operates asynchronously across a resilient queue pipeline to prevent API timeouts and respect provider rate limits.

```text
Inbound Email ──► ProcessInboundEmailJob ──► AnalyzeClientIntentJob ──► DraftAiResponseJob
                        │                              │                        │
                        ▼                              ▼                        ▼
                Extract RFC ID &               Gemini Extracts          Detects Relationship
                Mailbox Hash (th_*)            Criteria JSON            (First-Time vs Returning)
                                                       │                        │
                                                       ▼                        ▼
                                               Tiered Inventory         Generates Semantic
                                               Matching (1 ➔ 2 ➔ 3)     HTML Response Draft
```

### Ingestion & Parsing (`ProcessInboundEmailJob`)
1. Reads inbound Postmark webhook payloads via `PostmarkInboundDTO`.
2. Extracts clean client RFC Message-IDs and conversation hashes (`mailbox_hash`) to thread replies accurately.
3. Strips injected external `<style>` blocks using DOMDocument parsers to protect the CRM interface against CSS bleed.

### Intent Extraction & 3-Tier Match Engine (`AnalyzeClientIntentJob`)
Gemini processes the raw conversation context and returns structured JSON:
```json
{
  "is_property_inquiry": true,
  "listing_type": "sale",
  "property_type": "villa",
  "city": "Hydra",
  "budget_max": 5000000,
  "bedrooms": 5,
  "bathrooms": 5
}
```
If inquiry criteria is confirmed, the engine searches active properties using a **3-tier fallback strategy**:
* **Tier 1 (Strict Match):** Exact city, property type, budget ceiling, bedroom minimum, and bathroom minimum.
* **Tier 2 (Relaxed Dimensions):** Keeps city, property type, and budget, but drops bedroom/bathroom requirements.
* **Tier 3 (Real Estate Market Expansion):** Keeps city, drops property type, and expands the client budget ceiling by **15%** to present adjacent luxury options.
* Matches are stored in `thread_property_matches` with confidence scores (e.g., `95.00%`) and rendered in the agent timeline.

### Dynamic Persona Drafting (`DraftAiResponseJob`)
Before drafting, the system queries the thread history for historical interactions:
* **First-Time Inquiries:** The AI introduces the firm (*"Cadastre Private Office"*), establishes brand credentials, and pitches matched listings using semantic `<a>` links.
* **Returning Clients:** The AI suppresses corporate introductions and pleasantries, transitioning directly into scheduling viewings or answering specific property questions.

---

## 2. AI Modifiers & Prompt Blueprint Templates

Cadastre AI features a prompt engineering workspace directly inside the agent interface (`ManagesAiBrain`).

```text
Agent Interface ──► Select Modifier / Template
                           │
             ┌─────────────┴─────────────┐
             ▼                           ▼
    Apply Modifier               Apply Template Blueprint
    (ModifyAiDraftJob)           (ApplyTemplateJob)
             │                           │
             ▼                           ▼
    Targeted Rewrite             Interpolate {{variables}}
    (e.g., "Executive Tone")     Inject System Persona
             │                           │
             └─────────────┬─────────────┘
                           ▼
              Update Draft HTML in Composer
              & Increment Livewire UpdatedAt Signal
```

### AI Modifiers (`AiModifier`)
Quick-action prompt rewrite shortcuts configured in Filament:
* **Scope Isolation:** Modifiers are categorized as **Global** (managed agency-wide by Broker Managers) or **Agent-Specific** (scoped to individual users).
* **Execution (`ModifyAiDraftJob`):** Takes the active draft HTML, applies focused transformation instructions (e.g., *"Rewrite draft under 50 words focusing exclusively on price and viewing availability"*), and updates the record.

### Prompt Blueprint Templates (`Template`)
Dynamic drafting templates governed by communication channel (Email, WhatsApp, Webform):
* **Variable Interpolation:** Templates declare required runtime tokens (`client_name`, `target_city`, `property_title`, `property_price`, `agent_name`).
* **System Persona Boundaries:** Enforces strict role definitions (e.g., *"You are an elite Parisian private real estate advisor representing high-net-worth investors. Be impeccably polite, warm, and discreet."*).
* **Execution (`ApplyTemplateJob`):** Replaces variables with real CRM data and dispatches Gemini to generate a response tailored to that specific strategy.

---

## 3. Self-Healing Hybrid Sandbox Engine

To permit public evaluation without database destruction or persistent spam, Cadastre AI runs an autonomous 4-stage self-healing maintenance cycle every 30 minutes (`demo:cleanup`).

```text
Scheduler (Every 30m) ──► DemoCleanupCommand
                                 │
  ┌──────────────────────────────┼──────────────────────────────┐
  ▼                              ▼                              ▼
Stage 1: Storage Diff          Stage 2: Child-First Cascade    Stage 3: Snapshot Healing
• Diff physical storage        • Delete visitor matches        • Snapshot visitor edits (< 30m)
• Delete unseeded uploads      • Delete visitor messages       • Re-seed pristine seeders
• Protect baseline assets      • Delete visitor records        • Re-apply active grace edits
                               • Purge polymorphic alerts      • Sync pristine timestamps
```

### The 4 Cleanup Stages (`DemoCleanupCommand`)

1. **Stage 1: Orphaned Storage File Purge:**
   * Scans `storage/app/public` for property images, category icons, and message attachments.
   * Compares file paths against baseline seed records (`id <= limit`).
   * Permanently deletes visitor-uploaded files while leaving baseline demonstration assets untouched.

2. **Stage 2: Child-First MySQL Cascade Deletion:**
   * Uses an atomic database transaction to delete expired visitor data created before the 30-minute cutoff (`created_at < cutoff`).
   * Follows strict foreign-key dependency order: `ThreadPropertyMatch` $\rightarrow$ `PropertyImage` $\rightarrow$ `Message` $\rightarrow$ `Thread` $\rightarrow$ `Property` $\rightarrow$ `Category` $\rightarrow$ `Client` $\rightarrow$ `Contact` $\rightarrow$ `AiModifier` $\rightarrow$ `Template` $\rightarrow$ `User`.
   * Purges orphaned polymorphic database notifications where `notifiable_id > userLimit`.

3. **Stage 3: Baseline Seed Self-Healing (With Sliding Grace Window):**
   * **Snapshot Active Edits:** If a visitor is actively testing edits on a baseline record (e.g., updating a property price) and their edit is younger than 30 minutes (`updated_at >= cutoff`), the engine snapshots their changes.
   * **Re-Seed Missing Records:** Calls seeders silently (`PropertySeeder`, `PlanSeeder`, `ThreadSeeder`, etc.) to recreate any deleted baseline rows.
   * **Re-Apply Active Edits:** Re-applies active visitor edits over the freshly seeded baseline records so ongoing demo evaluations are not abruptly reset.
   * **Timestamp Synchronization:** Pristine baseline records have their `updated_at` timestamps synchronized back to `created_at`.

4. **Stage 4: Session & Spatie Permission Cache Flush:**
   * Prunes expired sessions from the `sessions` table (`last_activity < cutoff`).
   * Clears Spatie Shield cached permissions in memory via `PermissionRegistrar::forgetCachedPermissions()`.

### Real-Time Protection Trait (`ProtectsBaseline`)
Models implement the `ProtectsBaseline` trait. If any user (other than Master Root User ID 1) attempts an Eloquent `delete()` on a baseline record (`id <= limit`), the event is intercepted, halted, and a warning notification is dispatched to the UI.

---

## 4. Regulatory FHA Compliance Evaluator

To eliminate fair housing liability, outgoing messages are evaluated by Gemini against the regulatory single source of truth in `config/cadastre-rules.php`.

```text
Proposed Draft ──► Gemini Compliance Evaluation
                          │
       ┌──────────────────┴──────────────────┐
       ▼                                     ▼
Non-Compliant Draft                   Certified Compliant
• Trigger Compliance Block            • Grade: A / B (Score: 8–10)
• Display Violations & Critique       • Send Button Unlocked
• Offer 1-Click AI Fix                • Outbound Email Dispatched
```

### Compliance Rule Matrix
* **Protected Class Neutrality:** Detects and blocks terms referencing race, color, religion, national origin, sex, disability, or familial status.
* **Prohibited Demographic Steering:** Flags language directing buyers toward or away from neighborhoods based on demographics or family makeup.
* **Restricted Descriptors:** Replaces subjective marketing buzzwords (*"safe"*, *"quiet"*, *"family-friendly"*, *"ideal for retirees"*) with objective spatial or physical facts.
* **Objective Conflict Escape Clause:** If a client directly asks if an area is "safe", the model allows the response only if the agent immediately anchors the answer in physical, architectural facts (*"cul-de-sac layout," "low vehicular traffic," "triple-pane soundproofing"*).

---

## 5. Dual-Mailer & Reputation Infrastructure

Cadastre AI separates outbound client communications from internal staff notifications to protect sender scores and optimize costs:

```text
┌─────────────────────────────────┐
│ Outbound Email Dispatch         │
└────────────────┬────────────────┘
                 │
                 ├── Client Reply (Postmark API)
                 │   ├── Intercept .test/.example (Zero-Bounce Sinkhole)
                 │   └── Append Unique Reply-To Hash (inbound+th_***@domain.com)
                 │
                 └── Agent Notification (Namecheap SMTP)
                     └── Dispatched asynchronously via Namecheap transport
```

1. **Transactional Client Delivery (`PostmarkOutboundService`):**
   * Client replies are dispatched via the Postmark API.
   * Injects dynamic conversation hashes into the `Reply-To` header (`inbound+th_65f8a...@domain.com`) to thread future incoming emails automatically.
2. **Zero-Bounce Sinkhole Guard:**
   * `SendOutboundEmailJob` intercepts emails directed to `.test`, `.example`, `.invalid`, or `.localhost` domains.
   * Simulates delivery in the database (`status = 'delivered'`) without hitting the external Postmark API, maintaining a pristine sender reputation.
3. **Secondary Staff Mailer (`Namecheap SMTP`):**
   * In-app notifications alerting agents to new replies are routed through a dedicated Namecheap SMTP mailer (`mailer('namecheap')`), conserving Postmark transactional volume.

---

## 6. Headless API Integration Layer (v1)

A versioned REST API feeds the headless Framer marketing frontend ([cadastre.framer.ai](https://cadastre.framer.ai)) with full data isolation:

* `GET /api/v1/plans`: Returns active subscription packages with dynamic annual discount math (*"2 Months Free"*) and system limits. Cached via `saas_active_plans` (`PlanObserver` invalidation).
* `GET /api/v1/faqs`: Delivers global knowledge base items. Sanitizes HTML outputs defensively against Stored XSS. Cached via `saas_active_faqs` (`FaqObserver` invalidation).
* `POST /api/v1/contacts`: Ingests advisory inquiries with input sanitization, anti-spam honeypot defense (`_cadastre_hp`), and automatic Filament database notifications.

*Perimeter Hardening:* All v1 endpoints enforce JSON output via `force.json` middleware, restrict CORS to `cadastre.framer.ai` and Framer preview domains, and enforce tiered rate limits (`60 req/min` for reads, `5 req/min` for lead ingestion).

---

## 7. Demo Personas & Role Directory

Filament dual-card authentication provides instant persona testing with distinct authorization scopes:

| Role | Demo Credentials | Scope & Business Gates |
| :--- | :--- | :--- |
| **Admin** | `admin@cadastre.test` | Platform Owner. Unrestricted CRUD, SaaS billing management, AI modifier authoring, and compliance bypass authority. |
| **Manager** | `manager@cadastre.test` | Compliance Director. Full operational oversight, compliance override authority, can reopen closed deals (Sold/Rented). |
| **Senior Agent** | `agent@cadastre.test` | Operational Broker. Standard CRM actions. Price overrides locked; must achieve certified FHA compliance before sending replies. |
| **Guest** | `guest@cadastre.test` | Read-Only Evaluator. Safe exploratory mode. Create, edit, delete, and dispatch gates are fully locked. |

*Default password for all demo accounts:* `password`

---

## Local Development & Setup

### Prerequisites
* PHP 8.3+ with `pdo_mysql`, `mbstring`, `intl`, `bcmath`, `xml` extensions
* Composer 2.7+
* Node.js 20+ & npm
* MySQL 8.0+ or SQLite 3.35+

### Installation Steps

```bash
# 1. Clone repository
git clone https://github.com/MoncefDeveloper/cadastre-ai.git
cd cadastre-ai

# 2. Install PHP & Node dependencies
composer install
npm install

# 3. Environment configuration
# Mandatory keys: GEMINI_API_KEYS, POSTMARK_SERVER_TOKEN......
cp .env.example .env
php artisan key:generate

# 4. Database migration & pristine seeding
php artisan migrate:fresh --seed

# 5. Build production assets & create storage link
npm run build
php artisan storage:link

# 6. Start local server & background queue worker
php artisan serve
php artisan queue:listen --tries=1
php artisan schedule:work
```

Access the admin dashboard at `http://localhost:8000/admin`.

---

## License

Open-sourced software licensed under the [MIT License](LICENSE).

<div align="center">
  <sub>Architected & Engineered by <strong>Moncef Dev</strong> (<a href="https://moncefdev.me">moncefdev.me</a>)</sub>
</div>
