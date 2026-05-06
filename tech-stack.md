# Tech Stack — tekavogtil

Full breakdown of every tool, library, and service in the stack, with the reasoning behind each choice.

---

## Backend

| Tool | Version | Why |
|------|---------|-----|
| **Laravel** | 12.58.0 | Latest production-ready version. **Not Laravel 13** — Filament v3 is not yet compatible with Laravel 13 as of May 2026. |
| **PHP** | 8.4.20 | Latest stable. PHP 8.4 brings property hooks and improved performance. |
| **Filament** | v3.3.50 | Zero-boilerplate admin panel. Table builder, form builder, filters, tag inputs, toggles, reorderable rows, role-based access — all built in. Saved approximately 40 hours vs building CRUD manually. |
| **Laravel Cashier** | Latest | Stripe payment integration — one-off charges, subscription logic, webhook handling, Klarna via Stripe dashboard. |
| **Resend** | via Laravel Mail | Modern transactional email API. Better deliverability than Mailgun for Norway. Generous free tier. Simple `.env` integration: `MAIL_MAILER=resend`. |

---

## Database

| Tool | Environment | Why |
|------|-------------|-----|
| **SQLite** | Development | Zero config, single `.sqlite` file, instant start on any machine including Windows. No Docker, no running service. |
| **MySQL 8** | Production | Standard on Hetzner/Forge. Better performance for concurrent connections, relational integrity, full-text search if needed. |

---

## Frontend

| Tool | Version | Why |
|------|---------|-----|
| **Blade** | Laravel built-in | Server-rendered. No build step in development. Excellent SEO (no CSR hydration delay). Consistent with Filament's own templating. |
| **Tailwind CSS** | v3 via CDN (dev) → Vite build (prod) | Utility-first CSS. CDN for local development (no Node dependency). Vite build for production — purges unused classes, typically <20kb CSS output. |
| **Alpine.js** | v3 | Minimal JS for interactive components: mobile nav toggle, dropdowns, accordion sections. No full framework needed for a marketing site. |
| **Space Grotesk** | via Google Fonts | Headings. Geometric, modern, clean. Supports Latin Extended — needed for Norwegian characters (æ, ø, å). |
| **Inter** | via Google Fonts | Body / UI text. The most legible sans-serif at screen sizes 14–16px. Used by a large proportion of modern SaaS interfaces. |

---

## Cloud & Infrastructure

| Tool | Why |
|------|-----|
| **Hetzner VPS (CX22)** | ~€4/month. European data centre (Germany/Finland). GDPR-compliant hosting. Strong price-to-performance for a Laravel app at this scale. |
| **Laravel Forge** | Server provisioning + deploy automation. GitHub push → Forge detects → runs deploy script (git pull, composer install, artisan migrate, cache clear). ~€15/month. |
| **Let's Encrypt via Forge** | SSL auto-provisioned and auto-renewed on first deploy. Zero cost. |
| **GitHub Actions** | CI: run tests on push. Forge handles CD. |
| **Git + GitHub** | Version control. Private repo for full source. This public showcase repo is separate. |

---

## Payments (Milestone 5 — in progress)

| Tool | Use case |
|------|----------|
| **Stripe Checkout + Cashier** | Card payments, Klarna installments (enabled via Stripe dashboard → Norway), webhook handling for payment status updates |
| **Vipps eCommerce API v2** | Primary payment method. Used by the vast majority of Norwegians — the expected default for any Norwegian service business. Deposit collection for new projects. |
| **Vipps Recurring API** | Monthly retainer billing. Approved recurring charges — client authorises once, charged automatically each month. |
| **EHF / Peppol** | B2B invoicing for public sector clients. Mandatory for suppliers to Norwegian government entities. Integrated via Fiken.no API. |

---

## Development tooling

| Tool | Why |
|------|-----|
| **Claude Code** | Primary development partner. Reads `CLAUDE.md` project memory at session start. Generates code, reviews architecture, debugs issues, writes documentation. See [claude-code-workflow.md](claude-code-workflow.md). |
| **serve.bat** | Custom Windows batch script: `php -S 127.0.0.1:8000 -t public/`. Workaround for `php artisan serve` failing on a Windows A:\ drive (known PHP built-in server edge case). |
| **Cursor** | IDE. Used for file navigation and applying AI-generated edits. |
| **Loom** | Short screen recordings for client demos and recruiter showcases. |

---

## Norwegian-specific integrations

| Integration | Purpose | Status |
|-------------|---------|--------|
| Vipps eCommerce API v2 | Primary payments | Milestone 5 |
| Vipps Recurring API | Monthly retainers | Milestone 5 |
| BankID | Identity verification (future) | Backlog |
| EHF / Peppol | Public sector invoicing | Milestone 5 |
| Altinn API | Business registration integration (future) | Backlog |
| Fiken.no API | Accounting integration (future) | Backlog |
| Resend (Norway deliverability) | Transactional email | Live (Milestone 3) |

---

## Conscious exclusions

| What was excluded | Why |
|-------------------|-----|
| **Next.js / React** (for the marketing site) | Overkill for a content marketing site. Server-rendered Blade is faster to build, better for SEO, and requires no Node toolchain. React is planned for future app features. |
| **Laravel Livewire** | Will be added for real-time queue features in the drop-in booking product. Not needed for static marketing pages. |
| **Docker** | Adds complexity for solo development on Windows. Hetzner + Forge handles server setup cleanly. |
| **Inertia.js** | Considered. Rejected — Filament and Blade are the right tools for this use case; Inertia adds complexity without benefit here. |
| **Laravel Octane** | Not needed at this traffic level. Can be added later without architecture changes. |
| **MySQL in development** | SQLite is sufficient and much faster to set up. Differences between SQLite and MySQL are managed at the query/migration level. |
