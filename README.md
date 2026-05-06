# tekavogtil — Norwegian Software Agency Platform

> Founder build in progress. Full-Stack AI-Assisted development showcase.

[![Laravel](https://img.shields.io/badge/Laravel-12-red)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-v3-orange)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-purple)](https://php.net)
[![Tailwind](https://img.shields.io/badge/Tailwind-CSS-06B6D4)](https://tailwindcss.com)
[![AI-Assisted](https://img.shields.io/badge/Dev-AI--Assisted-brightgreen)](https://claude.ai)

---

## What is tekavogtil?

**tekavogtil** is a Norwegian software development agency, built from zero by its founder as a fully AI-assisted project. Every component — brand identity, platform, strategy, content, and marketing — has been designed and shipped using **Claude Code as the primary development partner**.

This repository is the **public showcase** of how the project is structured and built. The full production source is private. What you see here is the architecture, the patterns, the workflow, and the thinking — enough for a developer or recruiter to understand exactly what kind of engineering is happening.

**Language:** Norwegian-first (Bokmål), English toggle  
**Market:** Norwegian SMBs  
**Status:** In private development — Milestones 1–3 complete, Milestone 4 in progress

---

## Project scope

This is not just a website. It is a complete product + agency business built end-to-end through AI-assisted workflows:

| Component | What was built |
|-----------|----------------|
| **Brand & identity** | Naming research, logo design (Nordic T mark), 4 SVG variants, "Fjord & Aurora" design system |
| **12-page bilingual site** | NO/EN marketing site with custom i18n middleware, responsive layouts, glass-morphism design |
| **Laravel 12 platform** | 6 DB migrations, Filament v3 admin panel, session locale, inquiry pipeline, email notifications |
| **Content & strategy** | Website content bible (NO+EN), market research (12 competitors), financial model, investor pitch |
| **Marketing** | 7-day social campaign (Facebook, Instagram, LinkedIn), Norwegian-language captions, fal.ai image script |
| **AI-first workflow** | Claude Code as dev partner, persistent `CLAUDE.md` project memory, skill files, agent-driven QA |

---

## Tech stack

See [tech-stack.md](tech-stack.md) for the full breakdown with reasoning behind every choice.

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.4 |
| Admin panel | Filament v3 |
| Frontend | Blade, Tailwind CSS, Alpine.js |
| Database | SQLite (dev), MySQL (prod) |
| Email | Resend |
| Payments | Stripe Cashier + Vipps (Milestone 5) |
| Hosting | Hetzner VPS + Laravel Forge |
| CI/CD | GitHub Actions → Forge auto-deploy |
| Dev partner | Claude Code (AI-assisted development) |

---

## Build milestones

| # | Milestone | Status |
|---|-----------|--------|
| 1 | Laravel scaffold, Filament setup, 6 DB migrations, admin user | ✅ Complete |
| 2 | Homepage, services browser + detail pages, locale middleware, lang files | ✅ Complete |
| 3 | Inquiry form + email pipeline, contact form, partials, full nav | ✅ Complete |
| 4 | Filament admin resources (CRUD for all entities, role-based access) | 🔄 In progress |
| 5 | Stripe Checkout + Vipps eCommerce + Vipps Recurring | ⏳ Planned |
| 6 | Norwegian SEO, sitemap, Hetzner deployment, SSL, CI/CD | ⏳ Planned |

---

## Screenshots

> See the [screenshots/](screenshots/) folder. Captures are taken after each milestone.

| Page | File |
|------|------|
| Homepage | `screenshots/homepage.png` |
| Services browser | `screenshots/services-browser.png` |
| Service detail | `screenshots/service-detail.png` |
| Pricing page | `screenshots/pricing.png` |
| Inquiry form | `screenshots/inquiry-form.png` |
| Contact form | `screenshots/contact-form.png` |
| Admin panel | `screenshots/admin-panel.png` |
| Language toggle (NO/EN) | `screenshots/language-toggle.png` |

---

## Explore this repo

| File | What's in it |
|------|-------------|
| [ARCHITECTURE.md](ARCHITECTURE.md) | System architecture, data flow, key decisions |
| [case-study.md](case-study.md) | Milestone-by-milestone build narrative with learnings |
| [claude-code-workflow.md](claude-code-workflow.md) | How Claude Code is used as primary development partner |
| [tech-stack.md](tech-stack.md) | Full tech stack with reasoning behind every choice |
| [design-system.md](design-system.md) | "Fjord & Aurora" visual identity and CSS architecture |
| [code-samples/](code-samples/) | Sanitised, reusable code patterns from the project |

---

## The AI-assisted development approach

This project demonstrates **AI-assisted full-stack development** — a developer with solid engineering fundamentals directing an AI code-generation partner, while maintaining full accountability for architecture, security, debugging, and shipping.

**Human role:** architecture decisions, debugging, security review, Norwegian market expertise (Vipps, BankID, EHF, MVA), QA, deployment.  
**AI role:** code generation, scaffolding, refactoring, documentation, test writing, first-pass bug detection.

See [claude-code-workflow.md](claude-code-workflow.md) for the full workflow writeup.

---

## Contact

- **Founder:** Ayyad Anwar
- **LinkedIn:** [linkedin.com/in/ayyad-anwar](https://www.linkedin.com/in/ayyad-anwar)
- **GitHub:** [github.com/adyelmoro](https://github.com/adyelmoro)
- **This repo:** [github.com/adyelmoro/TekavogtilProject-public](https://github.com/adyelmoro/TekavogtilProject-public)

---

*Full production source is private. This repo contains architecture, patterns, and documentation only — enough to understand how the project is built without exposing business logic or client data.*
