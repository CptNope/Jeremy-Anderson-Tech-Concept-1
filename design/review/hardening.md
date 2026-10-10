# Hardening record (`/impeccable harden`, Oct 9 2026)

Follows the audit (15/20). Scope: the overview, all 15 site prototype pages and the brand book.

## What changed

**Navigation (site)**
- The mobile menu's `aria-controls` now points at a real element (`#primary-menu`).
- Opening the menu moves focus to the first link. Tab and Shift+Tab stay inside the open menu.
- Escape closes the menu and returns focus to the button. Clicking a link closes it.
- With JavaScript off, the menu button is hidden and the links show as a wrapping row, so phones still get navigation.

**Contact form**
- Each error message is linked to its field through `aria-describedby`, so screen readers read it when the field gets focus.
- Errors clear as soon as the input is fixed.
- A failed submit shows an error summary with links that jump to each field that needs attention.
- Limits: name 100 characters, email 254, organization 120, website 200, message 20–4,000. The message has a live counter.
- The website field is validated when filled in.
- Names with accents, CJK characters and emoji are accepted.
- Double submits are ignored while a send is in progress: the button is disabled and marked `aria-busy`.
- If the browser reports no connection, the form says so and keeps the draft.
- The draft is saved to `sessionStorage`, so a refresh or a failed send never loses it.
- A hidden honeypot field silently blocks bot submissions.
- The form posts with `method="post"`, so nothing leaks into the URL if scripts fail.
- A `<noscript>` notice gives the email address instead.
- Status messages are built with text nodes, never `innerHTML`.

**Resilience**
- Every site.js feature runs inside its own guard, so a failure in one never disables navigation or the form.
- Canvases check for `getContext` first.
- Pages still work in browsers without `IntersectionObserver` or `matchMedia().addEventListener`. The traces draw immediately instead of waiting to scroll into view.
- The brand book script has the same guards.
- With JavaScript off, the brand book shows its full contents list on phones and all three token panels.

**Long text and 200% text**
- Grid and flex children get `min-width: 0`, and two-column form rows use `minmax(0,1fr)`.
- Long words, mono strings (the email address, file paths) and display headings now wrap.
- Buttons wrap on narrow screens.
- Verified with German compound words and with text at 200% on a 320 px screen: no horizontal overflow on any page.

**Structure**
- The brand book has a skip link that is first in the tab order.
- 17 headings that skipped from H2 to H4 are now H3. They keep their visual size through the `.as-h4` class.
- Nested `<aside>` cards became labeled sections.
- Horizontally scrolling diagrams and tables can be focused and have labels.
- The concept bar uses `aria-current="true"` inside the site section and `"page"` only on its home.

## Verification

- **axe-core 4.10** (WCAG 2.0/2.1/2.2 A and AA, plus best-practice): 0 violations on 17 pages at 1440 and 390 px.
- **Keyboard:** the mobile menu opens with focus on its first link, keeps focus inside, closes on Escape and returns focus to the button.
- **Form scenarios:**
  - an empty submit shows a summary;
  - errors clear while typing;
  - input over the limit is capped;
  - a reload restores the draft, including the checked channels;
  - the honeypot blocks;
  - offline is handled.
- **No-JS:** the site and brand book stay navigable on phones.
- **Simulated old browser** (no IntersectionObserver, legacy matchMedia): no errors, the hero trace draws and the menu works.

## Left for other commands

- Type-scale consolidation and font-size drift against DESIGN.md: done in `/impeccable typeset` (`typeset.md`).
- 44 px targets for footer links and copy buttons, and the brand book picker: `/impeccable adapt`.
- Font subsetting and preload, and an external sprite: `/impeccable optimize`.
- DESIGN.md refresh: `/impeccable document`.
