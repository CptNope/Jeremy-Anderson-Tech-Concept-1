import sys, os
sys.path.insert(0, os.path.dirname(__file__))
from common import *
from plates import *

OUT = os.environ.get('OUT', str(ROOT)+'/')
os.makedirs(OUT, exist_ok=True)

def strip_html():
    return '''<nav class="strip" aria-label="Service channels">
  <a href="platforms.html" style="--c:var(--ch1)"><span class="v"><span class="id">CH1</span> Platforms</span><span class="p">Sites &amp; apps · from $3,500</span></a>
  <a href="intelligence.html" style="--c:var(--ch2)"><span class="v"><span class="id">CH2</span> Intelligence</span><span class="p">AI &amp; automation · from $1,200</span></a>
  <a href="security.html" style="--c:var(--ch3)"><span class="v"><span class="id">CH3</span> Security</span><span class="p">Audit &amp; harden · from $850</span></a>
  <a href="operations.html" style="--c:var(--ch4)"><span class="v"><span class="id">CH4</span> Operations</span><span class="p">Hosting &amp; care · from $350/mo</span></a>
</nav>'''

CH_ROWS = [
 ('ch1','Platforms','A website or app that wins work, loads fast, and your team can update without calling anyone.',
  ['Web design and redesign','Custom WordPress engineering','Custom software and PWAs','Migrations without losing search rank'],'from $3,500','fixed fee'),
 ('ch2','Intelligence','AI and automation that removes real work from your week, running on data you control.',
  ['AI discovery and roadmaps','Custom AI tools and assistants','Private LLMs and document search (RAG)','Integrations and workflow automation'],'from $1,200','fixed fee'),
 ('ch3','Security','Know where you are exposed, close the gaps, and see attacks before they become incidents.',
  ['Security audits','WordPress and server hardening','SIEM and SOC monitoring','Massachusetts privacy (MDPA) review'],'from $850','fixed fee'),
 ('ch4','Operations','Hosting, updates, backups, monitoring and search visibility, handled every month.',
  ['Managed hosting','Care plans with response times','Performance and Core Web Vitals','SEO, AEO and GEO visibility'],'from $350','per month'),
]
def ch_rows():
    out=['<div class="ch-rows">']
    for k,name,promise,items,price,unit in CH_ROWS:
        i,n,h,c = CH[k]
        lis=''.join(f'<li>{x}</li>' for x in items)
        out.append(f'''<article class="ch-row" style="--c:var({c})">
  <div class="id"><i></i>{i}</div>
  <div><h3><span class="id-inline">{i} </span>{name}</h3><p>{promise}</p></div>
  <ul aria-label="{name} services">{lis}</ul>
  <div class="price"><span class="amt">{price}</span><span class="readout">{unit}</span><a class="link-arrow" href="{h}">Explore {name}<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a></div>
</article>''')
    out.append('</div>')
    return ''.join(out)

POSTS = [
 ("God's Eye Lite: I built a free live world map that runs in your browser", BASE+'/gods-eye-lite/','AI Systems','2026-10-01','Oct 1, 2026','Satellites, weather, quakes and thousands of public cameras on a 3D globe, with no account and no server bill.'),
 ('I built a powerful interactive archive for ancient texts', BASE+'/i-built-a-powerful-interactive-archive-for-ancient-texts/','Infrastructure','2026-10-01','Oct 1, 2026','How Chronos &amp; Canon compares manuscripts, maps and traditions in a single offline-capable app.'),
 ('Attending OWASP BASC Boston 2026: AI, AppSec, and the future of backported security', BASE+'/attending-owasp-basc-boston-2026-ai-appsec-and-the-future-of-backported-security/','Cybersecurity','2026-04-22','Apr 22, 2026','Notes from the conference on AI-assisted backporting and keeping legacy systems secure.'),
]
def posts_html(n=3):
    return '<div class="posts">'+''.join(f'''<a class="post" href="{u}"><div class="meta"><span class="readout">{c}</span><time class="readout" datetime="{d}">{dd}</time></div><h3>{t}</h3><p>{x}</p><span class="readout" style="color:var(--ch1)">Read on the current site</span></a>''' for t,u,c,d,dd,x in POSTS[:n])+'</div>'

# ---------------- HOME ----------------
home = f'''
<section class="hero" aria-labelledby="hero-h">
  <div class="graticule" aria-hidden="true"></div>
  <canvas id="heroTrace" aria-hidden="true"></canvas>
  <div class="hero-state readout" data-state="run" aria-hidden="true"><span><span class="dot"></span><span class="lbl">Acquiring signal</span></span></div>
  <div class="wrap">
    <div class="hero-copy">
      <h1 id="hero-h">Systems that hold steady.</h1>
      <p class="lede">We build the website, software and AI your business runs on, then secure it, host it and keep it running. Prices are published. You work directly with the engineer.</p>
      <div class="cta-row"><a class="btn btn-primary" href="contact.html">Book a discovery call{ARROW}</a><a class="btn btn-secondary" href="work.html">See the work</a></div>
      <p class="readout note">Free 30-minute call · Written proposal in 3 business days</p>
    </div>
    {strip_html()}
  </div>
</section>

<section class="section" aria-labelledby="why-h"><div class="wrap">
  <div class="section-head"><h2 id="why-h">Most small businesses run on four vendors that never talk to each other.</h2><p class="lede">A designer built the site, a host runs it, an IT company "handles security", and an agency sends SEO reports. When something breaks, everyone points somewhere else.</p></div>
  <div class="versus">
    <div class="screen graticule"><div class="screen-bar"><span class="readout">Four vendors</span><span class="readout">Free-running</span></div><canvas data-versus="many" aria-hidden="true"></canvas>
      <div class="labels"><h3>Out of phase</h3><p>Each vendor optimizes their own piece. Handoffs drop context, and nobody owns the whole system.</p></div></div>
    <div class="screen graticule"><div class="screen-bar"><span class="readout">One studio</span><span class="readout" style="color:var(--ch4)">Locked</span></div><canvas data-versus="one" aria-hidden="true"></canvas>
      <div class="labels"><h3>One accountable lead</h3><p>The engineer who architects your system also secures it, hosts it and answers when it needs attention.</p></div></div>
  </div>
</div></section>

<section class="section alt" aria-labelledby="ch-h"><div class="wrap">
  <div class="section-head"><h2 id="ch-h">Four channels. One engineer on every layer.</h2><p class="lede">Choose one channel or combine them. Every build includes basic security hardening, accessibility and performance work as standard.</p></div>
  {ch_rows()}
</div></section>

<section class="section" aria-labelledby="work-h"><div class="wrap">
  <div class="section-head"><h2 id="work-h">Engineering you can inspect.</h2><p class="lede">Client engagements stay confidential. These open-source projects are public, live and readable line by line, so you can judge the work before you hire it.</p></div>
  <div class="work-feature">
    <a class="screen" href="work-gods-eye-lite.html" aria-label="God's Eye Lite case study" style="display:block">{gods_eye('hge',fit='meet')}</a>
    <div class="work-meta">
      <h3>God's Eye Lite</h3>
      <div class="tag-list">{tag('ch1')}{tag('ch4')}</div>
      <p class="muted">A free 3D world map that runs entirely in the browser: live satellites, weather, earthquakes, thousands of public traffic cameras and offline trail maps, with no account and almost no server.</p>
      <ul class="facts">
        <li><span>Proves</span><span>Complex live data delivered fast, privately and at near-zero hosting cost</span></li>
        <li><span>Built with</span><span>React, CesiumJS, Workbox, GitHub Actions, Cloudflare Workers</span></li>
        <li><span>Status</span><span>Live and in active development</span></li>
      </ul>
      <div class="links-row"><a class="link-arrow" href="work-gods-eye-lite.html">Read the case study<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a><a class="link-arrow" href="https://cptnope.github.io/gods-eye-lite/" rel="noopener">Open live app{EXT}</a></div>
    </div>
  </div>
  <div class="work-pair">
    <a class="work-card" href="work-cytoscape.html"><div class="plate">{cytoscape('hcy')}</div><div class="body"><h3>Cytoscape: Immune Defense</h3><div class="tag-list">{tag('ch1')}</div><p>An installable immunology arcade game with a tested engine, offline play and automatic deploys.</p><span class="more">Case study{ARROW}</span></div></a>
    <a class="work-card" href="work-chronos-canon.html"><div class="plate">{chronos('hcc')}</div><div class="body"><h3>Chronos &amp; Canon</h3><div class="tag-list">{tag('ch1')}{tag('ch2')}</div><p>A research app that turns scholarly evidence into navigable texts, graphs, timelines and maps in three languages.</p><span class="more">Case study{ARROW}</span></div></a>
  </div>
</div></section>

<section class="section alt" aria-labelledby="price-h"><div class="wrap">
  <div class="section-head"><h2 id="price-h">Prices before the first call.</h2><p class="lede">Fixed fees with a signed scope. If we misestimate, you don't pay for it. Hourly work and monthly care plans are published too.</p></div>
  <div class="price-table">
    <div class="price-row"><span class="name">Business Site {tag('ch1')}</span><span class="desc">Up to 8 pages, responsive build, on-page SEO and schema, hardening, 30 days support.</span><span class="amt">$3,500</span><span class="time">3–4 weeks</span></div>
    <div class="price-row"><span class="name"><span>Custom Platform <span class="popular">Most requested</span></span>{tag('ch1')}</span><span class="desc">Custom post types, plugin features, block patterns, API integration, CMS training, 60 days support.</span><span class="amt">$7,500</span><span class="time">5–7 weeks</span></div>
    <div class="price-row"><span class="name">Custom AI Tool {tag('ch2')}</span><span class="desc">One production AI tool for one real operational problem, with private model options and training.</span><span class="amt">from $8,500</span><span class="time">Scoped per project</span></div>
    <div class="price-row"><span class="name">Security Hardening {tag('ch3')}</span><span class="desc">Full audit plus every fix implemented and documented: 2FA, WAF, headers, log monitoring.</span><span class="amt">$2,400</span><span class="time">1–2 weeks</span></div>
    <div class="price-row"><span class="name">Guardian care {tag('ch4')}</span><span class="desc">Updates, backups, uptime and performance tracking, 24-hour priority response, monthly strategy call.</span><span class="amt">$850/mo</span><span class="time">3-month minimum</span></div>
  </div>
  <p style="margin-top:var(--s5)"><a class="link-arrow" href="services.html#pricing">See every package, rate and add-on<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a></p>
</div></section>

<section class="section" aria-labelledby="proc-h"><div class="wrap">
  <div class="section-head"><h2 id="proc-h">From first call to a system that runs itself.</h2><p class="lede">Most projects go from first contact to signed scope in one to two weeks, and start one to two weeks after that.</p></div>
  <ol class="steps">
    <li><h3>Discovery call</h3><span class="n">Day 0</span><p>30 minutes, free. What the system needs to do and what it's worth to you.</p></li>
    <li><h3>Written proposal</h3><span class="n">3 days</span><p>Scope, price, timeline and what's out of scope, in plain language.</p></li>
    <li><h3>Scope and deposit</h3><span class="n">Signed</span><p>50% deposit on fixed-fee projects. Changes go through written change orders.</p></li>
    <li><h3>Build and harden</h3><span class="n">Weeks</span><p>Design, engineering, security and accessibility testing, with reviews as it takes shape.</p></li>
    <li><h3>Launch and run</h3><span class="n">Ongoing</span><p>30 to 90 days of included support, then an optional care plan.</p></li>
  </ol>
  <p style="margin-top:var(--s6)"><a class="link-arrow" href="approach.html">How we work, in detail<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a></p>
</div></section>

<section class="section alt" aria-labelledby="founder-h"><div class="wrap founder">
  <div class="portrait graticule"><div class="ph">Founder portrait<br><span class="readout">To be photographed · see brand book §13</span></div><span class="readout" style="position:relative">Shrewsbury, MA</span></div>
  <div>
    <h2 id="founder-h">You work with Jeremy, every time.</h2>
    <p class="lede" style="margin-top:var(--s4)">Jeremy Anderson has built for the web since 1998, co-founded an IT and web company in San Antonio, and now leads every JAT engagement personally, from the first call through every update after launch.</p>
    <ul class="timeline">
      <li><span class="y">Since 1998</span><span>Building websites, systems and software</span></li>
      <li><span class="y">W3C / WAI</span><span>Web accessibility professional certification</span></li>
      <li><span class="y">2022 →</span><span>AI consultant and developer for research and science communication</span></li>
      <li><span class="y">Ongoing</span><span>Computer science studies at Harvard; attends OWASP security events</span></li>
    </ul>
    <p style="margin-top:var(--s5)"><a class="link-arrow" href="about.html">About the studio<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></a></p>
  </div>
</div></section>

<section class="section" aria-labelledby="ins-h"><div class="wrap">
  <div class="section-head"><h2 id="ins-h">Field notes.</h2><p class="lede">Build logs, security notes and what we learn shipping real systems.</p></div>
  {posts_html()}
</div></section>
{close_cta()}
'''
website_schema = {"@context":"https://schema.org","@type":"WebSite","@id":BASE+"/#site","url":BASE+"/","name":"Jeremy Anderson Tech","publisher":{"@id":BASE+"/#org"}}
open(OUT+'index.html','w').write(page('Jeremy Anderson Tech','Web platforms, AI and security, built and run by one accountable engineering studio in Shrewsbury, Massachusetts. Published prices. Book a free discovery call.','/','home',home,[website_schema],is_index=True))

# ---------------- SERVICES ----------------
def prow(name, desc, amt, time, chk=None, pop=False):
    t = tag(chk) if chk else ''
    nm = f'<span>{name} <span class="popular">{pop if isinstance(pop,str) else "Most popular"}</span></span>' if pop else name
    return f'<div class="price-row"><span class="name">{nm}{t}</span><span class="desc">{desc}</span><span class="amt">{amt}</span><span class="time">{time}</span></div>'

FAQ = [
 ('How do deposits and payments work?','Fixed-fee projects take a 50% deposit and the balance on delivery. Retainers are billed monthly in advance. Hourly work is invoiced every two weeks, or at completion for jobs under 10 hours.'),
 ('Can you work on my existing WordPress site?','Yes. Many projects start with an audit and improvements to an existing site. If a rebuild makes more sense, the proposal will say why and what it costs.'),
 ('What if my budget is smaller than a package?','Scopes can be adjusted. Common starting points are a single plugin feature, a landing page, a security audit or a Watchdog care plan you grow from.'),
 ('How soon can you start?','After a discovery call, you get a written proposal within three business days. Most projects sign within one to two weeks of first contact and start one to two weeks after signing. Active projects are limited so each gets full attention.'),
 ('Do you meet in person?','Yes, across Greater Boston and Worcester County. Ongoing work is mostly remote. There is no travel surcharge within 50 miles of Shrewsbury.'),
 ('What happens if the scope changes?','Fixed-fee work follows a signed scope. New requirements get a written change-order estimate before any work starts. If we misestimate our own work, you are not billed for it.'),
]
def faq_html(items):
    return '<div class="faq">'+''.join(f'<details><summary>{q}{PLUS}</summary><p>{a}</p></details>' for q,a in items)+'</div>'
faq_schema = {"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":q,"acceptedAnswer":{"@type":"Answer","text":a}} for q,a in FAQ]}

services = page_head([('index.html','Home'),(None,'Services')],'Services and pricing','Four service channels, published prices, and one engineer accountable for all of it.') + f'''
<section class="section" aria-labelledby="sv-h"><div class="wrap">
  <h2 id="sv-h" class="vh">Service channels</h2>
  {ch_rows()}
</div></section>
<section class="section alt" id="pricing" aria-labelledby="pr-h" style="scroll-margin-top:72px"><div class="wrap">
  <div class="section-head"><h2 id="pr-h">Pricing</h2><p class="lede">Every price below is current. Third-party licenses and hosting are quoted separately in your proposal.</p></div>
  <h3 style="margin-bottom:var(--s4)">WordPress platforms</h3>
  <div class="price-table">
    {prow('Business Site','Up to 8 pages, child or block theme, Core Web Vitals build, on-page SEO and schema, contact form and map, hardening and SSL, 30 days support.','$3,500','3–4 weeks','ch1')}
    {prow('Custom Platform','Unlimited pages, custom post types, up to 2 plugin features, block patterns, advanced SEO, caching and CDN, login protection, 1 API integration, CMS training, 60 days support.','$7,500','5–7 weeks','ch1','Most requested')}
    {prow('Enterprise System','Architecture spec, plugin suites, multisite, WooCommerce custom checkout, SIEM/SOC integration, compliance review, load testing, developer handoff, 90 days support.','$15,000+','8–12 weeks','ch1')}
  </div>
  <h3 style="margin:var(--s7) 0 var(--s4)">AI and automation</h3>
  <div class="price-table">
    {prow('AI Discovery','2-hour workshop, top 3 AI opportunities, feasibility, build-vs-buy advice, written roadmap with costs, follow-up call.','$1,200','1 week','ch2')}
    {prow('Custom AI Tool','RAG system, chat interface, log analyzer or document assistant. Model selection, vector search, interface or API, integration, training, 60 days support.','from $8,500','Scoped per project','ch2',True)}
    {prow('AI System Integration','Multi-tool architecture, large knowledge bases, n8n or custom automation, AI-assisted monitoring, privacy review, change management, 90 days of iteration.','$20,000+','8–14 weeks','ch2')}
  </div>
  <h3 style="margin:var(--s7) 0 var(--s4)">Security</h3>
  <div class="price-table">
    {prow('Security Audit','Vulnerability scan, user roles, login and 2FA, SSL/TLS, permissions and server config, risk-rated report, debrief call.','$850','5 business days','ch3')}
    {prow('Hardening Package','Everything in the audit, with every fix implemented: hardening, brute-force blocking, 2FA, headers and WAF, log monitoring, malware cleanup, change log.','$2,400','1–2 weeks','ch3',True)}
    {prow('SIEM / SOC Setup','Central logging and alerting across multiple sites or apps, AI-assisted log analysis, uptime monitoring, incident runbook, staff dashboard.','$4,500+','2–4 weeks','ch3')}
  </div>
  <h3 style="margin:var(--s7) 0 var(--s4)">Care plans</h3>
  <div class="price-table">
    {prow('Watchdog','About 3 hours. Updates, weekly uptime checks, offsite backups, monthly scan, 48-hour response, monthly status email. Extra hours $115.','$350/mo','3-month min','ch4')}
    {prow('Guardian','About 7 hours. Adds 24-hour response, Core Web Vitals tracking, monthly strategy call, content edits, quarterly security report. Extra hours $110.','$850/mo','3-month min','ch4')}
    {prow('Partner','About 15 hours. Adds 12-hour response, active development, SEO monitoring, compliance monitoring, AI tool maintenance, quarterly full audit. Extra hours $105.','$1,800/mo','3-month min','ch4')}
    {prow('Embedded Dev','About 40 hours. Same-day response, dedicated capacity, Slack or Teams access, weekly sync, annual architecture review.','$4,200/mo','3-month min','ch4')}
  </div>
  <div class="two-col" style="margin-top:var(--s8)">
    <div>
      <h3 style="margin-bottom:var(--s4)">Hourly rates</h3>
      <div class="price-table">
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">WordPress development</span><span class="amt">$115/hr</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Full-stack and custom development</span><span class="amt">$125/hr</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Cybersecurity consulting</span><span class="amt">$135/hr</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Strategy and technical advisory</span><span class="amt">$165/hr</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">AI systems and consulting</span><span class="amt">$185/hr</span></div>
      </div>
      <p class="muted" style="margin-top:var(--s4);font-size:.9375rem">Two-hour minimum (one hour for advisory). Prepaid blocks of 20+ hours save 10% and stay valid for 90 days.</p>
    </div>
    <div>
      <h3 style="margin-bottom:var(--s4)">Add-ons</h3>
      <div class="price-table">
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">AI Readiness Assessment</span><span class="amt">$495</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Schema &amp; AEO Markup</span><span class="amt">$475</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Local SEO Setup</span><span class="amt">$550</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Technical SEO Audit</span><span class="amt">$575</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Performance Optimization</span><span class="amt">$650</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">MDPA Compliance Review</span><span class="amt">$675</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Site Migration</span><span class="amt">$750</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">Custom Gutenberg Block</span><span class="amt">$950 · $650 each more</span></div>
        <div class="price-row" style="grid-template-columns:1fr auto"><span class="name">WordPress CMS Training</span><span class="amt">$350</span></div>
      </div>
    </div>
  </div>
</div></section>
<section class="section" aria-labelledby="faq-h"><div class="wrap">
  <div class="section-head"><h2 id="faq-h">Common questions</h2><p class="lede">Payment, timing and how scope changes are handled.</p></div>
  {faq_html(FAQ)}
</div></section>
{close_cta()}'''
open(OUT+'services.html','w').write(page('Services and Pricing · Jeremy Anderson Tech','Custom WordPress platforms, AI tools, security hardening and managed care plans, with every package price and hourly rate published.','/services/','services',services,[faq_schema]))

# ---------------- CHANNEL PAGES ----------------
def channel_page(key, slug, title, h1, lede, outcome, svcs, packages, proof, faqs, desc):
    i,n,h,c = CH[key]
    svc_html=''.join(f'''<section id="{sid}" aria-labelledby="{sid}-h"><h3 id="{sid}-h">{st}</h3><p class="outcome">{so}</p><ul class="check-list" style="--c:var({c})">{''.join(f'<li>{CHECK}<span>{x}</span></li>' for x in sl)}</ul></section>''' for sid,st,so,sl in svcs)
    pk=''.join(prow(*p) for p in packages)
    svc_schema={"@context":"https://schema.org","@type":"Service","name":n,"serviceType":title,"provider":{"@id":BASE+"/#org"},"areaServed":"US","description":desc,
      "hasOfferCatalog":{"@type":"OfferCatalog","name":n+" packages","itemListElement":[{"@type":"Offer","name":p[0],"description":p[1],"priceSpecification":{"@type":"PriceSpecification","priceCurrency":"USD","price":p[2].replace('$','').replace(',','').replace('from ','').replace('+','').replace('/mo','')}} for p in packages]}}
    body = page_head([('index.html','Home'),('services.html','Services'),(None,f'{i} {n}')], h1, lede, c, f'<div class="tag-list">{tag(key)}</div>') + f'''
<section class="section" aria-labelledby="out-h"><div class="wrap aside-col">
  <div><h2 id="out-h" style="margin-bottom:var(--s5)">{outcome[0]}</h2><p class="lede">{outcome[1]}</p>
    <div class="svc-grid" style="margin-top:var(--s7)">{svc_html}</div></div>
  <section class="sticky-card" aria-label="Get started">
    <span class="readout" style="color:var({c})">{i} · {n}</span>
    <span class="amt">{packages[0][2]}</span><span class="muted" style="margin-top:-8px">{packages[0][0]}, the usual starting point</span>
    <a class="btn btn-primary" href="contact.html">Book a discovery call{ARROW}</a>
    <a class="btn btn-secondary" href="#packages">Compare packages</a>
    <p class="readout" style="margin:0">Free 30-minute call · Proposal in 3 business days</p>
  </section>
</div></section>
<section class="section alt" id="packages" aria-labelledby="pk-h" style="scroll-margin-top:72px"><div class="wrap">
  <div class="section-head"><h2 id="pk-h">Packages</h2><p class="lede">Fixed fees with a signed scope, or hourly at the published rate.</p></div>
  <div class="price-table">{pk}</div>
</div></section>
{proof}
<section class="section" aria-labelledby="cf-h"><div class="wrap">
  <div class="section-head"><h2 id="cf-h">Questions about {n.lower()}</h2><p class="lede">If yours isn't here, ask on the call.</p></div>
  {faq_html(faqs)}
</div></section>
{close_cta()}'''
    open(OUT+slug,'w').write(page(f'{n} · Jeremy Anderson Tech', desc, '/services/'+slug.replace('.html','')+'/', 'services', body, [svc_schema]))

def proof_block(items):
    cards=''
    for href,plate,t,p in items:
        cards+=f'<a class="work-card" href="{href}"><div class="plate">{plate}</div><div class="body"><h3>{t}</h3><p>{p}</p><span class="more">Case study{ARROW}</span></div></a>'
    return f'''<section class="section" aria-labelledby="pf-h"><div class="wrap"><div class="section-head"><h2 id="pf-h">Proof you can open</h2><p class="lede">Public projects that use the same engineering this channel sells.</p></div><div class="work-pair" style="margin-top:0">{cards}</div></div></section>'''

channel_page('ch1','platforms.html','Web design, WordPress engineering and custom software',
  'Websites and apps that win work.',
  'Design, custom WordPress engineering and progressive web apps, built fast, accessible and secure from the first commit.',
  ('A site your team can run, and your customers can use on any device.','Every Platforms build ships with a block-based editor your staff can use, Core Web Vitals optimization, schema markup, WCAG 2.2 AA accessibility testing and basic security hardening. You own the code and the content.'),
  [('web-design','Web design and redesign','A clear, credible site that turns visitors into calls.',['Content strategy and page structure built around your services','Visual design system matched to your brand','Responsive layouts tested on real phones','Accessibility designed in, not patched on']),
   ('wordpress','Custom WordPress engineering','A platform that fits how your business actually works.',['Block themes and reusable patterns your team can edit','Custom post types, taxonomies and plugin features','Custom Gutenberg blocks with editor controls','WooCommerce with custom checkout when you sell online']),
   ('custom-software','Custom software and PWAs','Tools your staff and customers install like apps.',['React applications with Node.js and REST APIs','Offline-capable progressive web apps','Installable on iOS, Android and desktop','Automated testing and deployment pipelines']),
   ('migrations','Migrations and rebuilds','Move or rebuild without losing search rank.',['Host-to-host WordPress migrations with DNS and SSL','URL mapping and 301 redirects for every old page','Pre- and post-launch crawl comparison','30 days of post-migration monitoring'])],
  [('Business Site','Up to 8 pages, responsive build, on-page SEO and schema, contact form and map, hardening, 30 days support.','$3,500','3–4 weeks'),
   ('Custom Platform','Custom post types, 2 plugin features, block patterns, API integration, CMS training, 60 days support.','$7,500','5–7 weeks',None,'Most requested'),
   ('Enterprise System','Multisite, plugin suites, WooCommerce, compliance review, load testing, developer handoff, 90 days support.','$15,000+','8–12 weeks'),
   ('Custom Gutenberg Block','Editor controls, front-end rendering, full-site-editing compatible.','$950','+$650 each more'),
   ('Site Migration','Host-to-host move with DNS, SSL, testing and 30 days monitoring.','$750','Add-on')],
  proof_block([('work-cytoscape.html',cytoscape('p1cy',labels=False),'Cytoscape: Immune Defense','Installable PWA with offline play and a tested engine.'),('work-chronos-canon.html',chronos('p1cc',labels=False),'Chronos &amp; Canon','Data-heavy research app with four linked views.')]),
  [('Will I be able to edit the site myself?','Yes. Builds use the WordPress block editor with patterns designed for your content, and Custom Platform includes CMS training for your team.'),
   ('Do you use page builders?','No. Builds use native WordPress blocks and custom blocks, which load faster and do not lock you into a builder subscription.'),
   ('Who owns the site?','You do: the code, the content and the domain. Hosting with JAT is optional.')],
  'Web design, custom WordPress engineering, custom software and progressive web apps from Jeremy Anderson Tech. Business Site from $3,500.')

channel_page('ch2','intelligence.html','AI development and automation',
  'AI that removes real work.',
  'Custom AI tools, private language models and automation that solve one operational problem at a time, on data you control.',
  ('Start with the problem, not the model.','Every Intelligence engagement begins by finding where AI genuinely saves time or money, what it would cost, and whether to build or buy. Then we build one focused, production-ready tool and train your team to use it.'),
  [('discovery','AI discovery and roadmaps','Know where AI fits before you spend on it.',['Two-hour workshop with your team','Top three opportunities ranked by value and feasibility','Build-versus-buy recommendation','Written roadmap with cost estimates']),
   ('ai-tools','Custom AI tools','One tool, one problem, in production.',['Document assistants and chat over your own files','Log analyzers and internal search','Custom interface or API','Prompt engineering and testing against real cases']),
   ('private-llm','Private models and RAG','Keep sensitive data on infrastructure you control.',['Local model setup (Ollama) or vetted cloud models','Vector search and retrieval pipelines (RAG)','Data privacy review','Integration with WordPress and existing systems']),
   ('automation','Integrations and automation','Connect the tools you already pay for.',['REST API integrations between your systems','n8n or custom workflow automation','AI-assisted monitoring and alerting','Documentation your team can maintain'])],
  [('AI Readiness Assessment','90-minute workshop and a report ranking your top 3 automation opportunities.','$495','Add-on'),
   ('AI Discovery','Workshop, opportunity audit, feasibility, build-vs-buy, roadmap with costs, follow-up call.','$1,200','1 week'),
   ('Custom AI Tool','Scope and architecture, model setup, RAG if needed, interface or API, integration, training, 60 days support.','from $8,500','Scoped per project',None,True),
   ('AI System Integration','Multi-tool architecture, knowledge bases, automation workflows, monitoring, change management, 90 days iteration.','$20,000+','8–14 weeks')],
  proof_block([('work-chronos-canon.html',chronos('p2cc',labels=False),'Chronos &amp; Canon','Structured knowledge turned into navigable evidence: the foundation of good retrieval.'),('work-gods-eye-lite.html',gods_eye('p2ge',labels=False),"God's Eye Lite",'Many live data sources unified in one client, with keys kept on the device.')]),
  [('Does my data leave my company?','It doesn\'t have to. Local models can run on your own hardware or private cloud, and every engagement includes a data privacy review.'),
   ('Which AI models do you use?','Whichever fits the job and your privacy needs: local open models through Ollama, or established cloud models. The recommendation comes with its trade-offs written down.'),
   ('What if AI isn\'t the right answer?','Then the discovery roadmap will say so. Sometimes a simple automation or a better form solves the problem for less.')],
  'Custom AI tools, private LLMs, RAG and workflow automation from Jeremy Anderson Tech. AI Discovery from $1,200.')

channel_page('ch3','security.html','Cybersecurity and compliance',
  'Know where you are exposed.',
  'Audits, hardening and monitoring for WordPress sites and web applications, explained without fear and documented change by change.',
  ('Security that is measured, fixed and watched.','Start with an audit that rates every risk. Close the gaps with a hardening package that documents each change. Then watch for attacks with centralized logging and alerts your staff can understand.'),
  [('audit','Security audits','A clear, prioritized list of what to fix.',['Plugin and theme vulnerability scan','User roles, login and 2FA review','SSL/TLS, file permissions and server configuration','Risk-rated written report and debrief call']),
   ('hardening','Hardening','Every fix implemented and documented.',['Core, plugin and theme hardening','Brute-force blocking and two-factor authentication','Security headers and web application firewall','Malware scan and cleanup when needed']),
   ('monitoring','SIEM and SOC monitoring','See attacks before they become incidents.',['Centralized logging across sites and apps','Alert rules for common attack patterns','AI-assisted log analysis','Incident response runbook and staff dashboard']),
   ('privacy','Massachusetts privacy review','Data handling you can explain to customers.',['Cookie consent and tracking audit','Plugin telemetry and third-party data flows','Data handling practices review','Remediation checklist'])],
  [('Security Audit','Vulnerability scan, roles, 2FA, SSL/TLS, server config, risk-rated report, debrief.','$850','5 business days'),
   ('Hardening Package','Full audit plus implementation: hardening, 2FA, WAF, headers, log monitoring, change log.','$2,400','1–2 weeks',None,True),
   ('SIEM / SOC Setup','Multi-site monitoring, central logging, alert rules, AI log analysis, runbook, 60 days tuning.','$4,500+','2–4 weeks'),
   ('MDPA Compliance Review','Cookie consent, plugin telemetry, data handling and a remediation checklist.','$675','Add-on')],
  f'''<section class="section" aria-labelledby="pf-h"><div class="wrap two-col"><div><h2 id="pf-h">Current with the field</h2><p class="lede" style="margin-top:var(--s4)">Security practice changes monthly. Jeremy attends OWASP security events and writes up what matters for small organizations.</p></div><div>{posts_html(3)[:0]}<a class="post" href="{POSTS[2][1]}" style="border-top-color:var(--ch3)"><div class="meta"><span class="readout">Cybersecurity</span><time class="readout" datetime="2026-04-22">Apr 22, 2026</time></div><h3>{POSTS[2][0]}</h3><p>{POSTS[2][5]}</p></a></div></div></section>''',
  [('Will hardening break my site?','Changes are tested on a copy first and every one is documented, so anything unexpected can be traced and reversed.'),
   ('We already have antivirus and an IT company. Is that enough?','Device and network security is different from web application security. The audit shows exactly where your website and plugins stand.'),
   ('How often should we audit?','At least yearly, and after any major change. The Partner care plan includes a quarterly full audit.')],
  'WordPress security audits, hardening, SIEM/SOC monitoring and Massachusetts privacy reviews from Jeremy Anderson Tech. Audits from $850.')

channel_page('ch4','operations.html','Managed hosting, care plans and search visibility',
  'Handled every month.',
  'Managed hosting, updates, backups, monitoring, performance and search visibility, on infrastructure run by the people who built your site.',
  ('After launch is when the real work starts.','Care plans keep software current, backups offsite and performance measured, with guaranteed response times. Search work keeps you visible in Google and in the answers AI assistants give.'),
  [('hosting','Managed hosting','Fast, monitored hosting run by your engineer.',['Managed cloud infrastructure on Cloudways and DigitalOcean','SSL, CDN and caching configured for your site','Offsite backups and restore testing','Scoped in your proposal']),
   ('care','Care plans','Updates and fixes with response times you can count on.',['Core, plugin and theme updates','Uptime monitoring and monthly status reports','Response times from 48 hours to same-day','Hours for content edits and improvements']),
   ('performance','Performance','Pages that load fast on real phones.',['Core Web Vitals audit and fixes','Targeting 90+ PageSpeed on mobile and desktop','Image, caching, CDN and asset optimization','Ongoing tracking on Guardian and above']),
   ('visibility','SEO, AEO and GEO','Be found in search results and AI answers.',['Technical SEO audit and Search Console setup','Local SEO for Boston and Worcester businesses','Schema markup for answer engines (AEO)','Structured, citable content for AI assistants (GEO)'])],
  [('Watchdog','Updates, uptime checks, offsite backups, monthly scan, 48-hour response.','$350/mo','3-month min'),
   ('Guardian','Adds 24-hour response, Core Web Vitals tracking, strategy call, content edits, quarterly security report.','$850/mo','3-month min'),
   ('Partner','Adds 12-hour response, active development, SEO and compliance monitoring, quarterly full audit.','$1,800/mo','3-month min'),
   ('Embedded Dev','About 40 hours a month with same-day response, Slack or Teams access and a weekly sync.','$4,200/mo','3-month min'),
   ('Performance Optimization','Core Web Vitals audit and fixes targeting 90+ PageSpeed.','$650','Add-on'),
   ('Technical SEO Audit','Crawl analysis, metadata, Core Web Vitals, structured data gaps, prioritized fixes.','$575','Add-on')],
  proof_block([('work-gods-eye-lite.html',gods_eye('p4ge',labels=False),"God's Eye Lite",'Static hosting, edge workers and service-worker caching that keep running costs near zero.'),('work-cytoscape.html',cytoscape('p4cy',labels=False),'Cytoscape: Immune Defense','Automated deploys with cache-busting so every user gets the latest build.')]),
  [('Do unused retainer hours roll over?','They don\'t roll over automatically, but you can move them into a prepaid hour bank at the retainer rate.'),
   ('Can I cancel a care plan?','After the three-month minimum, with 30 days notice.'),
   ('What is GEO?','Generative engine optimization: structuring your content and data so AI assistants can find, understand and cite your business in their answers.')],
  'Managed hosting, WordPress care plans, Core Web Vitals performance and SEO/AEO/GEO from Jeremy Anderson Tech. Care plans from $350 a month.')
print('ok')
