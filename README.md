# Jeremy Anderson Tech · Concept 1

**Direction 01: Future Systems · Calibrated Signal**

Brand identity, brand book and working website concept for **Jeremy Anderson Tech**, a founder-led engineering studio in Shrewsbury, Massachusetts. It covers web platforms, applied AI, cybersecurity and managed operations.

| | |
|---|---|
| **Website concept** | https://cptnope.github.io/Jeremy-Anderson-Tech-Concept-1/ |
| **Brand book** | https://cptnope.github.io/Jeremy-Anderson-Tech-Concept-1/brand-book/ |
| **Status** | v1 concept for review, October 2026. Not the live site. |
| **Search** | Excluded with `noindex` on every page and a `robots.txt` that blocks all crawlers |

---

## Contents

1. [The idea](#the-idea)
2. [Repository map](#repository-map)
3. [Brand book](#brand-book)
4. [Identity files](#identity-files)
5. [Design system](#design-system)
6. [Website](#website)
7. [Content rules and placeholders](#content-rules-and-placeholders)
8. [How it was made](#how-it-was-made)
9. [Build, preview, deploy](#build-preview-deploy)
10. [WordPress phase 2](#wordpress-phase-2)
11. [Known limits and next steps](#known-limits-and-next-steps)
12. [Licenses](#licenses)

---

## The idea

**Thesis.** Every client system is a signal. Jeremy Anderson Tech is the calibrated instrument that finds that signal, measures it and locks it steady.

**World.** The design is a modern digital oscilloscope:
- a graticule-black screen with 10 × 8 measurement divisions;
- single-stroke beam traces;
- measurement readouts set in a monospace face;
- four input channels, each with its own color.

**Monogram.** The JAT mark is one continuous trace. It reads as a *step response*: the line enters at rest, the J and the A form the transient, and the T settles at a new trigger level. On the website hero, the trace sweeps as noise three times, triggers, and burns in at logo weight.

**Tagline.** *Systems that hold steady.*

**Positioning line.** Web platforms, AI and security, built and run by one accountable engineering studio.

**Service channels.**

| Channel | Color | Covers | Entry price |
|---|---|---|---|
| **CH1 Platforms** | Cyan `#3CE3F0` | Web design, custom WordPress engineering, custom software and PWAs | Business Site $3,500 |
| **CH2 Intelligence** | Ultraviolet `#9A8CFF` | AI development, automation, private LLMs and RAG, integrations | AI Discovery $1,200 |
| **CH3 Security** | Amber `#FFC23D` | Audits, hardening, SIEM/SOC monitoring, Massachusetts privacy review | Security Audit $850 |
| **CH4 Operations** | Signal `#5EF0A0` | Managed hosting, care plans, performance, SEO/AEO/GEO | Watchdog $350/mo |

---

## Repository map

```
/
├── index.html                  Home
├── services.html               Services and full pricing (+ FAQ)
├── platforms.html              CH1 channel page
├── intelligence.html           CH2 channel page
├── security.html               CH3 channel page
├── operations.html             CH4 channel page
├── work.html                   Work index
├── work-gods-eye-lite.html     Case study
├── work-cytoscape.html         Case study
├── work-chronos-canon.html     Case study
├── about.html                  Studio and founder
├── approach.html               Process, quality bar, terms
├── insights.html               Blog index (links to existing posts)
├── contact.html                Lead form (client-side validation, not connected)
├── sitemap.html                IA, redirect map, WordPress block plan
├── site.css · site.js          Shared styles and behavior (built copies)
├── brand-book/
│   ├── index.html              The brand book (17 sections)
│   └── assets/*.svg            Production logo files
├── PRODUCT.md                  Product truth: users, positioning, prices, evidence
├── DESIGN.md                   Design system record with machine-readable tokens
├── .impeccable/                Impeccable workflow records
│   ├── config.json             Build path (code-first)
│   ├── design.json             DESIGN.md sidecar: ramps, motion, component snippets
│   ├── surfaces/               Surface brief and direction contract
│   └── review/                 Final desktop and mobile captures used in review
├── design/                     All design source and tooling
│   ├── identity/               Wordmark outliner, logo + sprite generators, explorations
│   ├── brand-book-src/         Brand book source (part1–part5.html)
│   ├── site-src/               site.css, site.js and the Python page builder
│   ├── tools/                  build_all.py, capture.py
│   ├── fonts/                  Where to put the font files (not committed)
│   └── review/                 Finish-review record and iteration captures
├── robots.txt                  Disallow all (concept)
└── .nojekyll                   Serve files as-is on GitHub Pages
```

---

## Brand book

`/brand-book/` is a single, navigable page with a section index.

| # | Section | What it contains |
|---|---|---|
| 01 | Positioning | Positioning statement, five differentiators, a current-state audit and the move |
| 02 | Purpose and values | Purpose, mission, vision, six core values |
| 03 | Audiences | Three labeled archetype personas and buying motivations |
| 04 | Brand architecture | Branded house, four channels, engagement models, naming rules |
| 05 | Competitive landscape | Qualitative positioning map and a gap table by competitor category |
| 06 | Personality and voice | Traits, do/don't rules, before/after rewrites, message hierarchy |
| 07 | Tagline | Eight candidates with reasoning, one recommendation |
| 08 | Logo system | Three explorations, construction grid, lockups, color variants, clear space, minimum sizes, misuse |
| 09 | Vector files | SVG source, file list, production rules |
| 10 | Color | Primary, channel, neutral, semantic and paper palettes (HEX, RGB, CMYK); proportion; contrast matrix |
| 11 | Typography | Archivo and Martian Mono, interactive width axis, type scale, pairing rules, licensing |
| 12 | Grid and space | 12-column layout grid, 10 × 8 graticule, spacing tokens, radii, borders, elevation |
| 13 | Imagery and motion | Photo direction and shot list, trace diagrams, 14 authored icons, live motion demo |
| 14 | UI components | Live buttons, fields, channel tags, readout strip, price rows, feedback |
| 15 | Accessibility | Contrast, focus, reduced motion, targets, canvas rules, breakpoints |
| 16 | Applications | Business card, proposal, social profiles, deck, email signature (copyable HTML), apparel, merchandise |
| 17 | Design tokens | `tokens.css`, WordPress `theme.json` and W3C design-token JSON, each with a copy button |

---

## Identity files

All identity elements are SVG. Recolor them through custom properties; never edit the path data.

| File | Use |
|---|---|
| `brand-book/assets/jat-mark.svg` | Monogram, Channel Cyan |
| `brand-book/assets/jat-mark-ice.svg`, `jat-mark-ink.svg` | One-color monogram for dark and light grounds |
| `brand-book/assets/jat-lockup-horizontal.svg` | Primary logo on dark surfaces |
| `brand-book/assets/jat-lockup-horizontal-light.svg` | Primary logo on paper and light surfaces |
| `brand-book/assets/jat-lockup-stacked.svg` | Secondary logo |
| `brand-book/assets/jat-icon.svg` | App icon and favicon source (28/128 corner radius) |

**Construction**
- Artboard: `viewBox="1 6 108 68"` on a 16-unit grid.
- Stroke: 8 units, round caps and joins.
- Levels: trigger level at y 16, baseline at y 64.
- Path: `M9 50 Q9 64 23 64 Q33 64 36 57 L52 16 L68 64 H84 V16` plus the bar `M70 16 H100`.

**Wordmark.** Outlined from Archivo at width 125 and weight 620, tracked +40. "TECH" is weight 380, tracked +320, followed by a 12-interval measurement ruler.

**Clear space and minimum sizes**
- Clear space: one division (X = 16 units) on every side.
- Horizontal lockup: 160 px / 38 mm minimum.
- Stacked lockup: 96 px / 24 mm minimum.
- Monogram: 16 px / 5 mm minimum.

**Recoloring.** Use the custom properties `--mk`, `--lk-name`, `--lk-tech` and `--lk-rule`, or `currentColor`.

---

## Design system

`DESIGN.md` is the normative record: YAML tokens followed by prose. `.impeccable/design.json` holds the extras that YAML can't: tonal ramps, motion, breakpoints and drop-in component snippets.

**Color**

| Role | Value |
|---|---|
| Graticule (page) | `#0A0C0F` |
| Ink 950 (screens, footer, inputs) | `#07090B` |
| Panel | `#10141A` |
| Raised | `#161B22` |
| Line | `#222A34` |
| Line strong | `#2E3845` |
| Control border (3.35:1) | `#5A6675` |
| Ice (text) | `#E6ECF2` |
| Muted text | `#A3AFBC` |
| Subtle text | `#7D8996` |
| Channels | `#3CE3F0` · `#9A8CFF` · `#FFC23D` · `#5EF0A0` |
| Danger | `#FF5C7A` |
| Paper set (print and light) | `#F3F5F7` · `#465260` · deep cyan `#007C88` · deep UV `#5B4BD6` · deep amber `#8A5800` · deep signal `#0A7646` |

**Named rules**
- **The Identifier Rule.** Channel colors only label a channel or a state. They always appear with the CH number and name.
- **The Cyan-Carries-Ink Rule.** Text on Channel Cyan is always Graticule (12.5:1).
- **The Data-Only Mono Rule.** Martian Mono is only for prices, timings, IDs and code.
- **The Bezel Rule.** Glass only appears over a moving trace.

**Type**
- Archivo variable: width 125 for display, 118 for headlines, 112 for titles, 100 for body.
- Martian Mono at width 90 for readouts.

| Style | Weight | Size | Line height |
|---|---|---|---|
| Display | 640 | clamp(3.25–6rem) | 0.92 |
| Body | 400 | 1.0625rem | 1.6 |

**Space and shape.** 4 px base (4, 8, 12, 16, 24, 32, 48, 64, 96, 128). Radii are 2 px for tags, 6 px for controls and 12 px for panels. Borders are 1 px hairlines.

**Motion**
- Ease-out `cubic-bezier(.16,1,.3,1)`.
- Durations: 120, 200 and 320 ms for UI; 760 ms per hero sweep; 420 ms burn-in.
- One trigger sequence per page, after which canvases stop drawing.

**Accessibility**
- WCAG 2.2 AA throughout.
- 2 px cyan focus ring with a 2 px offset.
- 44 px minimum targets.
- Reduced motion renders the locked final frame immediately.
- Decorative canvases are `aria-hidden`; every diagram has a `<title>` text alternative.

---

## Website

**15 static pages.** HTML, CSS and vanilla JS. No framework and no hosting build step.

| Concept page | Planned production URL | Schema |
|---|---|---|
| `index.html` | `/` | WebSite, ProfessionalService |
| `services.html` | `/services/` | FAQPage |
| `platforms.html` | `/services/platforms/` | Service, OfferCatalog |
| `intelligence.html` | `/services/intelligence/` | Service, OfferCatalog |
| `security.html` | `/services/security/` | Service, OfferCatalog |
| `operations.html` | `/services/operations/` | Service, OfferCatalog |
| `work.html` | `/work/` | — |
| `work-gods-eye-lite.html` | `/project/gods-eye-lite/` | SoftwareApplication |
| `work-cytoscape.html` | `/project/cytoscape-immune-defense/` | VideoGame |
| `work-chronos-canon.html` | `/project/chronos-canon/` | SoftwareApplication |
| `about.html` | `/about/` | Person |
| `approach.html` | `/approach/` | HowTo |
| `insights.html` | `/blog/` | Blog |
| `contact.html` | `/contact/` | ContactPage |
| `sitemap.html` | — | — |

Phase 2 splits the channel sections into individual service pages using the anchors already in place (`platforms.html#wordpress` → `/services/platforms/wordpress/`, and so on). `sitemap.html` holds the full IA, the redirect map and the WordPress block plan.

**Signature interactions**
- **Hero:** a noise-to-lock trace with centre-axis ticks, plus measurement cursors that follow the pointer (X/Y in divisions).
- **Inner pages:** each draws its channel's waveform.
- **Home comparison:** "four vendors" versus "one studio" traces.

---

## Content rules and placeholders

Ground rules this concept follows. Keep them in the production site.

- **Evidence.** The only case studies are Jeremy's own public projects: God's Eye Lite, Cytoscape: Immune Defense, and Chronos & Canon. Client work is mentioned only as confidential and published with permission.
- **No inventions.** There are no testimonials, client logos, invented metrics or team members.
- **Prices and labels.** Prices, timelines and the "Most requested" / "Most popular" labels come from the current jeremyanderson.tech/services page.
- **Founder facts** come from the current /about page.

**Replace before launch**
- **Founder portrait.** Two labeled placeholders, on the home page and on About. Brand book §13 lists the shots to commission.
- **Project schematics.** These are authored SVG diagrams, labeled as schematics. They can stay, or sit beside real screenshots.
- **Contact form.** It validates in the browser only. Connect it to a WordPress REST endpoint with a nonce and a honeypot.
- **Insights.** Cards link to posts on the current site, which keep their URLs.
- **Response times.** The site states no response time beyond the published care-plan terms. Add one only if Jeremy commits to it.

---

## How it was made

The work was built with Claude using the **Impeccable** design skill in the order the creative brief asked for: research, brand strategy, identity exploration, full brand book, website architecture, design system, working website, testing, refinement.

1. **Research.** Audited jeremyanderson.tech (home, about, services and pricing, projects, blog) and the three live project demos.
2. **Product record.** `impeccable init` interviewed Jeremy and wrote `PRODUCT.md`. Decisions: own projects only, a balanced SMB-led audience, a static concept first, and a code-first build path.
3. **Direction.** Seven grounded candidates were drawn from the oscilloscope, engineering-drawing, datasheet, cartography and identity-program worlds. The Impeccable roll (seed `8ec31921`, degraded mode, so no catalog challengers) assigned **Calibrated Signal**; Jeremy confirmed it over the "Construction Set" pick and the category standard. The direction contract is in `.impeccable/surfaces/site-index-html.md`.
4. **Identity.**
   - Three monogram concepts: Trace (selected), Graticule cell and Lissajous.
   - Two refinement rounds: the first trace read as "UAT", so the J tail was shortened.
   - The wordmark was outlined from Archivo with fontTools.
   - Explorations are in `design/identity/exploration/`.
5. **Brand book.** Written section by section, rendered headless at desktop and mobile, and fixed in batches. Fixes included mockup trace alignment, grid orphans and icon-grid gaps.
6. **Website.** Generated from Python templates (`design/site-src/build/`), so markup maps one to one onto the planned Gutenberg blocks.
7. **Review.** The Impeccable detector ran, then a fresh reviewer did two rounds. The full findings and fixes are in `design/review/finish-review.md`, and the final captures are in `.impeccable/review/`.
8. **Record.** `DESIGN.md` and `.impeccable/design.json` were written from the built system.

---

## Build, preview, deploy

**Requirements.** Python 3.10+. Optional: `fontTools` to re-outline the wordmark, and `playwright` for captures.

```bash
# Rebuild everything served by Pages (logos, sprite, brand book, all site pages)
python3 design/tools/build_all.py

# Also re-outline the wordmark from the font (put Archivo-Variable.ttf in design/fonts first)
pip install fonttools
python3 design/tools/build_all.py --wordmark

# Preview locally
python3 -m http.server 8000      # then open http://localhost:8000

# Capture desktop + mobile screenshots into .impeccable/review/
pip install playwright && playwright install chromium
python3 design/tools/capture.py index services platforms brand-book/index
```

The build is deterministic: running `build_all.py` on an unchanged checkout produces no git diff.

**Where to edit**

| To change | Edit |
|---|---|
| Copy, pages, prices, schema | `design/site-src/build/pages.py` (home, services, channels) and `pages2.py` (work, cases, about, approach, insights, contact, sitemap) |
| Header, footer, page head, metadata | `design/site-src/build/common.py` |
| Project schematics and architecture diagrams | `design/site-src/build/plates.py` |
| Styles and behavior | `design/site-src/site.css` and `site.js` (copied to the root on build) |
| Brand book | `design/brand-book-src/part1–5.html` |
| Logo geometry | `TRACE` / `BAR` in `design/identity/logos.py` and `sprite.py` |

**Deploy.** In GitHub, open **Settings → Pages → Build and deployment**, choose **Deploy from a branch**, then select `main` and `/ (root)`. Every push to `main` republishes.

---

## WordPress phase 2

Hosting is planned on Jeremy's Cloudways/DigitalOcean infrastructure.

- **Block theme.** Use the `theme.json` in brand book §17 for the palette, fonts (self-hosted WOFF2), font sizes, spacing and button styles.
- **Custom blocks** (`jat/` namespace):
  - `hero-scope`, `channel-strip`, `channel-rows`, `versus`
  - `price-table`, fed from a Packages custom post type
  - `steps`, `work-feature`, `case-facts`
  - `faq`, which emits FAQPage schema
  - `close-cta`
  - `lead-form`, which posts to a REST endpoint with a nonce and a honeypot
- **Content types:** Packages, with channel taxonomy, price, timeline and badge fields; and Projects, keeping the existing `/project/{slug}/` URLs.
- **Redirects.** Existing project and blog URLs stay as they are. Consolidated service pages and tag archives get 301s, listed in `sitemap.html`.
- **Production setup.** Remove `noindex` and `robots.txt` at launch. Keep the canonical URLs as built.

---

## Known limits and next steps

- **Scope of the final review.** The last fix batch (inline channel IDs on phones, diagram width, axes on mobile) has not been re-scored by the reviewer.
- **Possible upgrades** the reviewer noted:
  - phosphor persistence on the monogram;
  - more measurement readouts in the case studies;
  - more variety in home section heads.
- **Optional `/lab/` page.** An interactive "Signal Lab" showcase is in the IA as a phase 3 idea.
- **Font loading.** Fonts load from Google Fonts in the concept. The production theme should self-host them.

---

## Licenses

- **Design, copy and code** © 2026 Jeremy Anderson Tech. All rights reserved unless stated otherwise.
- **Fonts:** Archivo (Omnibus-Type) and Martian Mono (Evil Martians), both under the SIL Open Font License 1.1, loaded from Google Fonts.
- **Third-party projects referenced:** God's Eye Lite, Cytoscape: Immune Defense and Chronos & Canon live in their own repositories under [github.com/CptNope](https://github.com/CptNope).
