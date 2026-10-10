# Polish record (`/impeccable polish`, Oct 9 2026)

**Scope:** the overview, all 15 site pages and the brand book. This was a refinement pass: the identity, copy, behavior and layouts stay as they were. There was no stored critique to work from, so this was an independent pass.

**Method:**
- An automated sweep of all 17 pages at 1440px and 390px for console errors, browser surfaces, labels set above headings, heading spacing and type-role drift.
- Full-page screenshots of every page at both widths, the first round taken in the real self-hosted fonts.
- One round of fixes, then one confirmation round.

## Found and fixed

| Where | Problem | Fix |
|---|---|---|
| Home and Insights, post cards | Category and date sat above each post title as a label, and "READ ON THE CURRENT SITE" was the only all-caps mono link on the site. | The title now comes first, then the summary. The metadata and a sentence-case "Read on the current site ↗" sit together at the bottom of the card and line up across the row. |
| Overview, deliverable cards | "17 sections" and "15 pages" sat above the card titles. | They moved into a footer row beside "Read it →" and "Explore it →". |
| Overview previews | The images were captured before the type and font work, so they showed the fallback faces. | Recaptured in the real fonts and re-encoded as AVIF, WebP and JPEG at 600 and 1200 wide. |
| Overview, Design files list | It didn't list `/fonts/`, the review records or the newer tools. | Lists `fonts`, the whole `design/review` folder and all four tool scripts. |
| Case studies, Architecture diagram | The 960-unit diagram sat in the 544px text column with an 820px minimum width, so on desktop it scrolled sideways and half of it was hidden. | It has its own full-width section below the case study: 1278px on desktop with no scrolling and every label legible. Phones keep the swipe area and hint. The screen-reader label no longer claims it scrolls. |
| Home hero note | It broke as "…Written proposal in 3 / business days". | `text-wrap: balance` plus non-breaking spaces in "3 business days" (on the contact page too). |
| Footer (site and overview) | On phones "MA" wrapped onto its own line. | Non-breaking spaces keep "Shrewsbury, MA" and "Concept 1" together. |

## Checked and left as is

- **Prose measure:** the Platforms and Intelligence intros set 72 characters per line. The 76 in `optimize.md` was a width-based estimate.
- **"Most requested" and "Most popular" badges:** they are copy from the current site, and changing them would change a claim.
- **The brand book's §01–§17 numbers:** this is how the book is navigated, cross-referenced ("see brand book §13") and printed.
- **Legend before "Contrast matrix" (brand book §10):** it belongs to the proportion bar above it; it is not a label on the heading.
- **Motion demo (brand book §13):** it draws once in view. It was blank in the capture only because it was mid-animation.
- **Browser surfaces:** text selection, the scrollbar, link underlines, tabular figures and the text cursor in form fields are all themed on every page. The one grey cursor reading came from the brand book's range slider, which has no cursor.

## Verification

- **axe-core 4.10** (WCAG 2.2 AA plus best-practice): 0 violations on 17 pages at 1440px and 390px.
- **Errors:** no console errors and no failed requests.
- **Layout:** no horizontal overflow at either width.
- **Build:** deterministic. Running `build_all.py` twice produces no diff.
