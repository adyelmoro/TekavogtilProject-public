# Claude Code as a Development Partner

A detailed writeup of the actual workflow used to build tekavogtil — for developers and recruiters who want to understand what AI-assisted full-stack development looks like in practice.

---

## The core idea

Traditional development:
```
Developer writes code → runs it → debugs → ships
```

AI-assisted development:
```
Developer decides architecture → AI generates code → Developer reviews + runs → debugs collaboratively → ships
```

**What changes:** the author of most code *lines* is the AI.  
**What does not change:** the human must understand what the code does, validate it is correct, catch mistakes, and be responsible for everything that ships.

This is not "auto-generating an app and clicking deploy." It is closer to being a technical lead reviewing and directing a developer who types extremely fast but occasionally makes incorrect assumptions.

---

## Setup: persistent project memory with CLAUDE.md

The biggest challenge in multi-session AI development is **context loss**. Each new conversation starts blank — the AI has no memory of previous sessions, decisions made, or code written.

The fix: a `CLAUDE.md` file at the project root that functions as the project's brain.

### What CLAUDE.md contains

```markdown
## SESSION SUMMARY
(Overwritten at the end of every session — new sessions read this first)
- Last session: what was built, what decisions were made, what's next

## Decisions Log
| Date | Decision | Notes |
(Every architectural choice, timestamped)

## Architecture
- Full project folder structure with file status (✅ complete / 🔄 in progress)
- Database schema
- Routes table
- Standing rules that apply every session

## Build Queue
- Milestone tracker with current status
- Next step clearly stated
```

At the start of every session: Claude reads `CLAUDE.md` before doing anything else.  
At the end of every session: the `SESSION SUMMARY` section is overwritten with what was done.

This turns a stateless tool into a persistent development partner. Across 15+ sessions, the project stays architecturally coherent because the memory is written down, not held in context.

---

## Session start protocol

Every session begins with the same sequence:

1. **Claude reads `CLAUDE.md`** — gets full current context
2. **Claude reads critical files** — whichever files are needed for the current milestone (e.g. the migration files before adding a new one, the controller before editing it)
3. **Discuss and confirm scope** — agree on exactly what we are building today before writing a line
4. **Architecture first** — any structural decisions are discussed and written to CLAUDE.md before code is generated

This prevents the common failure mode of "just start coding" that results in wrong architecture being half-implemented across multiple files.

---

## Skill files: repeatable tasks without re-explaining

Some tasks recur across sessions with the same rules. Rather than re-explain rules every time, dedicated skill files encode the rules once:

| File | Governs |
|------|---------|
| `skills/logo-creator.md` | SVG logo rules: tight viewBox, transparent background, gradient ID namespacing, no tagline, 4-file set |
| `skills/page-builder.md` | HTML/Blade page rules: design system classes, mobile nav pattern, i18n attribute wiring, scroll animations, section padding |
| `skills/content-writer.md` | Norwegian copy tone: no em dashes, no AI jargon, no "vi er lidenskapelige om", concrete facts over adjectives, one CTA per section |
| `skills/business-pitch-doc.md` | Pitch document formatting and structure rules |

When a task falls under a skill domain, Claude reads the skill file first. This produces consistent output across sessions without re-briefing.

---

## Code generation workflow

For each feature or file:

1. **Human describes what to build** — in plain language: "Build a Filament resource for the Service model with bilingual name fields, price, category select, tech stack as tags, and a visibility toggle"
2. **Claude generates the file** — complete implementation
3. **Human reads every line** — not to check style, but to understand what the code does and catch incorrect assumptions
4. **Human runs it** — confirms it works or reports the error
5. **Iterate if needed** — bugs are debugged collaboratively (see below)

The key discipline: **read every generated line before running it.** You will be asked to explain this code. You are responsible for it.

---

## Debugging workflow

When something fails:

1. Human runs the code, gets an error or wrong behaviour
2. Human pastes: the error message + the relevant file content (controller, view, etc.)
3. Claude diagnoses: explains why it's happening and proposes a fix
4. Human evaluates the fix (does it make sense?) before applying
5. Human applies and re-runs
6. Repeat until resolved

**The rule:** never apply a fix you don't understand. If the diagnosis is unclear, ask for an explanation before proceeding. This is how you catch AI mistakes before they compound.

### Real examples from this project

| Bug | Diagnosis | Fix |
|-----|-----------|-----|
| `artisan serve` silently fails on Windows A:\ drive | PHP's built-in server has a path resolution edge case for non-standard drive letters | `php -S 127.0.0.1:8000 -t public/` via batch script |
| `@context` in JSON-LD causes Blade parse error | Blade preprocessor interprets `@context` as an unknown directive | Use `@@context` to output a literal `@` |
| `@else` fails after a word character in a Blade expression | Blade's `\B@` regex won't match `@else` immediately following a word character | Use `@php` conditional assignment instead |
| Filament form not saving JSON array fields | TagsInput stores comma-separated strings by default; the `Service` model needs a cast to `array` | Add `'includes' => 'array'` to model `$casts` |

---

## What AI does well in this stack

- **Volume and repetition** — generating 300 translation keys, 12 Blade views, 6 database migrations, all form validation rules. Days of manual work done in hours.
- **Current API knowledge** — Laravel 12 + Filament v3 are recent enough that the AI rarely hallucinates method names. It knows the correct `Envelope`, `Content`, `Address` Mailable API, the correct Filament `Form::schema()` builder, the Blade `@section`/`@yield` pattern.
- **Proactive gotcha detection** — flagged `@@context`, `@@keyframes`, and `@else` Blade edge cases before they hit production.
- **First drafts that need minor tweaks**, not rewrites. Typically a generated file needs 5–10 lines changed, not a full redo.
- **Documentation** — writing inline comments, README sections, this very file.

---

## Where human judgment is essential

- **Database schema design** — the 6-table structure and its relationships are business decisions. AI generates migrations; humans design the model.
- **Norwegian market requirements** — Vipps before Stripe, EHF invoicing for public sector clients, MVA registration threshold, GDPR/Datatilsynet compliance. No AI reliably knows Norwegian-specific regulations.
- **Security review** — which routes need auth guards, how roles map to Eloquent query scopes, what data gets logged, what gets exposed in API responses.
- **Architecture choices** — why Laravel 12 and not 13, why Blade and not React, why Filament and not a custom admin. These have consequences that compound over the project lifetime.
- **Live environment debugging** — Windows-specific issues, production server behaviour, real database performance, actual email deliverability.

---

## What I would tell another developer considering this workflow

**1. Your architecture still matters.** AI generates code, not system design. A wrong schema costs the same to fix whether AI wrote the migration or you did.

**2. Read every line.** Not to catch typos — to know what your own project does. You will explain this code in interviews. You are responsible for it in production.

**3. Persistent memory is the unlock.** `CLAUDE.md` is why this project stays coherent across 15+ sessions. Without structured project memory, every session restarts from scratch and coherence breaks down after session 3.

**4. Be honest about what you're building.** AI-assisted development is not "I press a button and an app appears." It is a workflow with human judgment at every decision point. The gap between "AI wrote it" and "I can explain, debug, and extend it" is the gap between a portfolio project and a production system.

**5. Norwegian context requires human expertise.** Integration with Vipps, BankID, EHF, Altinn, MVA registration — this knowledge has to come from the developer. It is not reliably in any AI training data at the level of detail needed for production.

---

## Tools used

| Tool | Role |
|------|------|
| **Claude Code** | Primary development partner — architecture, code generation, debugging, documentation |
| **Cursor** | IDE — file navigation, code review, applying AI-generated edits |
| **GitHub** | Version control — private source repo + this public showcase repo |
| **Loom** | Recording short demo walkthroughs for recruiters and clients |
