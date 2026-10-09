from pathlib import Path
ROOT = Path(__file__).resolve().parents[3]
import json, html, os
PAGES = os.environ.get('PAGES','1')=='1'  # 1 = GitHub Pages build (full documents, brand book link)
SPRITE = open(ROOT/'design/identity/sprite.html').read()
REPO = 'https://github.com/CptNope/Jeremy-Anderson-Tech-Concept-1'
CBAR_CSS = """<style>
.cbar{background:#07090B;border-bottom:1px solid #222A34;font-family:"Martian Mono",ui-monospace,Menlo,monospace;font-stretch:90%;font-size:12px;letter-spacing:.04em;position:relative;z-index:70}
.cbar-in{max-width:1376px;margin-inline:auto;padding-inline:clamp(16px,4vw,48px);display:flex;align-items:center;gap:20px;min-height:40px;overflow-x:auto;scrollbar-width:none}
.cbar-in::-webkit-scrollbar{display:none}
.cbar a{color:#A3AFBC;text-decoration:none;display:inline-flex;align-items:center;gap:8px;min-height:40px;white-space:nowrap}
.cbar a:hover{color:#E6ECF2}
.cbar a:focus-visible{outline:2px solid #3CE3F0;outline-offset:-2px;border-radius:2px}
.cbar .home{color:#E6ECF2;font-weight:600;margin-right:auto}
.cbar .home svg{width:22px;height:14px;flex:none}
.cbar ul{list-style:none;margin:0;padding:0;display:flex;gap:20px}
.cbar a[aria-current=page]{color:#E6ECF2;box-shadow:inset 0 -2px 0 #3CE3F0}
.cbar .ext svg{width:11px;height:11px}
.cbar .tag{color:#7D8996;border:1px solid #2E3845;border-radius:2px;padding:2px 6px;font-size:10.5px;text-transform:uppercase;letter-spacing:.06em}
.cbar .s{display:none}
@media (max-width:640px){.cbar .tag,.cbar .home .l,.cbar .l{display:none}.cbar .s{display:inline}.cbar-in,.cbar ul{gap:14px}.cbar .home{margin-right:auto}}
</style>"""
def concept_bar(prefix, active, level='page'):
    """level='page' when this exact page is the destination, 'true' when inside that section."""
    def cur(k): return f' aria-current="{level}"' if k==active else ''
    mark = '<svg viewBox="1 6 108 68" aria-hidden="true"><g fill="none" stroke="#3CE3F0" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"><path d="M9 50 Q9 64 23 64 Q33 64 36 57 L52 16 L68 64 H84 V16"/><path d="M70 16 H100"/></g></svg>'
    ext = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M9 3h4v4M13 3 7 9M11 9v3.5a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5H7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
    return f"""{CBAR_CSS}<nav class="cbar" aria-label="Concept"><div class="cbar-in">
  <a class="home" href="{prefix}"{cur('overview')}>{mark}<span class="l">JAT Concept 1</span><span class="s">Overview</span> <span class="tag">Calibrated Signal</span></a>
  <ul><li><a href="{prefix}brand-book/"{cur('brand')}><span class="l">Brand Book</span><span class="s">Brand</span></a></li><li><a href="{prefix}site/"{cur('site')}><span class="l">Site Prototype</span><span class="s">Site</span></a></li><li><a href="{prefix}#design-files"><span class="l">Design Files</span><span class="s">Files</span></a></li><li><a class="ext" href="{REPO}" rel="noopener">GitHub {ext}</a></li></ul>
</div></nav>"""

FONTS = '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,100..900&family=Martian+Mono:wdth,wght@75..112.5,100..800&display=swap">'
BASE = 'https://jeremyanderson.tech'
ARROW = '<svg class="arrow" viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>'
EXT = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M9 3h4v4M13 3 7 9M11 9v3.5a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5H7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
CHECK = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8.5 6.5 12 13 4.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>'
PLUS = '<svg viewBox="0 0 18 18" aria-hidden="true"><path d="M9 3v12M3 9h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>'
CH = {
 'ch1': ('CH1','Platforms','platforms.html','--ch1'),
 'ch2': ('CH2','Intelligence','intelligence.html','--ch2'),
 'ch3': ('CH3','Security','security.html','--ch3'),
 'ch4': ('CH4','Operations','operations.html','--ch4'),
}
def tag(k):
    i,n,h,c = CH[k]
    return f'<span class="ch-tag" style="--c:var({c})"><i></i><b>{i}</b> {n}</span>'

NAV = [('services.html','Services','services'),('work.html','Work','work'),('approach.html','Approach','approach'),('about.html','About','about'),('insights.html','Insights','insights')]

ORG = {
  "@context":"https://schema.org",
  "@type":"ProfessionalService",
  "@id": BASE+"/#org",
  "name":"Jeremy Anderson Tech",
  "alternateName":"JAT",
  "url": BASE+"/",
  "email":"info@jeremyanderson.tech",
  "slogan":"Systems that hold steady.",
  "description":"Founder-led engineering studio for custom WordPress platforms, web design, custom software and PWAs, AI development and automation, cybersecurity, hosting and SEO.",
  "address":{"@type":"PostalAddress","addressLocality":"Shrewsbury","addressRegion":"MA","addressCountry":"US"},
  "areaServed":[{"@type":"State","name":"Massachusetts"},{"@type":"Country","name":"United States"}],
  "founder":{"@type":"Person","@id":BASE+"/about/#jeremy","name":"Jeremy Anderson","jobTitle":"Founder","sameAs":["https://github.com/CptNope","https://www.linkedin.com/in/jeremy-anderson-53963b222/","https://x.com/JeremyA59964523"]},
  "sameAs":["https://github.com/CptNope","https://www.linkedin.com/in/jeremy-anderson-53963b222/","https://x.com/JeremyA59964523"],
  "knowsAbout":["WordPress","Progressive web apps","Retrieval-augmented generation","Web accessibility","Cybersecurity","SIEM","Technical SEO"]
}

def jsonld(obj):
    return '<script type="application/ld+json">'+json.dumps(obj, ensure_ascii=False)+'</script>'

def header(active):
    items = ''.join(f'<li><a class="nl" href="{h}"{" aria-current=\"page\"" if active==k else ""}>{t}</a></li>' for h,t,k in NAV)
    return f'''<a class="skip" href="#main">Skip to content</a>
{concept_bar('../','site', 'page' if active=='home' else 'true') if PAGES else ''}
<header class="site-header"><div class="wrap">
  <a class="brand" href="index.html" aria-label="Jeremy Anderson Tech, home"><svg viewBox="1 6 468 68" aria-hidden="true"><use href="#lk-h"/></svg></a>
  <nav class="nav" aria-label="Primary">
    <ul id="primary-menu">{items}</ul>
    <a class="btn btn-primary btn-sm" href="contact.html">Book a call{ARROW}</a>
    <button class="menu-btn" type="button" aria-expanded="false" aria-controls="primary-menu"><svg viewBox="0 0 18 18" aria-hidden="true"><path d="M2 5h14M2 9h14M2 13h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg><span class="lbl">Menu</span></button>
  </nav>
</div></header>'''

def footer():
    return f'''<footer class="site-footer"><div class="wrap">
  <div class="foot-grid">
    <div>
      <a href="index.html" aria-label="Jeremy Anderson Tech, home" style="display:inline-block;color:var(--fg)"><svg viewBox="1 6 468 68" width="220" height="32" aria-hidden="true"><use href="#lk-h"/></svg></a>
      <p class="muted" style="margin-top:var(--s4);max-width:36ch">Web platforms, AI and security, built and run by one accountable engineering studio in Shrewsbury, Massachusetts.</p>
      <div class="copy-line"><span class="mono">info@jeremyanderson.tech</span><button class="btn-copy" type="button" data-copy-text="info@jeremyanderson.tech">Copy</button></div>
    </div>
    <div><h2>Services</h2><ul>
      <li><a href="platforms.html">CH1 Platforms</a></li><li><a href="intelligence.html">CH2 Intelligence</a></li><li><a href="security.html">CH3 Security</a></li><li><a href="operations.html">CH4 Operations</a></li><li><a href="services.html#pricing">Pricing</a></li></ul></div>
    <div><h2>Studio</h2><ul>
      <li><a href="work.html">Work</a></li><li><a href="approach.html">Approach</a></li><li><a href="about.html">About</a></li><li><a href="insights.html">Insights</a></li><li><a href="contact.html">Contact</a></li><li><a href="sitemap.html">Site map</a></li>{'<li><a href="../brand-book/">Brand book</a></li><li><a href="../">Concept overview</a></li>' if PAGES else ''}</ul></div>
    <div><h2>Elsewhere</h2><ul>
      <li><a href="https://github.com/CptNope" rel="noopener">GitHub</a></li><li><a href="https://www.linkedin.com/in/jeremy-anderson-53963b222/" rel="noopener">LinkedIn</a></li><li><a href="https://x.com/JeremyA59964523" rel="noopener">X</a></li>
      <li><a href="{BASE}/privacy-policy/">Privacy policy</a></li><li><a href="{BASE}/terms-of-service/">Terms of service</a></li><li><a href="{BASE}/accessibility/">Accessibility</a></li></ul></div>
  </div>
  <div class="foot-base"><span class="readout long">© <span data-year>2026</span> Jeremy Anderson Tech · Shrewsbury, MA</span><span class="concept-note">Website concept v1 for review. Not the live site.</span></div>
</div></footer>'''

def close_cta(title='Tell us what the system needs to do.', body="A free 30-minute discovery call, then a written proposal within three business days."):
    return f'''<section class="close" aria-labelledby="close-h"><div class="graticule" aria-hidden="true"></div><div class="wrap">
  <div><h2 id="close-h">{title}</h2></div>
  <div class="contact-lines">
    <p class="lede" style="color:var(--fg)">{body}</p>
    <div class="cta-row"><a class="btn btn-primary" href="contact.html">Book a discovery call{ARROW}</a><a class="btn btn-secondary" href="services.html#pricing">See pricing</a></div>
    <div class="copy-line"><span class="readout">Or write</span><span class="mono">info@jeremyanderson.tech</span><button class="btn-copy" type="button" data-copy-text="info@jeremyanderson.tech">Copy</button></div>
  </div>
</div></section>'''

def page_head(crumbs, h1, lede, color='--ch1', extra=''):
    cr = ''.join(f'<li><a href="{h}">{t}</a></li>' if h else f'<li aria-current="page">{t}</li>' for h,t in crumbs)
    return f'''<header class="page-head"><div class="graticule" aria-hidden="true"></div>
  <canvas class="head-trace" data-wave="{ {'--ch1':'step','--ch2':'burst','--ch3':'pulse','--ch4':'sine'}.get(color,'step') }" data-color="{color}" aria-hidden="true"></canvas>
  <div class="wrap"><ol class="crumbs" aria-label="Breadcrumb">{cr}</ol><h1>{h1}</h1><p class="lede">{lede}</p>{extra}</div></header>'''

def page(title, desc, path, active, body, schema=None, is_index=False):
    canonical = BASE + path
    meta = f'''<title>{title}</title>
<meta name="description" content="{html.escape(desc)}">
<link rel="canonical" href="{canonical}">
<meta name="robots" content="noindex">
<meta property="og:type" content="website"><meta property="og:title" content="{html.escape(title)}"><meta property="og:description" content="{html.escape(desc)}"><meta property="og:url" content="{canonical}">
<meta name="theme-color" content="#0A0C0F">
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='1 6 108 68'%3E%3Cg fill='none' stroke='%233CE3F0' stroke-width='9' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 50 Q9 64 23 64 Q33 64 36 57 L52 16 L68 64 H84 V16'/%3E%3Cpath d='M70 16 H100'/%3E%3C/g%3E%3C/svg%3E">
<script>document.documentElement.classList.add('js')</script>
{FONTS}
<link rel="stylesheet" href="site.css">'''
    schemas = [ORG] + (schema or [])
    content = f'''{SPRITE}
{header(active)}
<main id="main">
{body}
</main>
{footer()}
{''.join(jsonld(s) for s in schemas)}
<script src="site.js" defer></script>'''
    import re
    content = re.sub(r'class="readout([^"]*)"([^>]*)>([^<]{35,})<', lambda m: f'class="readout long{m.group(1)}"{m.group(2)}>{m.group(3)}<', content)
    if is_index and not PAGES:
        return meta + '\n' + content
    return f'<!doctype html>\n<html lang="en">\n<head>\n<meta charset="utf-8">\n<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n{meta}\n</head>\n<body>\n{content}\n</body>\n</html>'
