"""Build the concept overview page (repository root index.html).

The overview introduces Concept 1, links the two deliverables (brand book, site prototype),
lists every site page and the design files, and states the ground rules.
It reuses the site's stylesheet and trace engine from /site/.
"""
import sys
from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]
sys.path.insert(0, str(ROOT/'design/site-src/build'))
from common import SPRITE, FONTS, ARROW, EXT, REPO, concept_bar, tag, long_readouts  # noqa: E402

BLOB = REPO + '/blob/main/'
TREE = REPO + '/tree/main/'

PAGES = [
  ('Start', [('index.html','Home','The thesis, four channels, open-source proof, prices, process, founder')]),
  ('Services', [('services.html','Services &amp; pricing','Every package, hourly rate, care plan and add-on, plus FAQ'),
                ('platforms.html','CH1 Platforms','Web design, WordPress engineering, custom software and PWAs'),
                ('intelligence.html','CH2 Intelligence','AI discovery, custom AI tools, private LLMs, automation'),
                ('security.html','CH3 Security','Audits, hardening, SIEM/SOC monitoring, privacy review'),
                ('operations.html','CH4 Operations','Managed hosting, care plans, performance, SEO/AEO/GEO')]),
  ('Work', [('work.html','Work','Open-source projects you can inspect'),
            ('work-gods-eye-lite.html',"God's Eye Lite",'Case study: live 3D world map PWA'),
            ('work-cytoscape.html','Cytoscape: Immune Defense','Case study: tested arcade PWA'),
            ('work-chronos-canon.html','Chronos &amp; Canon','Case study: research app with an evidence graph')]),
  ('Studio', [('about.html','About','Founder-led studio and timeline'),
              ('approach.html','Approach','Process, quality bar, handoff, terms'),
              ('insights.html','Insights','Field notes linking existing posts'),
              ('contact.html','Contact','Lead form with validation (not connected)'),
              ('sitemap.html','Site map','IA, redirect map, WordPress blocks')]),
]

FILES = [
  ('README.md', BLOB+'README.md', 'Everything about this concept: idea, map, build, deploy, WordPress theme'),
  ('PRODUCT.md', BLOB+'PRODUCT.md', 'Product truth: users, positioning, prices, evidence'),
  ('DESIGN.md', BLOB+'DESIGN.md', 'Design system record with machine-readable tokens'),
  ('.impeccable/surfaces/site-index-html.md', BLOB+'.impeccable/surfaces/site-index-html.md', 'Direction contract for Calibrated Signal'),
  ('.impeccable/design.json', BLOB+'.impeccable/design.json', 'Tonal ramps, motion, breakpoints, component snippets'),
  ('brand-book/assets', TREE+'brand-book/assets', 'Production SVG logos: mark, lockups, icon'),
  ('wordpress/jat-signal', TREE+'wordpress/jat-signal', 'JAT Signal WordPress block theme: source, guide and changelog'),
  ('Releases', REPO+'/releases/latest', 'Installable theme: jat-signal.zip'),
  ('design/identity', TREE+'design/identity', 'Wordmark outliner, logo and sprite generators, explorations'),
  ('design/brand-book-src', TREE+'design/brand-book-src', 'Brand book source'),
  ('design/site-src', TREE+'design/site-src', 'Site styles, trace engine and Python page builder'),
  ('design/tools', TREE+'design/tools', 'Build, type-role, font-subset and capture scripts'),
  ('fonts', TREE+'fonts', 'Self-hosted Archivo and Martian Mono subsets with their licenses'),
  ('design/review', TREE+'design/review', 'Review records: finish review, hardening, adaptation, typesetting, performance, polish'),
]

def pages_html():
    out=[]
    for group, items in PAGES:
        lis=''.join(f'<li><a href="site/{h}"><span class="pn">{t}</span><span class="pd">{d}</span></a></li>' for h,t,d in items)
        out.append(f'<div class="pg-group"><h3 class="readout">{group}</h3><ul>{lis}</ul></div>')
    return ''.join(out)

def files_html():
    return '<ul class="files">'+''.join(f'<li><a href="{u}" rel="noopener"><span class="mono">{n}</span>{EXT}</a><span class="muted">{d}</span></li>' for n,u,d in FILES)+'</ul>'

CSS = '''<style>
.ov-hero .wrap{min-height:clamp(520px,74svh,760px)}
.ov-hero h1{font-size:var(--t-display);font-weight:640;font-stretch:125%;line-height:.92;letter-spacing:-.035em}
.ov-hero h1 span{display:block;color:var(--ch1)}
.byline{margin-top:var(--s5)}
@media (max-width:879px),(max-height:540px) and (orientation:landscape){.ov-lockup{display:none!important}}
.deliv{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:var(--s5)}
@media (max-width:860px){.deliv{grid-template-columns:1fr}}
.deliv a{display:grid;grid-template-rows:auto 1fr;border:1px solid var(--line);border-radius:var(--r-panel);overflow:hidden;background:var(--panel);text-decoration:none;color:var(--fg);transition:border-color var(--t-base) var(--ease-out)}
@media (hover:hover){.deliv a:hover{border-color:var(--ch1)}}
.deliv picture{display:block}
.deliv img{display:block;width:100%;height:auto;aspect-ratio:16/10;object-fit:cover;object-position:top;border-bottom:1px solid var(--line);background:var(--ink-950)}
.deliv .body{padding:var(--s5) var(--s5) var(--s6);display:flex;flex-direction:column;gap:var(--s3)}
.card-foot{margin-top:auto;padding-top:var(--s2);display:flex;justify-content:space-between;align-items:center;gap:var(--s4);flex-wrap:wrap}
.deliv h3{font-size:var(--t-title);font-weight:600;font-stretch:112%;line-height:1.15;letter-spacing:-.01em}
.deliv p{margin:0;color:var(--fg-muted)}
.deliv .more{display:inline-flex;align-items:center;gap:8px;color:var(--ch1);font-weight:600}
.pages{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:var(--s6) var(--s5)}
@media (max-width:1000px){.pages{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:560px){.pages{grid-template-columns:1fr}}
.pg-group h3{margin-bottom:var(--s3);color:var(--fg-subtle)}
.pg-group ul{list-style:none;margin:0;padding:0;border-top:1px solid var(--line)}
.pg-group a{display:grid;gap:2px;padding:12px 0;border-bottom:1px solid var(--line);text-decoration:none;color:var(--fg);min-height:44px}
@media (hover:hover){.pg-group a:hover .pn{color:var(--ch1)}}
.pn{font-family:var(--f-display);font-stretch:110%;font-weight:600;transition:color var(--t-base) var(--ease-out)}
.pd{color:var(--fg-muted);font-size:var(--t-small)}
.files{list-style:none;margin:0;padding:0;border-top:1px solid var(--line)}
.files li{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.3fr);gap:var(--s5);padding:12px 0;border-bottom:1px solid var(--line);align-items:baseline}
.files a{display:inline-flex;align-items:center;gap:8px;min-height:36px;text-decoration:none}
@media (pointer:coarse){.files a{min-height:44px}.repo-link{display:inline-flex;align-items:center;min-height:44px}}
.files a svg{width:14px;height:14px;flex:none}
@media (hover:hover){.files a:hover .mono{text-decoration:underline}}
.files .mono{font-size:var(--t-small)}
.files .muted{font-size:var(--t-small)}
@media (max-width:640px){.files li{grid-template-columns:1fr;gap:2px}}
.rules{list-style:none;margin:0;padding:0;display:grid;gap:12px}
.rules li{display:grid;grid-template-columns:18px 1fr;gap:10px;color:var(--fg-muted);font-size:var(--t-body)}
.rules li strong{color:var(--fg)}
.rules svg{width:16px;height:16px;margin-top:4px;color:var(--ch4)}
</style>'''

CHECK = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8.5 6.5 12 13 4.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'

body = f'''{SPRITE}
<a class="skip" href="#main">Skip to content</a>
{concept_bar('', 'overview')}
<main id="main">
<section class="hero ov-hero" aria-labelledby="ov-h">
  <div class="graticule" aria-hidden="true"></div>
  <canvas id="heroTrace" aria-hidden="true"></canvas>
  <div class="hero-state readout" data-state="run" aria-hidden="true"><span><span class="dot"></span><span class="lbl">Acquiring signal</span></span></div>
  <div class="wrap">
    <div class="hero-copy">
      <svg class="ov-lockup" viewBox="1 6 468 68" width="260" height="38" role="img" aria-label="Jeremy Anderson Tech" style="display:block;margin-bottom:var(--s6);color:var(--fg)"><use href="#lk-h"/></svg>
      <h1 id="ov-h">Concept 1 <span>Calibrated Signal</span></h1>
      <p class="lede">A brand identity, a 17-section brand book and a 15-page website prototype for Jeremy Anderson Tech. Direction 01: Future Systems. The identity is a modern oscilloscope: a noisy signal that locks steady into the JAT mark.</p>
      <div class="cta-row"><a class="btn btn-primary" href="brand-book/">Open the Brand Book{ARROW}</a><a class="btn btn-secondary" href="site/">Open the Site Prototype</a></div>
      <p class="readout byline">By Jeremy Anderson · jeremyanderson.tech · October 2026</p>
    </div>
  </div>
</section>

<section class="section" aria-labelledby="dl-h"><div class="wrap">
  <div class="section-head"><h2 id="dl-h">Two deliverables, one system.</h2><p class="lede">The brand book defines the identity and its production tokens. The site prototype applies them to every page the agency needs.</p></div>
  <div class="deliv">
    <a href="brand-book/"><picture><source type="image/avif" srcset="img/preview-brand-book-600.avif 600w, img/preview-brand-book.avif 1200w" sizes="(max-width:860px) calc(100vw - 32px), 50vw"><source type="image/webp" srcset="img/preview-brand-book-600.webp 600w, img/preview-brand-book.webp 1200w" sizes="(max-width:860px) calc(100vw - 32px), 50vw"><img src="img/preview-brand-book.jpg" srcset="img/preview-brand-book-600.jpg 600w, img/preview-brand-book.jpg 1200w" sizes="(max-width:860px) calc(100vw - 32px), 50vw" width="1200" height="750" alt="Brand book cover: the JAT trace mark on a graticule screen beside the title Brand Book" loading="lazy" decoding="async"></picture><div class="body"><h3>Brand Book</h3><p>Positioning, audiences, architecture, voice and tagline; the JAT logo system with construction and misuse; color, type, grid and motion; live UI components; applications from business cards to merch; tokens for CSS, theme.json and JSON.</p><div class="card-foot"><span class="more">Read it{ARROW}</span><span class="readout">17 sections</span></div></div></a>
    <a href="site/"><picture><source type="image/avif" srcset="img/preview-site-600.avif 600w, img/preview-site.avif 1200w" sizes="(max-width:860px) calc(100vw - 32px), 50vw"><source type="image/webp" srcset="img/preview-site-600.webp 600w, img/preview-site.webp 1200w" sizes="(max-width:860px) calc(100vw - 32px), 50vw"><img src="img/preview-site.jpg" srcset="img/preview-site-600.jpg 600w, img/preview-site.jpg 1200w" sizes="(max-width:860px) calc(100vw - 32px), 50vw" width="1200" height="750" alt="Website hero: Systems that hold steady, with the JAT trace locking on a graticule and four service channels below" loading="lazy" decoding="async"></picture><div class="body"><h3>Site Prototype</h3><p>Home, services and full pricing, four channel pages, work and three open-source case studies, about, approach, insights, contact and a site map with the WordPress block plan. Static HTML that maps onto Gutenberg blocks.</p><div class="card-foot"><span class="more">Explore it{ARROW}</span><span class="readout">15 pages</span></div></div></a>
  </div>
</div></section>

<section class="section alt" aria-labelledby="pg-h"><div class="wrap">
  <div class="section-head"><h2 id="pg-h">Every page in the prototype.</h2><p class="lede">Each page links to its planned production URL through its canonical tag; the site map page lists the redirect plan.</p></div>
  <div class="pages">{pages_html()}</div>
</div></section>

<section class="section" id="design-files" aria-labelledby="df-h" style="scroll-margin-top:24px"><div class="wrap">
  <div class="section-head"><h2 id="df-h">Design files</h2><p class="lede">Source, records and tooling live in the repository. Running <code class="mono">python3 design/tools/build_all.py</code> rebuilds every page served here.</p></div>
  {files_html()}
</div></section>

<section class="section alt" aria-labelledby="ab-h"><div class="wrap two-col">
  <div><h2 id="ab-h">About this concept</h2><div class="tag-list" style="margin-top:var(--s5)">{tag('ch1')}{tag('ch2')}{tag('ch3')}{tag('ch4')}</div></div>
  <ul class="rules">
    <li>{CHECK}<span><strong>Real proof only.</strong> Case studies are Jeremy's own public projects. No testimonials, client logos, invented metrics or team members.</span></li>
    <li>{CHECK}<span><strong>Published facts.</strong> Prices, timelines, package labels and founder facts come from the current jeremyanderson.tech.</span></li>
    <li>{CHECK}<span><strong>Labeled placeholders.</strong> The founder portrait is marked "to be photographed"; project visuals are labeled schematics; the contact form validates but does not send.</span></li>
    <li>{CHECK}<span><strong>Out of search.</strong> Every page is <code class="mono">noindex</code> and <code class="mono">robots.txt</code> blocks crawlers until launch.</span></li>
    <li>{CHECK}<span><strong>Built to ship.</strong> The JAT Signal WordPress block theme carries this design onto the live site without losing content, and sets up a complete site on a new install. <a href="{REPO}/releases/latest" rel="noopener">Download the theme</a>.</span></li>
  </ul>
</div></section>
</main>
<footer class="site-footer"><div class="wrap"><div class="foot-base" style="margin-top:0;border-top:0;padding-top:0">
  <span class="readout long">© 2026 Jeremy Anderson Tech · Shrewsbury,&nbsp;MA · Concept&nbsp;1 for review</span>
  <span class="concept-note">Fonts: Archivo and Martian Mono, SIL Open Font License 1.1 · <a class="repo-link" href="{REPO}" rel="noopener">github.com/CptNope/Jeremy-Anderson-Tech-Concept-1</a></span>
</div></div></footer>
<script src="site/site.js" defer></script>'''

html = f'''<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>JAT Concept 1 · Calibrated Signal</title>
<meta name="description" content="Jeremy Anderson Tech Concept 1: brand book and 15-page website prototype, Direction 01 Future Systems, Calibrated Signal.">
<meta name="robots" content="noindex">
<meta property="og:title" content="JAT Concept 1 · Calibrated Signal">
<meta property="og:description" content="Brand book and website prototype for Jeremy Anderson Tech.">
<meta property="og:image" content="https://cptnope.github.io/Jeremy-Anderson-Tech-Concept-1/img/preview-site.jpg">
<meta name="theme-color" content="#0A0C0F">
<link rel="icon" href="brand-book/assets/jat-mark.svg" type="image/svg+xml">
<script>document.documentElement.classList.add('js')</script>
{FONTS.replace('../fonts/', 'fonts/')}
<link rel="stylesheet" href="site/site.css">
{CSS}
</head>
<body>
{body}
</body>
</html>
'''
(ROOT/'index.html').write_text(long_readouts(html))
print('> index.html (overview)')
