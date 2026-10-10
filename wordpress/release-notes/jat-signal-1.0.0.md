**JAT Signal 1.0.0** is the Calibrated Signal design for Jeremy Anderson Tech as a full-site-editing WordPress block theme.

### Install
1. Back up the site.
2. Go to **Appearance › Themes › Add New › Upload Theme**, upload **`jat-signal.zip`** (below) as it is, then select **Activate**.

### What activation does
| Site | Result |
|---|---|
| **An existing site running Developer Portfolio** (jeremyanderson.tech) | Every page, project, service and post is restyled as it is, and nothing is rewritten or deleted. Approach, Contact and the four channel pages are added as drafts. **Appearance › JAT Signal** applies the concept layout to Home, Services, About and Projects, with one-click restore. |
| **A brand-new WordPress install** | The complete site is set up: the 27 services and prices, Home, Services, Projects, About and Blog with the concept layouts, the concept pages, and the Privacy, Terms and Accessibility pages. |

### Highlights
- Keeps all 22 `developer-portfolio/*` blocks, the `project` and `service` post types, their taxonomies and meta, and the page templates, so live content and URLs carry over.
- Adds 14 `jat/*` blocks. All 36 blocks have live previews and settings in the editor.
- Contact form with validation, a honeypot and a rate limit. Messages are emailed with `wp_mail()` and stored under **Inquiries**. Contact Form 7 is optional.
- SEO meta and structured data, which step aside for All in One SEO, Yoast, Rank Math, SEOPress and The SEO Framework.
- Self-hosted Archivo and Martian Mono fonts.

### Requirements and testing
Requires WordPress 6.6+ and PHP 8.0+. Tested on WordPress 7.1.3 with PHP 8.3, both on a copy of jeremyanderson.tech and on a brand-new install:
- every template at desktop and phone widths;
- axe-core: no violations;
- no PHP notices or console errors;
- the block editor and Site Editor;
- concept apply and restore, and the contact form.

Full guide: [`wordpress/jat-signal/README.md`](https://github.com/CptNope/Jeremy-Anderson-Tech-Concept-1/blob/main/wordpress/jat-signal/README.md) · [Changelog](https://github.com/CptNope/Jeremy-Anderson-Tech-Concept-1/blob/main/wordpress/jat-signal/CHANGELOG.md)
