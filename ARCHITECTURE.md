# Architecture — tekavogtil

## System overview

```mermaid
graph TD
    A[Visitor / Client] -->|HTTP| B[Laravel 12 Router]
    B --> C{SetLocale Middleware}
    C -->|session locale NO/EN| D[Controller]
    D --> E[Blade View]
    E --> F[layouts/app.blade.php]

    B --> G[/admin — Filament v3]
    G --> H[Admin Panel]
    H --> I[(SQLite dev / MySQL prod)]

    D --> J[InquiryController]
    J --> I
    J --> K[NewInquiry Mailable]
    K --> L[Resend — Email]

    I --> M[Eloquent Models]
    M --> N[Service]
    M --> O[Inquiry]
    M --> P[Client]
    M --> Q[Project]
    M --> R[Invoice]
```

---

## Application layers

```
VISITOR BROWSER
      ↕ HTTP
LARAVEL 12 ROUTING  (routes/web.php)
      ↕ Middleware stack (SetLocale → auth guards)
CONTROLLERS
  HomeController       → home.blade.php
  ServiceController    → services/index.blade.php + services/show.blade.php
  InquiryController    → pages/inquiry.blade.php  (GET/POST /start)
  ContactController    → pages/contact.blade.php  (GET/POST /kontakt)
  LocaleController     → session locale switch    (GET  /lang/{locale})
      ↕ Eloquent ORM
DATABASE  (SQLite dev / MySQL prod)
      ↕
FILAMENT v3 ADMIN  (/admin)
  ServiceResource      → manage service catalogue
  InquiryResource      → view, status-change, convert to project
  ClientResource       → manage clients
  ProjectResource      → Kanban pipeline
  InvoiceResource      → invoice log
      ↕ (Milestone 5)
PAYMENT LAYER
  Stripe Cashier       → card + Klarna
  Vipps eCommerce API  → Norwegian primary payment
  Vipps Recurring API  → monthly retainers
```

---

## Routes

| Method | URI | Handler | Description |
|--------|-----|---------|-------------|
| GET | `/` | `HomeController@index` | Homepage |
| GET | `/tjenester` | `ServiceController@index` | Services browser |
| GET | `/tjenester/{slug}` | `ServiceController@show` | Service detail |
| GET | `/priser` | `PricingController@index` | Pricing page |
| GET | `/start` | `InquiryController@create` | Inquiry form |
| POST | `/start` | `InquiryController@store` | Submit inquiry |
| GET | `/om-oss` | `AboutController@index` | About page |
| GET | `/kontakt` | `ContactController@create` | Contact form |
| POST | `/kontakt` | `ContactController@store` | Submit contact |
| GET | `/lang/{locale}` | `LocaleController@switch` | Switch NO/EN |

---

## Database schema (entity overview)

> Full migration files are in the private repo. This is the entity map only.

| Entity | Key fields | Relations |
|--------|-----------|-----------|
| `users` | name, email, role (owner/staff/subcontractor) | has many projects |
| `services` | name_no, name_en, slug, description_no/en, category, starting_price, includes (JSON), tech_stack (JSON), is_visible, sort_order | has many inquiries |
| `inquiries` | name, email, phone, company, service_id, budget, message, status (new/read/quoted/converted) | belongs to service |
| `clients` | name, org_number, email, phone, address | has many projects |
| `projects` | client_id, service_id, title, status (lead/quoted/active/delivered/retained), assigned_to, start/deadline dates, deposit_paid, fully_paid | belongs to client |
| `invoices` | project_id, amount_øre, currency, status (pending/paid/overdue), due_date | belongs to project |

---

## i18n architecture

Laravel's `app()->setLocale()` drives all translations. Locale is stored in the PHP session and set via a dedicated `LocaleController` route.

```
GET /lang/no  →  LocaleController::switch('no')  →  session(['locale' => 'no'])  →  redirect()->back()
GET /lang/en  →  LocaleController::switch('en')  →  session(['locale' => 'en'])  →  redirect()->back()

SetLocale middleware (runs on every request via the 'web' group):
  1. Read session('locale')  — default to config('app.locale', 'no')
  2. Validate against whitelist ['no', 'en']  — prevent injection
  3. app()->setLocale($locale)

Blade views use __('site.key') helper.
Lang files:  lang/no/site.php  and  lang/en/site.php
```

See [code-samples/i18n-middleware-example.php](code-samples/i18n-middleware-example.php) for the full implementation.

---

## Email pipeline

```
Client fills /start form
       ↓
InquiryController::store()
       ↓ validates (name, email, phone, service_id, budget, message)
       ↓ creates Inquiry record (status = 'new')
       ↓
Mail::to(config('mail.owner'))->send(new NewInquiry($inquiry))
       ↓
Resend delivers email to owner
       ↓
Owner logs in to /admin → InquiryResource
       ↓ changes status: new → read → quoted → converted
```

---

## Key architecture decisions

| Decision | Choice | Reason |
|----------|--------|--------|
| Framework | **Laravel 12** (not 13) | Filament v3 is not yet compatible with Laravel 13 |
| Admin panel | **Filament v3** | Zero-boilerplate CRUD, form builder, filters, roles — fastest path to working admin |
| Database (dev) | **SQLite** | Zero config, instant start on Windows A:\ drive, single `.sqlite` file |
| Database (prod) | **MySQL** | Hetzner/Forge standard, performance, relational integrity |
| Templating | **Blade** (not React/Vue) | Server-rendered, no build step, SEO-friendly, consistent with Filament |
| Styling | **Tailwind via CDN** (dev) → Vite (prod) | No Node dependency in dev; Vite purges unused classes in prod |
| Email | **Resend** | Modern API, generous free tier, excellent deliverability in Norway |
| Hosting | **Hetzner + Forge** | ~€4/mo, European (GDPR), GitHub push-to-deploy, SSL auto-provisioned |
| i18n | **Session-based** (not URL prefix) | Clean URLs, simpler state, no `/no/` or `/en/` prefix in routes |
| Payment priority | **Vipps first, Stripe second** | 95%+ of Norwegians use Vipps; cards + Klarna via Stripe |
| Language default | **Norwegian (Bokmål)** | Norwegian agency, Norwegian market — always NO first |

---

## Non-obvious gotchas (documented for contributors)

### 1. Blade `@@context` in JSON-LD

In Blade templates, `@context` inside a `<script type="application/ld+json">` block is interpreted as a Blade directive and throws an error. Use `@@context` to output a literal `@`:

```blade
{{-- WRONG — Blade treats @context as a directive --}}
{"@context": "https://schema.org"}

{{-- CORRECT --}}
{"@@context": "https://schema.org"}
```

Same applies to `@keyframes` in inline `<style>` blocks — use `@@keyframes`.

### 2. `artisan serve` on Windows A:\ drive

`php artisan serve` fails when the project root is on an A:\ drive due to a PHP built-in server path resolution issue. Workaround:

```bat
@echo off
cd /d A:\path\to\project\public
php -S 127.0.0.1:8000
```

### 3. `@else` / `@endif` after a word character

Blade's preprocessor regex uses `\B@` to detect directives, which fails when `@else` or `@endif` is immediately preceded by a word character (e.g. `word@else`). Use `@php` conditional blocks when this occurs:

```blade
{{-- Problematic pattern --}}
{{ $value }}@if($condition)...@endif

{{-- Safe alternative --}}
@php $display = $condition ? 'value-a' : 'value-b'; @endphp
{{ $display }}
```
