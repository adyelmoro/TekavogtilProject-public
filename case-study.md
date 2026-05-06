# Build Case Study — tekavogtil

A milestone-by-milestone narrative of building a Norwegian software agency platform from zero using AI-assisted development throughout. Written as a honest account of what was easy, what was hard, and what the AI actually helped with.

---

## Before the code: brand and strategy first

Before a single migration was written, the project went through a full brand and strategy phase:

- **Naming research** — tested 5 name concepts against Norwegian market feel, domain availability, and visual workability. Landing on *tekavogtil* ("tek" = tech, "av og til" = occasionally/on and off — a subtle Norwegian phrase hidden inside a technical word).
- **Logo design** — 5 concepts sketched in detail (Nordic T, Monogram Stack, Fjord Notch, Signal Wave, Code Bracket). Selected Nordic T: architectural T letterform, cursor notch at the stem base, teal dot accent top-right.
- **Design system** — "Fjord & Aurora" colour palette, typography pairing (Space Grotesk + Inter), border radius scale, CSS class architecture, glass-morphism component patterns.
- **Business documents** — investor pitch deck (DOCX), 12-month financial model with quarterly revenue targets (XLSX), market research covering 12 Norwegian competitors with keyword clusters and GTM roadmap.
- **Website content bible** — all copy for all 12 pages written first in Norwegian, then English, before any Blade file was created. Tone rules established: no AI jargon, no em dashes, concrete over adjectives, one CTA per section.
- **Social media campaign** — 7-day pre-launch campaign across Facebook, Instagram, LinkedIn. Norwegian-language captions (Bokmål). AI image generation script using fal.ai API.

**Why this order?** Building a platform without knowing what it says or looks like is backwards. Every architecture decision (what data to store, what routes to expose, what emails to send) flows from the content and business model. The content bible made the data model obvious.

---

## Milestone 1: Scaffold + infrastructure

**Goal:** Working Laravel 12 project, Filament admin panel live, all 6 database migrations applied, admin user created.

**What we built:**
- Laravel 12.58.0 scaffolded with Composer (Filament v3, Cashier, Sanctum, Resend drivers)
- 6 database migrations: `users` (with `role` column), `services`, `inquiries`, `clients`, `projects`, `invoices`
- Filament v3 wired — `User` model implements `FilamentUser`, `canAccessPanel()` returns true for owner/staff roles
- `SetLocale` middleware registered in the HTTP kernel middleware group
- `serve.bat` workaround for `artisan serve` failing on Windows A:\ drive

**What was hard:**
- **Windows A:\ drive path issue.** `php artisan serve` silently fails when the project root is on a non-standard drive letter. The fix — `php -S 127.0.0.1:8000 -t public/` via a batch script — took debugging to find.
- **Filament user access wiring.** The `FilamentUser` interface + `canAccessPanel()` method is not obvious from the docs at first glance. Getting it wrong blocks access to `/admin` with a silent redirect.

**AI contribution:**
- Generated all 6 migrations from a plain-English data model description in one pass
- Wired Filament user access correctly on the first attempt
- Diagnosed the Windows A:\ `artisan serve` issue and proposed the batch script workaround

---

## Milestone 2: Public pages

**Goal:** Homepage, services browser and all 6 detail pages live with bilingual content, i18n middleware working.

**What we built:**
- `lang/no/site.php` and `lang/en/site.php` — approximately 300 translation keys each, covering all nav, footer, and page-specific strings
- `SetLocale` middleware + `LocaleController` (GET `/lang/{locale}`)
- `HomeController`, `ServiceController` (index + show), full routing in `routes/web.php`
- `layouts/app.blade.php` — design system CSS, Tailwind config inline, Space Grotesk + Inter from Google Fonts, full responsive nav with mobile hamburger
- `home.blade.php` — hero, services strip (6 cards from DB), how-it-works, trust stats, CTA banner, footer
- `services/index.blade.php` — services browser with category chips
- `services/show.blade.php` — service detail with includes, tech stack, CTA
- `ServiceSeeder` — seeds all 6 services from the content bible with full NO/EN copy

**What was hard:**
- **~600 translation strings in two languages.** Keeping them consistent, correctly keyed, and without missing keys took care. A single missing key silently outputs the key name rather than failing.
- **Blade `@@context` collision.** JSON-LD structured data uses `@context` which Blade tries to interpret as a directive. Required `@@context` everywhere in schema blocks. Caught before production.

**AI contribution:**
- Generated all translation strings from the content bible in one structured pass — split by page namespace (home.*, tj.*, nav.*, footer.*)
- Built all Blade views from a layout spec and design-system reference without requiring iterative correction
- Flagged the `@@context` directive collision proactively

---

## Milestone 3: Forms + email pipeline

**Goal:** Inquiry form at `/start` and contact form at `/kontakt` live, email notifications working, all navigation wired.

**What we built:**
- `InquiryController` (GET/POST `/start`) — form validation, Inquiry record creation, redirect with flash message
- `ContactController` (GET/POST `/kontakt`) — same pattern
- `NewInquiry` Mailable — reply-to set to submitter's email, Norwegian subject line
- `NewContact` Mailable — same
- `mail/new-inquiry.blade.php` + `mail/new-contact.blade.php` — email templates
- Phone country-code dropdown on inquiry form — custom HTML select with flag characters, hidden input for the full number
- All `#` placeholder hrefs replaced — every CTA in nav, footer, hero, and service pages points to a real route
- Verified: all 12 public routes return HTTP 200

**What was hard:**
- **`@else` after a word character.** Blade's `\B@` regex for directive detection fails when `@else` or `@endif` immediately follows a word character (e.g. `{{ $val }}@else`). Solution: use `@php` conditional assignments instead of inline Blade control flow.
- **Phone country-code UX.** Building an accessible dropdown that stores `+47 93 92 91 40` (not just `93 92 91 40`) required a custom hidden input + JS sync pattern.

**AI contribution:**
- Generated both Mailable classes and email templates in one pass with correct Laravel 12 API usage
- Identified the `@else` regex edge case before it caused a production 500 error
- Wrote all form validation rules with correct Norwegian field labels and error messages

---

## Milestone 4: Filament admin resources (in progress)

**Goal:** Full CRUD admin panel for all entities. Role-based access (owner/staff/subcontractor).

**What we've built so far:**
- `ServiceResource` — edit name (NO/EN), starting price, category, includes (TagsInput), tech stack (TagsInput), visibility toggle, drag-reorder by sort_order
- `InquiryResource` — view submissions, filter by status (new/read/quoted/converted), change status from table row
- `ClientResource` — manage clients with org number, contact details

**What's next:**
- `ProjectResource` — Kanban pipeline: Lead → Quoted → Active → Delivered → Retained
- `InvoiceResource` — invoice log with Stripe/Vipps status
- Role-based column/action visibility: staff see only assigned projects; subcontractors can view + update status only; financials visible to owner only

---

## Milestones 5–6 (planned)

**Milestone 5 — Payments:**
- Stripe Checkout for card + Klarna payments
- Vipps eCommerce API v2 for deposit collection
- Vipps Recurring API for monthly retainers
- Webhook handlers for both Stripe and Vipps status updates

**Milestone 6 — Deploy:**
- Norwegian SEO: `hreflang` tags, Norwegian schema.org markup, sitemap.xml
- Hetzner VPS provisioned via Laravel Forge
- SSL via Let's Encrypt (auto by Forge)
- GitHub → Forge push-to-deploy CI/CD
- Switch SQLite → MySQL, run production migrations

---

## Reflection: what AI-assisted development actually looks like

### What AI handled well
- **Volume tasks** — generating 600 translation strings, 12 Blade views, 6 migrations, form validation rules. These would have taken days manually. With AI, they took hours.
- **API knowledge** — Laravel 12 + Filament v3 API is very current. The AI rarely hallucinated method names or parameters for these specific versions.
- **Proactive error catching** — flagged `@@context`, `@@keyframes`, and `@else` Blade gotchas before they hit production.
- **First drafts that needed minor tweaks**, not rewrites.

### Where human judgment was essential
- **Schema design** — the 6-table database structure (and the relationships between inquiry → project → invoice) is a business decision, not a code decision. AI generated the migration files; the human designed the model.
- **Norwegian market decisions** — Vipps before Stripe, EHF invoicing awareness, MVA registration threshold, GDPR compliance approach. These require domain knowledge no AI reliably has.
- **Security decisions** — who can access what in the admin panel, how roles map to Eloquent scopes, what data to log.
- **Debugging live environment issues** — the Windows A:\ drive bug, Filament version-specific behaviour, real server configuration.
- **Reading every line** — the human reviewed every generated file before running it. This is non-negotiable.
