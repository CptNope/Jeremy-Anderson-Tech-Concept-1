# JAT Signal

The Calibrated Signal identity for Jeremy Anderson Tech, built as a full-site-editing block theme.

It works in two situations:
- **On an existing site:** it's a drop-in replacement for the **Developer Portfolio** theme. Switch themes and the site renders in the new design with the same pages, projects, services and posts.
- **On a brand-new WordPress install:** it sets up the complete site, including pages, services and prices, legal pages and the concept layouts, the moment it's activated.

**Download:** [`jat-signal.zip` from the latest release](https://github.com/CptNope/Jeremy-Anderson-Tech-Concept-1/releases/latest). Upload the zip as it is. Don't unzip it first.

| | |
|---|---|
| Version | 1.0.0 ([changelog](CHANGELOG.md)) |
| Requires | WordPress 6.6+ (tested on 7.1.3), PHP 8.0+ |
| License | GPL v2 or later; fonts under the SIL Open Font License 1.1 |

---

## Install on an existing site (jeremyanderson.tech)

1. **Back up** the database and `wp-content`. Most hosts offer one-click backups.
2. Go to **Appearance › Themes › Add New › Upload Theme**, upload `jat-signal.zip`, then select **Activate**.
3. Open the site. Every page now uses the new design with its current content. Activation doesn't rewrite or delete anything.
4. Go to **Appearance › JAT Signal**:
   - **Concept layouts.** Select **Apply concept** for Home, Services, About and Projects. Each page is rebuilt from the concept using your real projects, services and prices. The previous content is stored, and **Restore previous** puts it back. The change is also saved in the page's Revisions.
   - **Concept pages.** Activation created Approach, Contact, and the four channel pages (Platforms, Intelligence, Security, Operations, under Services) as **drafts**. Review each one and publish it. Published pages join the header and footer automatically.
   - **Contact details.** Set the public email and the address that receives the contact form.
5. **Email delivery.** The contact form sends with `wp_mail()` and saves a private copy of every message under **Inquiries**, so nothing is lost if mail fails. On shared hosting, an SMTP plugin such as WP Mail SMTP makes delivery reliable.
6. Clear any page cache or CDN cache.

### What carries over unchanged

| Developer Portfolio | JAT Signal |
|---|---|
| 22 `developer-portfolio/*` blocks | Same names, attributes and defaults, so saved pages render without block errors |
| `project` and `service` post types (`/project/`, `/service/`) | Registered identically, with the same editor templates |
| `project_tag`, `project_category`, `service_category` | Same slugs and URLs |
| Service, project and post meta (price, timeline, features, links, subtitle, `tech_stack_data`) | Same keys, with the same sidebars in the editor |
| Page templates `page-projects`, `page-services`, `page-blog`, `page-about`, `blank` | Same slugs, so pages keep their assigned template |
| Palette, gradient, font, size, spacing and shadow presets | Same slugs, remapped to the new tokens, so blocks styled with them restyle instead of breaking |
| Patterns `developer-portfolio/*`, the `[faq]` shortcode, the `{year}` token | Still available |

The old `// section` eyebrow labels and emoji are kept in the saved content but aren't displayed. In the editor they appear under **Kept from Developer Portfolio**.

---

## Install on a new WordPress site

Upload and activate the zip as above. The theme treats a site as new when it has no static front page, no published pages other than WordPress's "Sample Page", and no projects or services. On a new site, activation sets up:

- **Services and prices:** the six service categories and all 27 services, with prices, timelines, tiers, features and add-ons, as published on jeremyanderson.tech.
- **Pages:** Home (set as the front page), Services, Projects, About, and Blog (set as the posts page), with the concept layouts applied. **Restore previous** returns any of them to the Developer Portfolio layout.
- **Concept pages:** Approach, Contact and the four channel pages, published.
- **Legal pages:** Privacy Policy (WordPress's draft, filled in and published), Terms of Service and Accessibility.
- **Tech stack:** the technology list shown on Home and About.
- **Clean-up:** WordPress's untouched "Sample Page" is moved to the trash.

This runs once. It never runs on a site with existing content, and switching themes back and forth doesn't repeat it.

**Still yours to add on a new site:** projects (the Projects page says "No projects yet" until the first one is published), blog posts, a portrait for About and the founder section (the JAT mark stands in until then), and the site title under **Settings › General**, which appears in the footer.

---

## What the theme includes

- **Templates** for every view: front page, pages, posts, the blog, projects and services (single and archive), taxonomies, search and 404. The header and footer are template parts and can be edited in **Appearance › Editor**.
- **14 `jat/*` blocks** for the concept layouts: page head, channels, versus, price list, steps, founder, check grid, channel intro, contact form, site footer, archive list, project facts, service summary and post navigation.
- **Contact form:**
  - client- and server-side validation, a honeypot, a minimum fill time and a rate limit;
  - a draft that survives a refresh;
  - every message emailed and also stored under **Inquiries**;
  - the option to show a Contact Form 7 form in the same frame instead, if that plugin is active.
- **SEO.** Meta description, Open Graph and structured data (person, website, breadcrumbs, projects, services and FAQs). If All in One SEO, Yoast, Rank Math, SEOPress or The SEO Framework is active, the theme leaves meta tags and the main schema to that plugin and adds only the project, service, list and FAQ data.
- **Fonts.** Archivo and Martian Mono are self-hosted variable fonts; no Google Fonts requests are made.
- **Editor.** Every block shows a live preview and a settings sidebar:
  - lists are edited as repeaters;
  - projects and posts are chosen by title;
  - the tech stack is edited in place on the Tech Stack block.

## Header navigation

The header defaults to **Automatic**, which links Services, Projects, Approach, About and the blog page once they're published. To use **Appearance › Menus › Primary** or custom links instead, change **Navigation** in the header block's settings (**Appearance › Editor › Patterns › Header**).

## Before switching back to Developer Portfolio

Customizations made in the Site Editor are stored per theme, so any header or template edits made under Developer Portfolio won't appear here. The reverse is also true.

The concept layouts use `jat/*` blocks, which Developer Portfolio doesn't have. Before switching back, select **Restore previous** for each page and unpublish the concept pages. Pages left in the restyled state work in both themes.

---

## How it was tested

Version 1.0.0 was tested on WordPress 7.1.3 with PHP 8.3, in two setups:

1. **A copy of jeremyanderson.tech** running Developer Portfolio, with:
   - the same pages and the same 13 projects;
   - the same 27 services and 21 posts;
   - the same customized hero, about and tech stack.
2. **A brand-new install** with nothing but WordPress's defaults.

Checks run in both setups:

| Area | Result |
|---|---|
| Pages | Every template (home, pages, posts, projects, services, archives, search, 404) at 1440px and 390px: no PHP notices, console errors or horizontal scroll |
| Accessibility | axe-core (WCAG 2 A/AA and best practice): no violations |
| Block editor and Site Editor | Load every block with its preview and settings |
| Concept layouts | Apply and restore round-trip |
| Contact form | Validation, draft restore, sending and the stored inquiry |
| Mobile menu | Focus trap and Escape |
| Theme switching | Switching back to Developer Portfolio and again to JAT Signal creates no duplicate pages |

## Files

```
jat-signal/
├── style.css · theme.json · functions.php · screenshot.png
├── inc/
│   ├── helpers.php        Markup helpers: icons, channels, cards, rows, page head
│   ├── setup.php          Theme supports, assets, fonts, sprite, editor styles
│   ├── content-model.php  Projects, Services, taxonomies and meta (same keys as Developer Portfolio)
│   ├── blocks.php         Registers every block in /blocks and the shared editor script
│   ├── compat.php         Behavior carried over from Developer Portfolio
│   ├── seo.php            Meta tags and structured data (yields to SEO plugins)
│   ├── forms.php          Contact form handler and the Inquiries list
│   ├── concept.php        Concept layouts, concept pages, Appearance › JAT Signal
│   ├── starter.php        New installs only: services, pages, legal pages
│   └── starter/*.json     Starter data: services and prices, legal pages, tech stack
├── blocks/                36 server-rendered blocks (block.json + render.php)
├── templates/ · parts/    Block templates and the header and footer
├── patterns/              Developer Portfolio patterns (same slugs) and concept sections
└── assets/
    ├── css/               theme.css (front end and editor canvas), editor.css, editor-ui.css
    ├── js/                theme.js (front end), editor.js (block settings), meta sidebars
    ├── fonts/             Archivo and Martian Mono variable WOFF2 and their licenses
    └── images/            Brand sprite and SVG logos
```

There's no build step. Edit the files and reload.

## Releasing a new version

Releases are published automatically by the repo's GitHub Actions workflow (`.github/workflows/release-jat-signal.yml`).

1. Bump `Version:` in `style.css` and `Stable tag:` in `readme.txt`.
2. Add an entry to `CHANGELOG.md`.
3. Optionally, write release notes in `wordpress/release-notes/jat-signal-<version>.md`. Without that file, the release uses the changelog entry.
4. Push to `main`.

The workflow zips the `jat-signal` folder, with the folder at the root of the zip as WordPress expects. It tags the commit `jat-signal-v<version>` and publishes the release with `jat-signal.zip` attached. Pushes that don't change the version publish nothing. The workflow can also be run by hand from the **Actions** tab.

## Credits

Archivo and Martian Mono are licensed under the SIL Open Font License; see `assets/fonts/OFL-*.txt`. The theme is licensed under GPL v2 or later.
