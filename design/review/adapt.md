# Adaptation record (`/impeccable adapt`, Oct 9 2026)

**Targets:** phones in portrait and landscape, tablets in portrait and landscape, laptops, desktops and 2560px screens, with touch and mouse input. Each was measured in a headless browser, with touch emulated so `pointer: coarse` and `hover: none` apply.

## What the measurements showed

| Context | Problem |
|---|---|
| Every touch screen | 15–27 controls per page were under 44px: concept bar 40px, footer links 36px, copy buttons 32–36px, logo links 24–30px. |
| Brand book on phones | The Contents picker, the main navigation, was 16px tall. |
| Tablet portrait (768px) | The hero's JAT mark sat on top of the headline, because the stacked layout started only below 760px. |
| Tablet landscape (1024px) | The site showed a menu button although the full navigation fits. The hero mark crossed the lede. The brand book hid its section rail. |
| Phone landscape (844×390) | The hero mark sat on the headline, and the hero was forced to at least 560px on a 390px screen. The brand book had 99px of fixed chrome (25% of the screen). |
| Phones | Data tables scrolled sideways. |
| Touch screens | Hover states stuck after a tap. |

## What changed

**Breakpoints set by content**
- The full navigation needs 748px plus gutters, so the menu button now applies only below **880px** (it was 1080px).
- The brand book's section rail returns at **960px**.
- A **short-landscape** mode (540px tall or less) gives phones on their side a compact layout.

**Hero, three modes:** the CSS and the trace engine use the same media queries.
- **Narrow (under 880px):** the trace has its own band above the headline, sized from the band, and the copy uses the full width.
- **Wide:** the copy is held to at most 52vw and the mark to the right 40%, so they never touch.
- **Short landscape:** copy and mark sit side by side. The headline is sized from viewport height, and the header shrinks to 56px.

The brand book cover measures its real glyph boxes:
- the mark sits beside the title when there's room;
- in the band above it when there isn't;
- squeezed to the right edge on short screens.

A container query switches the cover's status strip to two columns when it is narrower than 720px.

**Touch**
- On `pointer: coarse`, every control reaches 44px: concept bar, footer links, copy buttons, tabs, the width slider, file links, site map links, breadcrumbs and the brand book rail.
- The brand book's Contents picker is now a full 44px select.
- The logo links are 44px for every input type.

**Hover and feedback**
- 26 hover rules now apply only under `@media (hover:hover)`, so taps no longer leave stuck hover states.
- Touch screens get `:active` feedback and a cyan-tinted tap highlight instead.

**Tables**
- Below 640px, data tables in the brand book and site map become label/value cards. A shared `stack_tables()` build step adds `data-label` and table roles to each table.
- The contrast matrix stays a scrollable grid on purpose.

**Safe areas:** gutters, the concept bar, the header menu, footers and the brand book all respect notch and home-indicator insets in landscape.

**Smaller fixes**
- Sticky cards turn static in single-column layouts.
- Breadcrumbs stay aligned when their links grow on touch.
- The overview preview images ship 600px versions with `srcset`.
- The brand book has a print / Save as PDF stylesheet: it keeps the instrument palette, drops the navigation and starts each section on a new page.
- The X footer link now reads "X (Twitter)".

## Verification

- **Device matrix:** 8 device classes × 9 pages.
  - No horizontal overflow anywhere.
  - **No interactive element under 44px on any touch device.**
  - Fixed chrome on phone landscape is 57px on the site and 67px on the brand book (was 69–99px).
- **axe-core 4.10** (WCAG 2.2 AA plus best-practice): 0 violations on 17 pages at desktop, phone and tablet-landscape touch.
- **Visual check:** layouts reviewed at 390, 768, 844×390, 1024, 1440 and 2560px.

## Notes

- All testing used emulation. Before launch, check on a real iPhone (Safari notch insets and the select picker) and a mid-range Android phone.
- The type-scale consolidation (`/impeccable typeset`) and font loading work (`/impeccable optimize`) from the audit are still open.
