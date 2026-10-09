import sys, os
sys.path.insert(0, os.path.dirname(__file__))
from common import *
from plates import *
from pages import OUT, posts_html, POSTS, ch_rows, faq_html, prow

AR = '<svg viewBox="0 0 16 16" aria-hidden="true"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>'

# ---------------- WORK ----------------
work = page_head([('index.html','Home'),(None,'Work')],'Work you can open and inspect.','Client engagements stay confidential and are published only with permission. These open-source projects are live, public and readable line by line.') + f'''
<section class="section" aria-labelledby="w1"><div class="wrap">
  <h2 id="w1" class="vh">Featured project</h2>
  <div class="work-feature">
    <a class="screen" href="work-gods-eye-lite.html" style="display:block" aria-label="God's Eye Lite case study">{gods_eye('wge',fit='meet')}</a>
    <div class="work-meta">
      <h3>God's Eye Lite</h3>
      <div class="tag-list">{tag('ch1')}{tag('ch4')}</div>
      <p class="muted">A browser-based 3D world map combining live satellites, weather, earthquakes, public traffic cameras and outdoor navigation, installable and usable offline.</p>
      <ul class="facts"><li><span>Type</span><span>Open-source PWA</span></li><li><span>Stack</span><span>React, TypeScript, CesiumJS, Workbox, Cloudflare Workers</span></li><li><span>Status</span><span>Live, in active development</span></li></ul>
      <div class="links-row"><a class="link-arrow" href="work-gods-eye-lite.html">Case study{AR}</a><a class="link-arrow" href="https://cptnope.github.io/gods-eye-lite/" rel="noopener">Live app{EXT}</a><a class="link-arrow" href="https://github.com/CptNope/gods-eye-lite" rel="noopener">Source{EXT}</a></div>
    </div>
  </div>
  <div class="work-pair">
    <a class="work-card" href="work-cytoscape.html"><div class="plate">{cytoscape('wcy')}</div><div class="body"><h3>Cytoscape: Immune Defense</h3><div class="tag-list">{tag('ch1')}</div><p>An immunology arcade game built as an installable PWA, with its engine separated from the interface and covered by automated tests.</p><span class="more">Case study{ARROW}</span></div></a>
    <a class="work-card" href="work-chronos-canon.html"><div class="plate">{chronos('wcc')}</div><div class="body"><h3>Chronos &amp; Canon</h3><div class="tag-list">{tag('ch1')}{tag('ch2')}</div><p>A digital humanities research app comparing ancient texts, manuscripts and archaeology through linked views in three languages.</p><span class="more">Case study{ARROW}</span></div></a>
  </div>
</div></section>
<section class="section alt" aria-labelledby="w2"><div class="wrap two-col">
  <div><h2 id="w2">Client work</h2></div>
  <div><p class="lede">JAT builds and runs platforms for local businesses, research programs and growing teams across Massachusetts. Client case studies are published here only when the client approves them.</p><p class="muted">Ask on a discovery call and we can walk through relevant work privately.</p><a class="btn btn-secondary" href="contact.html">Ask about relevant work</a></div>
</div></section>
{close_cta()}'''
open(OUT+'work.html','w').write(page('Work · Jeremy Anderson Tech','Open-source projects by Jeremy Anderson Tech you can inspect: God\'s Eye Lite, Cytoscape: Immune Defense and Chronos & Canon.','/work/','work',work))

# ---------------- CASE STUDIES ----------------
def case(slug, path, title, h1, lede, tags, plate, facts, sections, arch, links, desc, app):
    fl=''.join(f'<li><span>{k}</span><span>{v}</span></li>' for k,v in facts)
    ll=''.join(f'<a class="link-arrow" href="{u}" rel="noopener">{t}{EXT}</a>' for t,u in links)
    sec=''.join(f'<h2>{h}</h2>'+''.join(f'<p>{p}</p>' if not p.startswith('<ul') else p for p in ps) for h,ps in sections)
    body = page_head([('index.html','Home'),('work.html','Work'),(None,title)], h1, lede, '--ch1', f'<div class="tag-list">{tags}</div>') + f'''
<section class="section" style="padding-top:var(--s7)" aria-label="Project overview"><div class="wrap">
  <div class="screen">{plate}</div>
  <p class="readout" style="margin-top:var(--s3)">Schematic illustration of the interface. Open the live app for the real thing.</p>
</div></section>
<section class="section" style="padding-top:0;border-top:0" aria-label="Case study"><div class="wrap aside-col">
  <article class="prose">{sec}
    <h2>Architecture</h2>
    <div class="screen diagram-scroll" style="margin-top:var(--s4)" tabindex="0" role="region" aria-label="Architecture diagram, scrolls horizontally"><div>{arch}</div></div><p class="readout diagram-hint" style="display:none;margin-top:var(--s2)">Swipe to see the full diagram</p>
  </article>
  <section class="sticky-card" aria-label="Project facts"><ul class="facts" style="border-top:0">{fl}</ul><div class="links-row" style="flex-direction:column;gap:0">{ll}</div><a class="btn btn-primary" href="contact.html">Build something like this{ARROW}</a></section>
</div></section>
{close_cta('Need a system this dependable?','Tell us what it has to do. You will have a written proposal within three business days.')}'''
    open(OUT+slug,'w').write(page(f'{title} · Work · Jeremy Anderson Tech', desc, path, 'work', body, [app]))

case('work-gods-eye-lite.html','/project/gods-eye-lite/',"God's Eye Lite","God's Eye Lite","A free 3D world map that combines live public data with outdoor navigation, and runs almost entirely in the browser.",tag('ch1')+tag('ch4'),gods_eye('cge',1200,560),
 [('Type','Open-source PWA'),('Role','Design, engineering, infrastructure'),('Stack','React, TypeScript, CesiumJS, Workbox, GitHub Actions, Cloudflare Workers'),('Data','Public APIs, agency feeds, OpenStreetMap'),('Status','Live, in active development')],
 [('The brief',["Make a world of live public data usable by anyone, on any device, without an account and without a server bill that grows with every visitor."]),
  ('What it does',['<ul><li>Shows orbiting satellites and their paths, with orbital positions calculated in the browser.</li><li>Layers recent earthquakes, upcoming launches, radar, clouds, wind, cyclone tracks, lightning and warnings.</li><li>Aggregates thousands of public traffic cameras from transportation agencies and municipalities.</li><li>Adds trails, topographic maps, peaks, water sources, campsites and current conditions for the outdoors.</li><li>Records GPS tracks on the device and exports them as GPX. Track data never leaves the phone.</li><li>Caches chosen map areas for offline use.</li></ul>']),
  ('Engineering decisions',['<strong>Static first.</strong> The app is served as a static site from GitHub Pages, and the browser fetches most data directly from public services. No database, no app server.','<strong>Pre-built catalogs.</strong> Agencies block browsers from downloading full camera catalogs, so a scheduled GitHub Actions job collects and processes camera metadata and publishes it with the app.','<strong>A thin edge layer.</strong> Cloudflare Workers fill only the gaps browser restrictions create, instead of standing up a traditional backend.','<strong>Keys stay home.</strong> Optional third-party API keys live in an encrypted vault on the user\'s device and unlock temporarily, never on a central server.']),
  ('What it means for clients',['The same approach keeps business systems fast and cheap to run: push work to the edge, cache aggressively, keep sensitive data on the device or in your own infrastructure, and automate the chores with scheduled jobs.'])],
 gel_arch(), [('Open the live app','https://cptnope.github.io/gods-eye-lite/'),('Read the source on GitHub','https://github.com/CptNope/gods-eye-lite'),('Read the build log',BASE+'/gods-eye-lite/')],
 "Case study: God's Eye Lite, a free browser-based 3D world map PWA built with React, CesiumJS and Cloudflare Workers by Jeremy Anderson Tech.",
 {"@context":"https://schema.org","@type":"SoftwareApplication","name":"God's Eye Lite","applicationCategory":"ReferenceApplication","operatingSystem":"Web, iOS, Android, Windows, macOS, Linux","url":"https://cptnope.github.io/gods-eye-lite/","codeRepository":"https://github.com/CptNope/gods-eye-lite","isAccessibleForFree":True,"author":{"@id":BASE+"/about/#jeremy"}})

case('work-cytoscape.html','/project/cytoscape-immune-defense/','Cytoscape: Immune Defense','Cytoscape: Immune Defense','An installable immunology arcade game, built like production software: a separate engine, automated tests and continuous deployment.',tag('ch1'),cytoscape('ccy',1200,560),
 [('Type','Open-source PWA game'),('Role','Design, engineering, CI/CD'),('Stack','React 19, TypeScript, HTML5 Canvas, Vite, Tailwind CSS, Vitest, Workbox'),('Platforms','iOS, Android, Windows, macOS, Linux'),('Status','Live')],
 [('The brief',['Teach the four pathogen classes through play, in a game that installs on any device and keeps working offline.']),
  ('What it does',['The player pilots a T-cell and fires antibodies at waves of pathogens. Destroyed enemies split into smaller threats, and each wave gets faster and denser.','<ul><li>Viruses, including faster Swift variants.</li><li>Bacteria, including durable Armored variants.</li><li>Parasites that track the player, and Stalkers that home in harder.</li><li>Fungi: large, slow and damage resistant.</li><li>Power-ups: Rapid Fire, Shield, Damage Boost and System Purge.</li></ul>','Keyboard controls on desktop and touch controls on mobile.']),
  ('Engineering decisions',['<strong>Engine separate from interface.</strong> Physics, collision, screen wrapping, rendering, entity behavior and score persistence live in dedicated engine modules, not in React components.','<strong>Tested core.</strong> Vitest covers the engine\'s behavior, so gameplay changes don\'t silently break it.','<strong>Ship on every merge.</strong> GitHub Actions builds and deploys to GitHub Pages automatically.','<strong>Updates users actually get.</strong> Version-based cache busting and an in-app prompt tell installed players when a new build is ready.']),
  ('What it means for clients',['Separating business logic from the interface, testing it, and deploying automatically is how internal tools stay reliable as they grow. The same discipline goes into every custom app we build.'])],
 cyto_arch(), [('Play the live game','https://cptnope.github.io/Cytoscape-Immune-Defense/'),('Read the source on GitHub','https://github.com/CptNope/Cytoscape-Immune-Defense')],
 'Case study: Cytoscape: Immune Defense, an installable immunology arcade PWA with a tested engine and CI/CD, by Jeremy Anderson Tech.',
 {"@context":"https://schema.org","@type":"VideoGame","name":"Cytoscape: Immune Defense","url":"https://cptnope.github.io/Cytoscape-Immune-Defense/","gamePlatform":"Web browser","applicationCategory":"Game","isAccessibleForFree":True,"author":{"@id":BASE+"/about/#jeremy"}})

case('work-chronos-canon.html','/project/chronos-canon/','Chronos & Canon','Chronos &amp; Canon','An interactive research app for comparing ancient texts, manuscripts, archaeology and traditions, with every relationship labeled by the strength of its evidence.',tag('ch1')+tag('ch2'),chronos('ccc',1200,560),
 [('Type','Open-source research PWA'),('Role','Information design, engineering'),('Stack','React 19, TypeScript, Vite, Workbox, i18n'),('Languages','English, Spanish, Portuguese interface'),('Status','Live, corpus growing')],
 [('The brief',['Make a dense scholarly field navigable without flattening it: show what the evidence says, how strong it is, and where it came from.']),
  ('What it does',['<ul><li>Compares passages side by side, such as Genesis and 1 Enoch, or biblical texts and Dead Sea Scroll manuscripts.</li><li>Draws an evidence graph of quotation, literary dependence, shared tradition and proposed links.</li><li>Separates estimated composition dates from surviving manuscript evidence on a dual timeline.</li><li>Maps texts to archaeological sites such as Qumran, Ugarit, Nineveh and Persepolis.</li><li>Offers four research modes, from Scholarly to Speculative, that control what is shown.</li></ul>']),
  ('Engineering decisions',['<strong>Data compiled, not served.</strong> Structured datasets compile into strongly typed application data, so the app needs almost no backend.','<strong>Honest labels.</strong> Every relationship carries a type and an evidence strength, and the interface never presents speculation as consensus.','<strong>Respect the sources.</strong> The interface is localized in three languages, while original-language material in Hebrew, Aramaic, Greek, Ge\'ez and transliterated Akkadian stays untranslated.','<strong>No stale installs.</strong> A custom update check keeps installed copies from running old builds.']),
  ('What it means for clients',['Knowledge-heavy organizations, from labs to law firms, need the same thing: structured data, clear provenance, and interfaces that show confidence levels. It is also the groundwork for trustworthy AI retrieval.'])],
 chron_arch(), [('Open the live app','https://cptnope.github.io/Chronos-and-Canon/'),('Read the source on GitHub','https://github.com/CptNope/Chronos-and-Canon'),('Read the build log',BASE+'/i-built-a-powerful-interactive-archive-for-ancient-texts/')],
 'Case study: Chronos & Canon, a digital humanities research PWA with an evidence graph, dual timeline and archaeology map, by Jeremy Anderson Tech.',
 {"@context":"https://schema.org","@type":"SoftwareApplication","name":"Chronos & Canon","applicationCategory":"EducationalApplication","url":"https://cptnope.github.io/Chronos-and-Canon/","codeRepository":"https://github.com/CptNope/Chronos-and-Canon","inLanguage":["en","es","pt"],"isAccessibleForFree":True,"author":{"@id":BASE+"/about/#jeremy"}})

# ---------------- ABOUT ----------------
about = page_head([('index.html','Home'),(None,'About')],'A founder-led engineering studio.','Jeremy Anderson Tech is based in Shrewsbury, Massachusetts. Every engagement is led and engineered by its founder.') + f'''
<section class="section" aria-labelledby="ab1"><div class="wrap founder">
  <div class="portrait graticule"><div class="ph">Founder portrait<br><span class="readout">To be photographed · see brand book §13</span></div><span class="readout" style="position:relative">Jeremy Anderson · Founder</span></div>
  <div class="prose">
    <h2 id="ab1" style="margin-top:0">Meet Jeremy</h2>
    <p class="lede" style="color:var(--fg)">Jeremy has worked with computers and the web since 1998. He builds web platforms, applied AI systems and security programs, and he believes the person who builds a system should be the one who answers for it.</p>
    <p>He co-founded AndersonGraf Technologies in San Antonio, an IT and web company that served local businesses for a decade. Since 2016 he has worked independently as a full-stack developer, building WordPress themes and plugins, custom applications and integrations. Since 2022 he has designed AI-assisted systems and tools for research labs and scientific communication.</p>
    <p>He holds W3C/WAI web accessibility professional certification, is studying computer science at Harvard, and attends OWASP security events such as BASC Boston.</p>
  </div>
</div></section>
<section class="section alt" aria-labelledby="ab2"><div class="wrap two-col">
  <div><h2 id="ab2">Timeline</h2></div>
  <ul class="timeline" style="margin-top:0">
    <li><span class="y">1998–2008</span><span>Co-owner and developer, AndersonGraf Technologies, San Antonio, TX</span></li>
    <li><span class="y">2016 →</span><span>Independent full-stack developer: WordPress, React, Node.js, PHP</span></li>
    <li><span class="y">2022</span><span>W3C/WAI web accessibility professional certification</span></li>
    <li><span class="y">2022 →</span><span>AI consultant and developer, SciStories</span></li>
    <li><span class="y">2023 →</span><span>Computer science studies, Harvard</span></li>
    <li><span class="y">2024 →</span><span>Consulting on restaurant operations software and digital game systems</span></li>
    <li><span class="y">2026</span><span>Jeremy Anderson Tech becomes a studio with four service channels</span></li>
  </ul>
</div></section>
<section class="section" aria-labelledby="ab3"><div class="wrap">
  <div class="section-head"><h2 id="ab3">What we believe</h2><p class="lede">Six commitments that shape every proposal and every line of code.</p></div>
  <div class="svc-grid">
    <section><h3>Measure first</h3><p>Audit, test and benchmark before recommending anything. Decisions follow evidence.</p></section>
    <section><h3>Plain language</h3><p>Every recommendation is explained so the person paying for it can repeat it to their team.</p></section>
    <section><h3>Secure by default</h3><p>Least privilege, current software and monitored logs are the starting point of every build.</p></section>
    <section><h3>Built for everyone</h3><p>Accessibility is a requirement, tested with keyboards and screen readers before launch.</p></section>
    <section><h3>Own the outcome</h3><p>If something we built breaks, we answer for it. Our misestimates are not billed.</p></section>
    <section><h3>Show the work</h3><p>Prices, process and code are public wherever possible.</p></section>
  </div>
</div></section>
<section class="section alt" aria-labelledby="ab4"><div class="wrap two-col">
  <div><h2 id="ab4">Why a studio, and why one engineer</h2></div>
  <div><p class="lede" style="color:var(--fg)">Today, JAT is Jeremy. That is deliberate: you get senior engineering on every layer and a single point of accountability, without agency overhead.</p><p class="muted">Active projects are limited so each one gets full attention. When a project needs a specialist, you will know who they are before they start.</p>
  <div class="stat-strip" style="margin-top:var(--s5)"><div><span class="readout">Based in</span><span class="v">Shrewsbury, MA</span></div><div><span class="readout">Meets in person</span><span class="v">Boston &amp; Worcester</span></div><div><span class="readout">No travel fee within</span><span class="v">50 miles</span></div></div></div>
</div></section>
{close_cta()}'''
person = {"@context":"https://schema.org","@type":"Person","@id":BASE+"/about/#jeremy","name":"Jeremy Anderson","jobTitle":"Founder","worksFor":{"@id":BASE+"/#org"},"homeLocation":{"@type":"Place","name":"Shrewsbury, Massachusetts"},"hasCredential":{"@type":"EducationalOccupationalCredential","name":"W3C/WAI Web Accessibility professional certification"},"sameAs":["https://github.com/CptNope","https://www.linkedin.com/in/jeremy-anderson-53963b222/"]}
open(OUT+'about.html','w').write(page('About · Jeremy Anderson Tech','Jeremy Anderson Tech is a founder-led engineering studio in Shrewsbury, Massachusetts. Meet founder Jeremy Anderson.','/about/','about',about,[person]))

# ---------------- APPROACH ----------------
approach = page_head([('index.html','Home'),(None,'Approach')],'How a project runs.','A short, documented process with prices, dates and responsibilities written down before work starts.') + f'''
<section class="section" aria-labelledby="ap1"><div class="wrap">
  <h2 id="ap1" class="vh">Process</h2>
  <ol class="steps">
    <li><h3>Discovery call</h3><span class="n">Day 0</span><p>Free, 30 to 60 minutes. Your goals, constraints, budget and what success looks like.</p><span class="readout dt">Free</span></li>
    <li><h3>Written proposal</h3><span class="n">3 business days</span><p>Scope, deliverables, price, timeline and exclusions in plain language.</p><span class="readout dt">You decide</span></li>
    <li><h3>Scope and deposit</h3><span class="n">Signed</span><p>A signed scope document and a 50% deposit on fixed-fee projects.</p><span class="readout dt">Start in 1–2 weeks</span></li>
    <li><h3>Design, build, harden</h3><span class="n">Build</span><p>Reviews as it takes shape, then security, accessibility and performance testing.</p><span class="readout dt">Per package timeline</span></li>
    <li><h3>Launch and run</h3><span class="n">Launch</span><p>Training, documentation, 30 to 90 days of support, then an optional care plan.</p><span class="readout dt">Balance due on delivery</span></li>
  </ol>
</div></section>
<section class="section alt" aria-labelledby="ap2"><div class="wrap">
  <div class="section-head"><h2 id="ap2">The quality bar every build meets</h2><p class="lede">These standards are part of the price, not extras.</p></div>
  <div class="svc-grid">
    <section><h3>Accessibility</h3><p class="outcome">WCAG 2.2 AA.</p><p>Keyboard and screen reader testing before launch, reduced-motion support, and contrast checked against real colors, not guesses.</p></section>
    <section><h3>Performance</h3><p class="outcome">Core Web Vitals in the green.</p><p>Image, caching and asset work tuned for mid-range phones, with a 90+ PageSpeed target on optimization work.</p></section>
    <section><h3>Security</h3><p class="outcome">Hardened from day one.</p><p>Least-privilege roles, current software, login protection and SSL on every build. Deeper hardening is available as a package.</p></section>
    <section><h3>Search and answers</h3><p class="outcome">Structured to be found.</p><p>Semantic HTML, on-page SEO and schema markup so search engines and AI assistants understand your business.</p></section>
  </div>
</div></section>
<section class="section" aria-labelledby="ap3"><div class="wrap two-col">
  <div><h2 id="ap3">What you get at handoff</h2></div>
  <ul class="check-list" style="--c:var(--ch4)">
    <li>{CHECK}<span>Admin access to everything: hosting, domain, code and accounts stay in your name.</span></li>
    <li>{CHECK}<span>Documentation written for the people who will use the system.</span></li>
    <li>{CHECK}<span>Training for your team on editing and day-to-day care.</span></li>
    <li>{CHECK}<span>A record of every security change made.</span></li>
    <li>{CHECK}<span>Included support for 30, 60 or 90 days depending on the package.</span></li>
  </ul>
</div></section>
<section class="section alt" aria-labelledby="ap4"><div class="wrap two-col">
  <div><h2 id="ap4">Terms in plain language</h2></div>
  <div class="prose">
    <p><strong>Deposits.</strong> 50% to start fixed-fee projects, the balance on delivery. Retainers bill monthly in advance. Hourly work bills every two weeks.</p>
    <p><strong>Changes.</strong> New requirements get a written change-order estimate before any work. If we misestimate our own work, you don't pay for it.</p>
    <p><strong>Care plans.</strong> Three-month minimum, then 30 days notice to cancel. Unused hours can move into a prepaid hour bank.</p>
    <p><a class="link-arrow" href="services.html#pricing">See all pricing{AR}</a></p>
  </div>
</div></section>
{close_cta()}'''
howto = {"@context":"https://schema.org","@type":"HowTo","name":"How a Jeremy Anderson Tech project runs","step":[{"@type":"HowToStep","name":n,"text":t} for n,t in [("Discovery call","Free 30 to 60 minute call about goals, constraints and budget."),("Written proposal","Scope, price, timeline and exclusions within three business days."),("Scope and deposit","Signed scope document and 50% deposit for fixed-fee projects."),("Design, build, harden","Build with reviews, then security, accessibility and performance testing."),("Launch and run","Training, documentation, 30 to 90 days support, optional care plan.")]]}
open(OUT+'approach.html','w').write(page('Approach · Jeremy Anderson Tech','How a Jeremy Anderson Tech project runs: free discovery call, written proposal in three business days, signed scope, build and hardening, launch and support.','/approach/','approach',approach,[howto]))

# ---------------- INSIGHTS ----------------
insights = page_head([('index.html','Home'),(None,'Insights')],'Field notes.','Build logs, security notes and what we learn shipping real systems. Posts currently live on the existing site and keep their addresses.') + f'''
<section class="section" aria-labelledby="in1"><div class="wrap">
  <h2 id="in1" class="vh">Latest posts</h2>
  <div class="tag-list" style="margin-bottom:var(--s6)" aria-label="Categories"><span class="ch-tag" style="--c:var(--ch2)"><i></i>AI Systems</span><span class="ch-tag" style="--c:var(--ch4)"><i></i>Infrastructure</span><span class="ch-tag" style="--c:var(--ch3)"><i></i>Cybersecurity</span></div>
  {posts_html(3)}
</div></section>
{close_cta()}'''
blog = {"@context":"https://schema.org","@type":"Blog","name":"Jeremy Anderson Tech Insights","url":BASE+"/blog/","blogPost":[{"@type":"BlogPosting","headline":t,"url":u,"datePublished":d,"author":{"@id":BASE+"/about/#jeremy"}} for t,u,c,d,dd,x in POSTS]}
open(OUT+'insights.html','w').write(page('Insights · Jeremy Anderson Tech','Build logs, security notes and engineering field notes from Jeremy Anderson Tech.','/blog/','insights',insights,[blog]))

# ---------------- CONTACT ----------------
ERR = '<span class="msg"><svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><circle cx="7" cy="7" r="6" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M7 4v3.5M7 9.6v.1" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg><span></span></span>'
contact = page_head([('index.html','Home'),(None,'Contact')],'Tell us what the system needs to do.','Send a few details and we will set up a free 30-minute discovery call. You will have a written proposal within three business days of that call.') + f'''
<section class="section" aria-labelledby="ct1"><div class="wrap aside-col">
  <noscript><p class="form-status">This form needs JavaScript. Email <strong>info@jeremyanderson.tech</strong> with what the system should do, and we'll reply with times for a call.</p></noscript>
  <form class="form" id="leadForm" method="post" novalidate aria-labelledby="ct1">
    <h2 id="ct1" class="vh">Project inquiry</h2>
    <div class="form-row">
      <div class="field"><label for="name">Name</label><input class="input" id="name" name="name" autocomplete="name" required maxlength="100" spellcheck="false">{ERR}</div>
      <div class="field"><label for="email">Work email</label><input class="input" id="email" name="email" type="email" autocomplete="email" required maxlength="254" placeholder="you@company.com" spellcheck="false" autocapitalize="off">{ERR}</div>
    </div>
    <div class="form-row">
      <div class="field"><label for="org">Company or organization <span class="muted">(optional)</span></label><input class="input" id="org" name="org" autocomplete="organization" maxlength="120"></div>
      <div class="field"><label for="site">Current website <span class="muted">(optional)</span></label><input class="input" id="site" name="site" inputmode="url" autocomplete="url" maxlength="200" placeholder="mycompany.com" spellcheck="false" autocapitalize="off">{ERR}</div>
    </div>
    <fieldset class="field"><legend>What do you need? <span class="muted">(choose any)</span></legend>
      <div class="choices">
        <label class="choice" style="--c:var(--ch1)"><input type="checkbox" name="channel" value="Platforms"><span><i></i>CH1 · A website or app</span></label>
        <label class="choice" style="--c:var(--ch2)"><input type="checkbox" name="channel" value="Intelligence"><span><i></i>CH2 · AI or automation</span></label>
        <label class="choice" style="--c:var(--ch3)"><input type="checkbox" name="channel" value="Security"><span><i></i>CH3 · Security help</span></label>
        <label class="choice" style="--c:var(--ch4)"><input type="checkbox" name="channel" value="Operations"><span><i></i>CH4 · Hosting, care or SEO</span></label>
      </div></fieldset>
    <div class="form-row">
      <div class="field"><label for="budget">Budget range</label><select class="input" id="budget" name="budget"><option value="">Not sure yet</option><option>Under $2,500</option><option>$2,500 – $7,500</option><option>$7,500 – $15,000</option><option>$15,000 or more</option><option>Monthly care plan</option></select></div>
      <div class="field"><label for="when">Ideal start</label><select class="input" id="when" name="when"><option value="">Flexible</option><option>As soon as possible</option><option>Within 1–2 months</option><option>Next quarter</option></select></div>
    </div>
    <div class="field"><label for="message">What should the system do?</label><textarea class="input" id="message" name="message" required maxlength="4000" aria-describedby="msgHint msgCount"></textarea><span class="hint field-hint"><span id="msgHint">A few sentences is plenty: the problem, who uses it, and any deadline.</span><span class="readout count" id="msgCount" aria-live="off">0 / 4,000</span></span>{ERR}</div>
    <div class="hp" aria-hidden="true"><label for="company_url">Leave this field empty</label><input id="company_url" name="company_url" tabindex="-1" autocomplete="off"></div>
    <div class="cta-row"><button class="btn btn-primary" type="submit">Request a discovery call {ARROW}</button><span class="readout">Proposal within 3 business days of the call</span></div>
    <div class="form-status" id="formStatus" role="status" tabindex="-1" hidden></div>
  </form>
  <section class="sticky-card" aria-label="What happens next">
    <h2 class="readout" style="color:var(--fg)">What happens next</h2>
    <ol class="steps" style="grid-template-columns:1fr;border-top:0;border-left:1px solid var(--line);margin-left:4px">
      <li style="border-right:0;padding:0 0 var(--s4) var(--s5)"><h3 style="font-size:1rem">We reply</h3><span class="n">First</span><p>With times for a 30-minute call.</p></li>
      <li style="border-right:0;padding:0 0 var(--s4) var(--s5)"><h3 style="font-size:1rem">Discovery</h3><span class="n">Call</span><p>Video, phone or in person in Greater Boston and Worcester.</p></li>
      <li style="border-right:0;padding:0 0 0 var(--s5)"><h3 style="font-size:1rem">Proposal</h3><span class="n">3 business days</span><p>Written scope, price and timeline.</p></li>
    </ol>
    <div class="copy-line"><span class="mono" style="font-size:.875rem">info@jeremyanderson.tech</span><button class="btn-copy" type="button" data-copy-text="info@jeremyanderson.tech">Copy</button></div>
    <p class="readout" style="margin:0">Shrewsbury, MA · Serving Massachusetts and remote clients</p>
  </section>
</div></section>'''
cp = {"@context":"https://schema.org","@type":"ContactPage","name":"Contact Jeremy Anderson Tech","url":BASE+"/contact/","about":{"@id":BASE+"/#org"}}
open(OUT+'contact.html','w').write(page('Contact · Jeremy Anderson Tech','Book a free 30-minute discovery call with Jeremy Anderson Tech. Written proposal within three business days.','/contact/','contact',contact,[cp]))

# ---------------- SITE MAP / IA ----------------
IA = [
 ('/','Home','index.html','Persuade','WebSite, ProfessionalService'),
 ('/services/','Services & pricing','services.html','Persuade','FAQPage'),
 ('/services/platforms/','CH1 Platforms','platforms.html','Persuade','Service, OfferCatalog'),
 ('/services/platforms/web-design/','Web design (phase 2 page)','platforms.html#web-design','Persuade','Service'),
 ('/services/platforms/wordpress/','WordPress engineering (phase 2)','platforms.html#wordpress','Persuade','Service'),
 ('/services/platforms/custom-software/','Custom software & PWAs (phase 2)','platforms.html#custom-software','Persuade','Service'),
 ('/services/intelligence/','CH2 Intelligence','intelligence.html','Persuade','Service, OfferCatalog'),
 ('/services/intelligence/automation/','Integrations & automation (phase 2)','intelligence.html#automation','Persuade','Service'),
 ('/services/security/','CH3 Security','security.html','Persuade','Service, OfferCatalog'),
 ('/services/operations/','CH4 Operations','operations.html','Persuade','Service, OfferCatalog'),
 ('/services/operations/hosting/','Hosting & infrastructure (phase 2)','operations.html#hosting','Persuade','Service'),
 ('/services/operations/seo/','SEO / AEO / GEO (phase 2)','operations.html#visibility','Persuade','Service'),
 ('/work/','Work','work.html','Experience','CollectionPage'),
 ('/project/gods-eye-lite/',"God's Eye Lite",'work-gods-eye-lite.html','Read','SoftwareApplication'),
 ('/project/cytoscape-immune-defense/','Cytoscape','work-cytoscape.html','Read','VideoGame'),
 ('/project/chronos-canon/','Chronos & Canon','work-chronos-canon.html','Read','SoftwareApplication'),
 ('/approach/','Approach','approach.html','Read','HowTo'),
 ('/about/','About','about.html','Read','Person'),
 ('/blog/','Insights','insights.html','Read','Blog'),
 ('/contact/','Contact','contact.html','Operate','ContactPage'),
 ('/lab/','Signal Lab (optional showcase, phase 3)','—','Experience','WebApplication'),
]
REDIR = [
 ('/projects/','/work/','301'),('/project/{slug}/','unchanged','keep'),('/services/','unchanged','keep'),
 ('/service/business-site/','/services/platforms/#packages','301'),('/service/custom-platform/','/services/platforms/#packages','301'),('/service/custom-ai-tool/','/services/intelligence/#packages','301'),
 ('/service-category/wordpress-packages/','/services/platforms/','301'),('/service-category/ai-packages/','/services/intelligence/','301'),
 ('/project-tag/{tag}/','/work/','301 (noindex tag archives)'),('/{post-slug}/','unchanged','keep'),('/category/{cat}/','/blog/?category={cat}','keep or 301'),
 ('/about','unchanged','keep'),('/privacy-policy/ · /terms-of-service/ · /accessibility/','unchanged','keep'),
]
BLOCKS = [('jat/hero-scope','Hero with canvas trace, headline, CTAs, channel strip'),('jat/channel-strip','Four-channel readout navigation'),('jat/channel-rows','Channel rows with promise, services, entry price'),('jat/versus','Two graticule screens comparing states'),('jat/price-table','Price rows from a Packages custom post type'),('jat/steps','Numbered process with time readouts'),('jat/work-feature','Featured project with schematic plate and facts'),('jat/case-facts','Sticky facts sidebar for case studies'),('jat/faq','Accessible details list emitting FAQPage schema'),('jat/close-cta','Closing call to action with copyable email'),('jat/lead-form','Inquiry form posting to a REST endpoint with nonce and honeypot')]
ia_rows=''.join(f'<tr><td class="mono">{p}</td><td>{t}</td><td>{"<a href=\""+h+"\">"+h+"</a>" if h!="—" else "—"}</td><td>{m}</td><td class="mono">{s}</td></tr>' for p,t,h,m,s in IA)
rd_rows=''.join(f'<tr><td class="mono">{a}</td><td class="mono">{b}</td><td>{c}</td></tr>' for a,b,c in REDIR)
bl_rows=''.join(f'<tr><td class="mono">{a}</td><td>{b}</td></tr>' for a,b in BLOCKS)
sitemap = page_head([('index.html','Home'),(None,'Site map')],'Site map and architecture.','Every page in the new information architecture, its production URL, the schema it emits, and how existing URLs keep their search equity.') + f'''
<section class="section" aria-labelledby="sm1"><div class="wrap">
  <div class="section-head"><h2 id="sm1">Information architecture</h2><p class="lede">Concept pages link to their production URLs. Phase 2 splits channel sections into individual service landing pages.</p></div>
  <div style="overflow-x:auto" tabindex="0" role="region" aria-label="Information architecture table"><table class="spec-table" style="width:100%;border-collapse:collapse;min-width:760px;font-size:.9375rem"><thead><tr><th>Production URL</th><th>Page</th><th>Concept file</th><th>Mode</th><th>Schema</th></tr></thead><tbody>{ia_rows}</tbody></table></div>
</div></section>
<section class="section alt" aria-labelledby="sm2"><div class="wrap">
  <div class="section-head"><h2 id="sm2">Redirect map</h2><p class="lede">Existing projects and blog posts keep their URLs. Only consolidated service pages and thin tag archives move.</p></div>
  <div style="overflow-x:auto" tabindex="0" role="region" aria-label="Redirect map table"><table class="spec-table" style="width:100%;border-collapse:collapse;min-width:640px;font-size:.9375rem"><thead><tr><th>Current URL</th><th>New URL</th><th>Action</th></tr></thead><tbody>{rd_rows}</tbody></table></div>
</div></section>
<section class="section" aria-labelledby="sm3"><div class="wrap two-col">
  <div><h2 id="sm3">WordPress block plan</h2><p class="lede" style="margin-top:var(--s4)">Custom blocks in a <code class="mono">jat</code> namespace, styled from theme.json tokens, so the team edits content without touching layout.</p></div>
  <div style="overflow-x:auto" tabindex="0" role="region" aria-label="WordPress block plan table"><table class="spec-table" style="width:100%;border-collapse:collapse;font-size:.9375rem"><thead><tr><th>Block</th><th>Purpose</th></tr></thead><tbody>{bl_rows}</tbody></table></div>
</div></section>
<style>.spec-table th{{text-align:left;font-family:var(--f-mono);font-stretch:90%;font-weight:500;font-size:.6875rem;letter-spacing:.06em;text-transform:uppercase;color:var(--fg-subtle);padding:10px 14px 10px 0;border-bottom:1px solid var(--line)}}.spec-table td{{padding:12px 14px 12px 0;border-bottom:1px solid var(--line);vertical-align:top}}.spec-table td.mono{{font-size:.8125rem}}</style>
{close_cta()}'''
open(OUT+'sitemap.html','w').write(page('Site Map · Jeremy Anderson Tech','Information architecture, redirect map and WordPress block plan for the Jeremy Anderson Tech website.','/sitemap/','',sitemap))
print('ok2')
