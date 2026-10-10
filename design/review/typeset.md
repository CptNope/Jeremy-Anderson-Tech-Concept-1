# Typesetting record (`/impeccable typeset`, Oct 9 2026)

**Scope:** the overview, all 15 site prototype pages and the brand book.
**Starting point:** 131 type findings from the Impeccable detector (`detect --scope type`). Font sizes had drifted into about 20 one-off values (.5625 to 1.1875rem plus raw px), and headings at neighboring levels were nearly the same size. Mono text inherited heading weights the font never loads, and the brand book's type scale didn't match what the site used.

## The system: ten roles

Each role sets size, weight, width, leading and tracking together. Components pick a role and never set a raw size. The tokens live in `site.css`, the brand book and §17 (`--jat-text-*`, the `theme.json` `fontSizes`, `tokens.json` composites). The mapping from selector to role is kept in `design/tools/typeset_roles.py`.

| Role | Token | Size | Archivo wdth · wght | Leading | Used for |
|---|---|---|---|---|---|
| Display | `--t-display` | clamp(3.25–6rem) | 125 · 640 | .92 | Hero headline only |
| Page | `--t-page` | clamp(2.5–4.5rem) | 125 · 640 | .95 | Page titles, closing statement |
| Section | `--t-section` | clamp(1.875–2.75rem) | 118 · 620 | 1.05 | Every h2 |
| Title | `--t-title` | clamp(1.375–1.75rem) | 112 · 600 | 1.15 | Card and channel titles (h3) |
| Item | `--t-item` | 1.125rem | 108 · 600 | 1.35 | Steps, prices, FAQ, list headings |
| Lede | `--t-lede` | clamp(1.125–1.3125rem) | 100 · 400 | 1.5 | Intro paragraphs |
| Body | `--t-body` | 1.0625rem | 100 · 400 | 1.6 | Running text |
| Small | `--t-small` | .9375rem | 100 · 430 | 1.55 | Secondary text, captions, hints |
| Label | `--t-label` / `--t-label-long` | .75rem / .8125rem | Martian 90 · 400 | — | Readouts, tags, channel IDs |
| Data | `--t-data` | 1.0625rem | Martian 90 · 400 | — | Prices, timings |

Each step from body up is at least 1.25×. Small text on near-black gets weight 430 (`--w-small`) so it doesn't look thin.

## What changed

**Scale and roles**
- 18 heading and label selectors in `site.css`, 7 in the overview and 30 in the brand book were moved onto roles. The leftover literal sizes were folded into the nearest role.
- Inline sizes in the page templates became the `.small`, `.t-item` and `.spec-table` classes.
- The brand book's §11 type scale now shows the ten real tokens with their real values. Its meta column was widened so token names no longer break mid-name.
- Specimen labels in §11 use the Title role, and the alphabet specimen wraps inside its card.

**Mono**
- Martian Mono renders only at 400 or 500, the range the page loads. The concept bar, channel IDs, timeline years, prices, swatch values and verdict tags all used to inherit 600 or 430 and were drawn with a synthesized weight.
- Readouts longer than about 35 characters drop the uppercase (`.readout.long`, added by the `long_readouts()` build step), because long runs of capitals are hard to read.
- Canvas labels use weight 400 and the semi-condensed width.

**Narrow screens and zoom**
- Below 400px, display and page titles shrink and narrow to width 112, so no word ever has to break.
- "Explore →" links and price amounts may now wrap. With text at 200%, they had pushed four pages 49–71px past the edge.

**Loading**
- Metric-matched fallback faces (Arial with `size-adjust` and ascent/descent overrides) keep line breaks stable while web fonts load.
- The Google Fonts request asks only for the axes in use: Archivo wdth 100–125 and wght 400–700; Martian wdth 87.5–100 and wght 400–500.

## Verification

- **Detector:** 131 findings went down to 26. All 26 are deliberate:
  - **17 numbered section labels:** the brand book's §01–§17 numbering is how the book is navigated.
  - **Misuse specimens:**
    - Georgia appears in the "don't" example on purpose;
    - the email signature uses Courier/Arial at 11–13px because email clients need web-safe fonts and pixel sizes.
  - **Two tight-leading flags:** a heading at 1.05–1.15 and the 36px statement at 1.2.
  - **One flat-hierarchy flag:** the detector counts the brand book's item-level `h3.as-h4` headings as h3.
  - **51-character caps flag:** a two-line cover readout of about 17 and 33 characters.
  - **Arial:** it is the metric-matched fallback, not the primary face.
- **axe-core 4.10** (WCAG 2.2 AA plus best-practice): 0 violations on 17 pages.
- **Text zoom** (root at 32px): body text scales with it (34px) and no page overflows at 1280px.
- **320px:** no overflow, and no visible heading is wider than its box.
- **Measure:** prose runs 69–73 characters per line on every page that has long paragraphs.
- **Mono weights:** only 400 and 500 render anywhere.
- **Visual check:** home, services, the Cytoscape case study, the overview and brand book §11 at 1440px and 390px.

## Notes

- Captures used the fallback faces, because Google Fonts can't be reached from the build environment. Check the real Archivo widths once on the live Pages URL.
- Self-hosting a subset WOFF2 and preloading it is part of `/impeccable optimize`.
