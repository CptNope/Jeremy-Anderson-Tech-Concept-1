# Finish review record

Impeccable's finish review, run by a separate reviewer with no access to the build conversation. It judged the build against the direction contract (`.impeccable/surfaces/site-index-html.md`), `PRODUCT.md` and the craft floor, using the captures in `.impeccable/review/`.

## Round 1 · disposition: fix

**Fidelity to the contract**

| Element | Verdict |
|---|---|
| Type: Archivo expanded display, Martian Mono for data | Match |
| Material: single-stroke vector traces, hairline bezels | Match |
| Ground: graticule black (#0A0C0F / #07090B) | Match |
| Hero headline, offer, CTAs, noise-to-lock trace, channel strip | Match |
| Story order: problem, channels, work, prices, process, founder, call | Match |
| Kicker readout above the H1 | Adapted. The craft floor bans kickers, so the state readout sits top right instead |
| Graticule tick rulers | Missing |
| Inner page heads | Contradicted. Each repeated the large JAT mark, and on some pages it collided with the H1 |
| Popularity badges | Flagged. Needed a source |
| "Reply within one business day" | Flagged. Not in PRODUCT.md |

**Material fixes requested**

1. Replace the repeated mark on inner page heads, and keep the trace clear of the headline.
2. Remove or source the "Most popular" and "Most requested" badges.
3. Remove or confirm the response-time commitment.
4. Move kicker-shaped labels (channel IDs, step timings, tags) off the tops of headings.
5. Case-study paragraphs collapsed into one block (a build bug).
6. Schematic labels were clipped on desktop and unreadable on phones.
7. Mobile wrapping: badge text, and the arrow on "Explore" links.
8. Add the graticule tick rulers promised in OWN-WORLD.

**Ceiling notes, not fixed in v1:** the monogram could draw with phosphor persistence; case studies could carry more measurement readouts; the home sections share one head layout and could vary their rhythm.

## Fixes applied

1. Inner page heads draw a waveform for their channel instead of the mark: step for CH1, burst for CH2, pulse for CH3 and sine for CH4. The headline is capped at 58% width.
2. Badges now match the labels on the current live pricing page: "Most requested" on Custom Platform, "Most popular" on Custom AI Tool and Hardening. The unsourced Guardian badge was removed.
3. The response-time claim was removed everywhere. The page now states the proposal-within-3-business-days commitment instead.
4. Channel IDs sit inline with the channel names. Step timings sit below each step heading. Channel tags sit below the project headings.
5. Every paragraph is its own `<p>`.
6. Feature plates use `meet` scaling. In-SVG labels hide under 700px. Architecture diagrams scroll sideways on narrow screens.
7. Badges and link arrows no longer wrap, and the price column stacks on phones.
8. Centre axes with minor and major ticks are drawn on the hero and page-head canvases.

## Round 2 verdict pass

The reviewer scored 6 fixes resolved and 2 partial (fixes 4 and 6), and found two regressions: the desktop architecture diagram was clipped by its new minimum width, and the axes crossed body copy on phones.

A final batch then:

- put the channel ID inline inside the h3 on narrow screens;
- limited the diagram's minimum width to screens 760px and narrower, with a "Swipe to see the full diagram" hint;
- turned off the full-screen axes below 760px.

After this batch the Impeccable detector reported only the `cramped-padding` false positives on full-bleed sections, which the reviewer confirmed are not real issues. Two review rounds is the budget for an unattended run, so this final batch has not been re-scored.

## Process notes

- The direction roll (`concept-seed`, key `8ec31921`) ran in degraded mode because the catalog service was unreachable from the build environment, so no catalog challengers were dealt. Jeremy chose **Calibrated Signal** from the grounded candidates.
- The build path was code-first, so no image comps were made and every visual is authored SVG or canvas.
- The harness had no shipped Impeccable reviewer agent, so the review ran as a fresh general-purpose subagent following `reference/degraded/finish-reviewer.md`.
