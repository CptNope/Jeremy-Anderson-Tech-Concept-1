# Performance record (`/impeccable optimize`, Oct 9 2026)

**Scope:** the overview, the site prototype and the brand book.

**Test conditions:**
- Headless Chromium as a 390px phone, on Slow 4G (150ms RTT, 1.6 Mbps down) with the CPU slowed 4×.
- Served over HTTP/2 with gzip, as GitHub Pages serves.
- Each figure is the median of 3 runs.

**How "before" was measured:** Google Fonts can't be reached from the build environment, so the before state replays its chain. Each Google request is intercepted and charged what it costs on that network: a new connection (3 RTT) plus a request for the render-blocking stylesheet, then RTT plus transfer time for each font. The font bytes are the same files that are now self-hosted, so only the delivery path differs.

## Results

| Page | FCP | LCP | Real fonts in | Blocking time | CLS | Transfer |
|---|---|---|---|---|---|---|
| Overview | 1036 → **796** | 1036 → **796** | 1333 → **901** | 130 → **61** | 0 → 0 | 274 → **154 KB** |
| Site home | 1152 → **892** | 1152 → **892** | 1565 → **1003** | 248 → **165** | 0 → 0 | 104 → 104 KB |
| Services | 1016 → **788** | 1016 → **788** | 1389 → **863** | 109 → **80** | 0 → 0 | 99 → 99 KB |
| Case study | 1004 → **784** | 1004 → **784** | 1426 → **844** | 107 → **54** | 0.015 → 0.015 | 98 → 98 KB |
| Brand book | 244 → 704 | 1132 → **704** | 1767 → **1232** | 611 → 674 | 0.034 → **0** | 143 → 142 KB |

Times are in ms; transfer is per page. On the site, FCP and LCP improved by 20–25% and the real fonts arrive 33–41% sooner. The brand book's old 244ms FCP was the concept bar painting unstyled before the book's CSS had been parsed, which is where its layout shift came from. Its first paint is now the finished page, and LCP is 38% sooner.

## What changed

**Fonts (the main bottleneck)**
- The pages no longer request anything from Google:
  - Before, the Google stylesheet blocked rendering, and getting it cost a new connection to `fonts.googleapis.com` before the font files on `fonts.gstatic.com` could even be requested.
  - Fonts now come from the same origin, so there are no third-party requests and no third-party CSS that blocks rendering. Visitors' IP addresses are no longer sent to Google, which also helps with privacy.
- `design/tools/subset_fonts.py` cuts each variable font down to the width and weight ranges in use, keeps a Latin subset (Basic Latin, Latin-1 and the punctuation, arrows and math signs the pages use), and writes WOFF2:
  - Archivo, width 100–125 and weight 400–700 (site and overview): **50 KB**
  - Archivo, width 62–125 and weight 400–700 (brand book, whose specimen slider runs to 62): **74 KB**
  - Martian Mono, width 87.5–100 and weight 400–500: **20 KB**
- Every page preloads its two files and uses `font-display: swap`.
- The OFL texts ship in `/fonts/`. Neither font declares a Reserved Font Name, so the subset copies may keep their names.
- The file names match the `theme.json` `fontFace` entries in brand book §17, so the WordPress theme can use them as they are.

**Fallback metrics**
- The fallback sizes were remeasured against the real fonts in the DOM:
  - Archivo against Arial: 0.985 across 27,800 characters of site copy, so the fallback is set to 98.4%.
  - Martian Mono at width 90 is 0.66em; Menlo and DejaVu are 0.602em, so the mono fallback is set to 109.6%.
- Ascent and descent overrides were recomputed to match.
- Consolas was dropped from the mono fallback because it is 9% narrower than every other face in that stack.

**Brand book assembly (a build defect)**
- The book's `<title>`, meta description, font preloads and every style block were landing in `<body>`. `build_all.py` now moves them into `<head>`, in the original cascade order. That order matters: it decides which `scroll-padding` wins, and the section links land exactly where they did before.

**Script work on the main thread**
- `markPoints()` computes the mark's 161 trace points with arithmetic. It used to insert an SVG and call `getPointAtLength` 161 times, forcing a full layout on every call: 65–71ms of main-thread time under 4× CPU. The new points match the browser's to within 0.002 units on a 108-unit mark.
- Design-token reads are cached. Each `getComputedStyle` read used to force a style recalculation: 77ms in the brand book.
- The comparison ("versus") canvases paint only when scrolled into view and skip resize work until then. They used to cost 61ms during load on the site home.

**Images**
- The overview previews ship as AVIF and WebP inside `<picture>`, with JPEG as the fallback. At 1200 wide: 28 KB AVIF against 89 KB JPEG; at 600 wide: 12 KB against 29 KB. There is no visible difference, including the faint graticule lines.
- `og:image` is now an absolute URL, which link previews require.

## Considered and rejected

- **`content-visibility: auto` on brand book sections.** It cut the book's blocking time to 0. But deep links and the Contents picker then landed thousands of pixels away from their target, because the jump positions came from placeholder heights. Correct navigation wins. The book's remaining ~650ms of blocking time, under 4× CPU, is the style and layout of one very long reference page.
- **An external SVG sprite.** The inline sprite costs 6.6 KB gzipped per page and draws the header logo on the first paint. An external file would only save that on repeat views, and it would add a request and a moment where the logo is missing.

## Verification

- **axe-core 4.10:** 0 violations on 17 pages.
- **Layout:** no horizontal overflow at 320px or with text at 200%.
- **Mono weights:** Martian Mono renders only at 400 and 500.
- **Navigation:** deep links, rail clicks and the phone Contents picker all land at the same offset as before (152px), at desktop and phone widths.
- **Visual check:** home, services, the Cytoscape case study, the overview and brand book §11 at 1440px and 390px, now rendered in the real fonts. This was the first time the real Archivo widths could be checked; earlier captures used the fallbacks. The specimen slider reaches width 62.
- **Measure:** with the real fonts, long paragraphs estimate at 70–76 characters per line. The 76 is on the Platforms and Intelligence pages, just past the ~75 ceiling; worth checking in the polish pass.

## Limits

- GitHub Pages sets a 10-minute cache on everything, which can't be changed. On Cloudways, serve `/fonts/` and `/img/` with `Cache-Control: public, max-age=31536000, immutable` and versioned file names.
- The case study keeps a 0.015 CLS: its intro paragraph sits 2px from a line break, and the real Archivo is 0.75% wider than the fallback on that sentence. That is far inside the 0.1 "good" threshold.
- All numbers are lab measurements. Confirm on a mid-range Android phone over cellular once Pages is live.
