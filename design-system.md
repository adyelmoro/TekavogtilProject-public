# Design System — "Fjord & Aurora"

The tekavogtil visual identity is built around a system called **Fjord & Aurora** — deep Norwegian fjord tones as the base, with electric teal aurora glow accents.

The design was initially prototyped in **Stitch by Google**, then ported into a self-contained CSS architecture in `layouts/app.blade.php`. All styles are self-contained (no separate CSS file) — the design system lives in a `<style>` block in the layout, using Tailwind for utilities and custom CSS classes for the glass-morphism components.

---

## Colour palette

```css
/* ── Core backgrounds ── */
--color-background:  #020d14;   /* Deep fjord — main page background */
--color-surface-low: #0b0f10;   /* Alternate section background */
--color-surface-mid: #1d2022;   /* Card backgrounds, panels */

/* ── Accent ── */
--color-teal:        #00f1fe;   /* Electric teal — CTAs, icons, active states, glow */

/* ── Text ── */
--color-on-surface:  #e0e3e5;   /* Primary text */
--color-on-muted:    #c3c7cb;   /* Secondary text, descriptions */
--color-outline:     #8d9195;   /* Labels, captions, footer text */

/* ── Borders ── */
--border-subtle:     rgba(255, 255, 255, 0.07);  /* Card borders at rest */
--border-hover:      rgba(0, 241, 254, 0.45);    /* Card borders on hover */
```

---

## Tailwind config extension

The Tailwind config inline in `layouts/app.blade.php` extends the defaults with these tokens:

```js
tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'background':  '#020d14',
        'surface-low': '#0b0f10',
        'surface-mid': '#1d2022',
        'teal':        '#00f1fe',
        'on-surface':  '#e0e3e5',
        'on-muted':    '#c3c7cb',
        'outline':     '#8d9195',
      },
      borderRadius: {
        DEFAULT: '6px',
        sm:      '4px',
        md:      '6px',
        lg:      '8px',
        xl:      '12px',
        '2xl':   '16px',
        full:    '9999px',
      },
      fontFamily: {
        sans:    ['Inter', 'sans-serif'],
        display: ['Space Grotesk', 'sans-serif'],
      },
    },
  },
}
```

See [code-samples/tailwind-design-tokens.css](code-samples/tailwind-design-tokens.css) for the standalone CSS custom-property version.

---

## Typography

| Role | Font | Weight | Size |
|------|------|--------|------|
| H1 (hero headings) | Space Grotesk | 600 | `clamp(40px, 6vw, 64px)` |
| H2 (section headings) | Space Grotesk | 500 | `clamp(28px, 4vw, 48px)` |
| H3 (subsection) | Space Grotesk | 500 | `clamp(20px, 3vw, 32px)` |
| Body / UI | Inter | 400 | 16px |
| Small / descriptions | Inter | 400 | 14px |
| Label caps | Inter | 700 | 11px, `letter-spacing: 0.12em`, `text-transform: uppercase` |
| Trust stats | Space Grotesk | 600 | 44px |

---

## Border radius scale

| Component | Radius |
|-----------|--------|
| `.glass-card` (service cards, content cards) | `8px` |
| `.glass-panel` (floating panels, modals) | `10px` |
| `.btn-primary`, `.btn-ghost` | `6px` |
| `.chip` (category tags, badges) | `4px` |
| `.lang-btn` (language toggle) | `4px` |
| Nav bar | `0` (full-width strip, no radius) |
| Avatar / dot indicators | `9999px` (full circle only) |

---

## Key CSS class reference

### Navigation

```css
.glass-nav {
  position: fixed;
  top: 0;
  width: 100%;
  backdrop-filter: blur(12px);
  background: rgba(2, 13, 20, 0.82);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  z-index: 50;
}
```

### Cards

```css
.glass-card {
  background: #1d2022;
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 8px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.glass-card:hover {
  border-color: rgba(0, 241, 254, 0.45);
  box-shadow: 0 0 24px rgba(0, 241, 254, 0.08);
}
```

### Buttons

```css
.btn-primary {
  background: #00f1fe;
  color: #020d14;
  font-weight: 700;
  border-radius: 6px;
  padding: 12px 28px;
  transition: box-shadow 0.2s, transform 0.1s;
}

.btn-primary:hover {
  box-shadow: 0 0 20px rgba(0, 241, 254, 0.35);
}

.btn-primary:active {
  transform: scale(0.97);
}

.btn-ghost {
  border: 1px solid #00f1fe;
  color: #00f1fe;
  border-radius: 6px;
  padding: 11px 28px;
  transition: background 0.2s, color 0.2s;
}

.btn-ghost:hover {
  background: #00f1fe;
  color: #020d14;
}
```

### Aurora effects

```css
/* Hero background — two radial aurora gradients */
.aurora-gradient {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(ellipse 60% 50% at 20% 40%, rgba(0, 241, 254, 0.07) 0%, transparent 70%),
    radial-gradient(ellipse 40% 60% at 80% 60%, rgba(0, 100, 200, 0.06) 0%, transparent 70%);
  pointer-events: none;
}

/* Blurred glow blob — placed behind content sections */
.aurora-glow {
  position: absolute;
  border-radius: 9999px;
  filter: blur(80px);
  pointer-events: none;
  opacity: 0.15;
}

/* Animated 1px shimmer line */
.aurora-band {
  position: absolute;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(0, 241, 254, 0.4), transparent);
  animation: aurora-slide 4s ease-in-out infinite;
}

@keyframes aurora-slide {
  0%, 100% { transform: translateX(-100%); opacity: 0; }
  50%       { transform: translateX(100%);  opacity: 1; }
}
```

> **Note:** In Blade templates, use `@@keyframes` not `@keyframes` inside `<style>` blocks to avoid Blade directive collision.

### Scroll reveal

```css
.reveal {
  opacity: 0;
  transform: translateY(28px);
  transition: opacity 0.6s ease, transform 0.6s ease;
}

.reveal.visible {
  opacity: 1;
  transform: translateY(0);
}
```

```js
// Scroll reveal — IntersectionObserver (added at bottom of every page)
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
```

---

## Section spacing

| Context | Padding |
|---------|---------|
| Full-page sections (desktop) | `py-[120px]` — one focus per section, generous breathing room |
| Full-page sections (mobile) | `py-16 md:py-[120px]` |
| Alternate section backgrounds | `#020d14` and `#0b0f10` alternate for visual separation |

---

## Logo — "Nordic T"

The logo is an architectural T letterform:

- Wide crossbar, narrow stem
- Small cursor notch at the stem base (signals software / development)
- Teal dot accent floating top-right of the crossbar
- Liquid glass fill: base gradient → glass overlay → edge stroke → glow bloom → highlight ellipse

**Files:**
- `assets/logo-wide.svg` — wide header logo (dark theme)
- `assets/logo-icon.svg` — square icon (dark theme)
- `assets/logo-wide-light.svg` — wide header (light theme, pending)
- `assets/logo-icon-light.svg` — square icon (light theme, pending)

**Wordmark construction:**
Single `<text>` element with `<tspan>` for weight split:
- `tek` → Outfit 700 (bold, white)
- `avogtil` → Outfit 300 (light, muted blue-white)

Weight contrast creates the visual separation — no dot, no space, no separator needed.

---

## Dark / light mode

The site is dark-mode only in current production. CSS custom properties are defined in `:root` with commented `html.light` overrides ready for a future toggle. The switch requires no CSS refactor — only uncommenting the override block and wiring a toggle button.
