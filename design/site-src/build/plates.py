import math
GRID = '<defs><pattern id="{id}m" width="16" height="16" patternUnits="userSpaceOnUse"><path d="M16 0V16H0" fill="none" stroke="rgba(159,180,200,.05)"/></pattern><pattern id="{id}M" width="80" height="80" patternUnits="userSpaceOnUse"><rect width="80" height="80" fill="url(#{id}m)"/><path d="M80 0V80H0" fill="none" stroke="rgba(159,180,200,.1)"/></pattern></defs><rect width="100%" height="100%" fill="url(#{id}M)"/>'

def gods_eye(id='ge', w=800, h=450, labels=True, fit='slice'):
    g = GRID.format(id=id)
    # equirectangular graticule: lat/long lines
    lines = []
    for i in range(1,12):
        x = w*i/12; lines.append(f'<path d="M{x:.1f} 0V{h}" stroke="rgba(94,240,160,.10)"/>')
    for j in range(1,6):
        y = h*j/6; lines.append(f'<path d="M0 {y:.1f}H{w}" stroke="rgba(94,240,160,.10)"/>')
    # satellite ground tracks (sinusoids)
    tracks=[]
    cols=['#3CE3F0','#9A8CFF','#E6ECF2']
    for k,(inc,ph,col) in enumerate([(0.36,0.0,cols[0]),(0.30,1.9,cols[1]),(0.42,3.6,cols[2])]):
        d=''
        for xi in range(0,w+1,6):
            y=h/2 - inc*h*math.sin(2*math.pi*xi/w*1.0+ph)
            d+=('L' if xi else 'M')+f'{xi} {y:.1f}'
        op = .95 if k==0 else .55
        tracks.append(f'<path d="{d}" fill="none" stroke="{col}" stroke-width="{2 if k==0 else 1.4}" opacity="{op}" stroke-dasharray="{"" if k==0 else "2 5"}"/>')
    # satellite position on main track
    sx=w*0.62; sy=h/2-0.36*h*math.sin(2*math.pi*0.62)
    sat=f'<circle cx="{sx:.1f}" cy="{sy:.1f}" r="5" fill="#3CE3F0"/><circle cx="{sx:.1f}" cy="{sy:.1f}" r="11" fill="none" stroke="#3CE3F0" stroke-opacity=".5"/>'
    quakes=''.join(f'<circle cx="{x}" cy="{y}" r="{r}" fill="none" stroke="#FFC23D" stroke-width="1.4"/><circle cx="{x}" cy="{y}" r="2" fill="#FFC23D"/>' for x,y,r in [(140,150,9),(655,262,6),(560,120,12),(720,330,7)])
    cams=''.join(f'<rect x="{x-2.5}" y="{y-2.5}" width="5" height="5" fill="#5EF0A0"/>' for x,y in [(220,160),(232,170),(214,176),(410,135),(422,128),(398,142),(600,175),(612,182),(305,300)])
    lab=''
    if labels:
        lab=f'''<g class="plate-lbl" font-family="Martian Mono, monospace" font-size="11" fill="#A3AFBC">
<rect x="16" y="{h-70}" width="236" height="56" rx="4" fill="rgba(7,9,11,.85)" stroke="#222A34"/>
<circle cx="30" cy="{h-52}" r="3.5" fill="#3CE3F0"/><text x="42" y="{h-48}">ORBIT · IN-BROWSER</text>
<circle cx="30" cy="{h-34}" r="3.5" fill="#FFC23D"/><text x="42" y="{h-30}">QUAKES</text>
<rect x="132" y="{h-37.5}" width="6" height="6" fill="#5EF0A0"/><text x="146" y="{h-30}">PUBLIC CAMS</text>
<text x="{w-16}" y="26" text-anchor="end" fill="#7D8996">SCHEMATIC · EQUIRECTANGULAR</text></g>'''
    return f'<svg viewBox="0 0 {w} {h}" preserveAspectRatio="xMidYMid {fit}" role="img" aria-label="Schematic of God\'s Eye Lite: satellite ground tracks drawn as sine waves over a world grid, with earthquake markers and public traffic camera locations."><rect width="100%" height="100%" fill="#07090B"/>{g}{"".join(lines)}{cams}{quakes}{"".join(tracks)}{sat}{lab}</svg>'

def cytoscape(id='cy', w=800, h=450, labels=True):
    g = GRID.format(id=id)
    def poly(cx,cy,r,n,rot=0,jag=0.0):
        pts=[]
        for i in range(n):
            a=rot+2*math.pi*i/n; rr=r*(1+(jag if i%2 else -jag))
            pts.append(f'{cx+rr*math.cos(a):.1f},{cy+rr*math.sin(a):.1f}')
        return ' '.join(pts)
    ship=f'<g transform="translate(390 250) rotate(28)"><polygon points="0,-22 15,16 0,8 -15,16" fill="none" stroke="#E6ECF2" stroke-width="2" stroke-linejoin="round"/></g>'
    shots=''.join(f'<path d="M{x} {y}l{dx} {dy}" stroke="#3CE3F0" stroke-width="2.4" stroke-linecap="round"/>' for x,y,dx,dy in [(404,222,6,-11),(420,192,6,-11),(437,161,6,-11)])
    virus=f'<polygon points="{poly(520,110,26,10,0,.28)}" fill="none" stroke="#9A8CFF" stroke-width="1.8"/><circle cx="520" cy="110" r="7" fill="none" stroke="#9A8CFF"/>'
    bact=f'<rect x="150" y="96" width="90" height="38" rx="19" fill="none" stroke="#5EF0A0" stroke-width="1.8" transform="rotate(18 195 115)"/><rect x="164" y="106" width="62" height="18" rx="9" fill="none" stroke="#5EF0A0" stroke-opacity=".5" transform="rotate(18 195 115)"/>'
    para=f'<path d="M600 320c20-26 50-26 70 0s50 26 70 0" fill="none" stroke="#FFC23D" stroke-width="1.8"/><circle cx="600" cy="320" r="5" fill="none" stroke="#FFC23D" stroke-width="1.6"/>'
    fung=f'<polygon points="{poly(170,330,44,7,.3,.12)}" fill="none" stroke="#FF5C7A" stroke-width="1.8"/><polygon points="{poly(170,330,26,7,.7,.1)}" fill="none" stroke="#FF5C7A" stroke-opacity=".5" stroke-width="1.4"/>'
    frags=''.join(f'<polygon points="{poly(x,y,r,6,a,.3)}" fill="none" stroke="#9A8CFF" stroke-width="1.4" opacity=".75"/>' for x,y,r,a in [(470,170,9,.4),(560,160,7,1.1)])
    hud=''
    if labels:
        hud=f'''<g class="plate-lbl" font-family="Martian Mono, monospace" font-size="11" fill="#A3AFBC"><text x="20" y="28">WAVE 07</text><text x="{w-20}" y="28" text-anchor="end">SCORE 18,420</text>
<g transform="translate(20 {h-28})"><rect width="120" height="6" fill="#222A34"/><rect width="84" height="6" fill="#5EF0A0"/></g><text x="148" y="{h-21}">SHIELD</text>
<text x="{w-16}" y="{h-20}" text-anchor="end" fill="#7D8996">SCHEMATIC · SAMPLE STATE</text></g>'''
    return f'<svg viewBox="0 0 {w} {h}" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Schematic of Cytoscape: Immune Defense, a vector arcade game: a T-cell ship firing antibodies at a virus, bacteria, a parasite and a fungus."><rect width="100%" height="100%" fill="#07090B"/>{g}{virus}{frags}{bact}{para}{fung}{shots}{ship}{hud}</svg>'

def chronos(id='cc', w=800, h=450, labels=True):
    g = GRID.format(id=id)
    nodes={'gen6':(250,120,'Genesis 6:1–4','#3CE3F0'),'enoch':(470,90,'1 Enoch','#9A8CFF'),'dss':(640,170,'Dead Sea Scrolls','#5EF0A0'),
           'genflood':(200,250,'Genesis flood','#3CE3F0'),'atra':(400,230,'Atrahasis','#FFC23D'),'gilg':(560,280,'Gilgamesh XI','#FFC23D'),'ber':(700,300,'Berossus','#E6ECF2')}
    edges=[('gen6','enoch','solid'),('enoch','dss','solid'),('genflood','atra','dash'),('atra','gilg','solid'),('gilg','ber','dash'),('genflood','gilg','dot')]
    e=''
    for a,b,s in edges:
        x1,y1=nodes[a][:2]; x2,y2=nodes[b][:2]
        da={'solid':'','dash':'6 5','dot':'1.5 5'}[s]
        e+=f'<path d="M{x1} {y1}L{x2} {y2}" stroke="#5A6675" stroke-width="1.5" stroke-dasharray="{da}" stroke-linecap="round"/>'
    n=''
    for k,(x,y,t,c) in nodes.items():
        n+=f'<circle cx="{x}" cy="{y}" r="7" fill="#07090B" stroke="{c}" stroke-width="2"/><text x="{x}" y="{y-14}" text-anchor="middle" font-family="Martian Mono, monospace" font-size="11" fill="#E6ECF2">{t}</text>'
    # dual timeline
    tl=f'''<g transform="translate(40 {h-96})" font-family="Martian Mono, monospace" font-size="10" fill="#7D8996">
<path d="M0 0H{w-80}" stroke="#2E3845"/>
<text x="0" y="-8">COMPOSITION (EST.)</text><rect x="120" y="10" width="220" height="8" rx="1" fill="#3CE3F0" opacity=".7"/><rect x="300" y="24" width="160" height="8" rx="1" fill="#9A8CFF" opacity=".7"/>
<text x="0" y="56">MANUSCRIPT EVIDENCE</text><rect x="440" y="64" width="90" height="8" rx="1" fill="#5EF0A0" opacity=".8"/><rect x="560" y="64" width="40" height="8" rx="1" fill="#5EF0A0" opacity=".5"/></g>'''
    leg=''
    if labels:
        leg=f'''<g class="plate-lbl" font-family="Martian Mono, monospace" font-size="10" fill="#A3AFBC" transform="translate({w-250} 26)"><path d="M0 0H24" stroke="#5A6675" stroke-width="1.5"/><text x="32" y="4">DEPENDENCE</text><path d="M110 0H134" stroke="#5A6675" stroke-width="1.5" stroke-dasharray="6 5"/><text x="142" y="4">SHARED</text><path d="M0 18H24" stroke="#5A6675" stroke-width="1.5" stroke-dasharray="1.5 5" stroke-linecap="round"/><text x="32" y="22">PROPOSED</text></g>
<text class="plate-lbl" x="20" y="28" font-family="Martian Mono, monospace" font-size="11" fill="#7D8996">SCHEMATIC · EVIDENCE GRAPH</text>'''
    return f'<svg viewBox="0 0 {w} {h}" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Schematic of Chronos and Canon: an evidence graph linking Genesis 6, 1 Enoch and the Dead Sea Scrolls, and flood narratives from Genesis, Atrahasis, Gilgamesh and Berossus, above a dual timeline separating composition dates from manuscript evidence."><rect width="100%" height="100%" fill="#07090B"/>{g}{e}{n}{tl}{leg}</svg>'

def gel_arch():
    return '''<svg class="diagram" viewBox="0 0 960 300" role="img" aria-labelledby="gelT"><title id="gelT">God's Eye Lite architecture: public APIs and agency feeds flow to the browser app. GitHub Actions builds the camera catalog as static data. A Cloudflare Worker fills gaps browsers cannot reach directly. Optional API keys stay in an encrypted vault on the device, and a service worker caches the app and saved map areas.</title>
<g font-family="Martian Mono, monospace" font-size="11" fill="#A3AFBC">
<g fill="#0A0C0F" stroke-width="1.5"><rect x="30" y="40" width="222" height="64" rx="6" stroke="#5EF0A0"/><rect x="30" y="196" width="222" height="64" rx="6" stroke="#5EF0A0"/><rect x="385" y="118" width="190" height="64" rx="6" stroke="#3CE3F0"/><rect x="716" y="40" width="214" height="64" rx="6" stroke="#FFC23D"/><rect x="716" y="196" width="214" height="64" rx="6" stroke="#9A8CFF"/></g>
<text x="46" y="66" fill="#5EF0A0">PUBLIC APIS + FEEDS</text><text x="46" y="88">orbits, quakes, weather</text>
<text x="46" y="222" fill="#5EF0A0">GITHUB ACTIONS</text><text x="46" y="244">camera catalog → JSON</text>
<text x="401" y="144" fill="#3CE3F0">BROWSER APP</text><text x="401" y="166">React · CesiumJS · PWA</text>
<text x="732" y="66" fill="#FFC23D">LOCAL KEY VAULT</text><text x="732" y="88">encrypted on device</text>
<text x="732" y="222" fill="#9A8CFF">SERVICE WORKER</text><text x="732" y="244">offline map areas</text>
<g fill="none" stroke-width="1.5"><path d="M252 72H300Q320 72 320 92V130Q320 150 340 150H385" stroke="#5EF0A0"/><path d="M252 228H300Q320 228 320 208V170Q320 150 340 150" stroke="#5EF0A0"/><path d="M575 150H640Q660 150 660 130V92Q660 72 680 72H716" stroke="#FFC23D"/><path d="M660 170V208Q660 228 680 228H716" stroke="#9A8CFF"/><path d="M480 182V250H300" stroke="#3D4855" stroke-dasharray="3 4"/></g>
<text x="320" y="276" fill="#7D8996">CLOUDFLARE WORKER fills blocked feeds</text><g fill="#3CE3F0"><circle cx="385" cy="150" r="3"/><circle cx="575" cy="150" r="3"/></g></g></svg>'''

def cyto_arch():
    return '''<svg class="diagram" viewBox="0 0 960 260" role="img" aria-labelledby="cyT"><title id="cyT">Cytoscape architecture: a React 19 interface sits on top of a separate game engine with physics, collision, rendering and entity modules; Vitest covers the engine; GitHub Actions deploys to GitHub Pages; a service worker enables offline play.</title>
<g font-family="Martian Mono, monospace" font-size="11" fill="#A3AFBC">
<rect x="30" y="30" width="250" height="200" rx="6" fill="#0A0C0F" stroke="#3CE3F0" stroke-width="1.5"/><text x="46" y="56" fill="#3CE3F0">REACT 19 UI</text><text x="46" y="80">menus, HUD, touch controls</text><text x="46" y="102">update prompt</text>
<rect x="355" y="30" width="250" height="200" rx="6" fill="#0A0C0F" stroke="#9A8CFF" stroke-width="1.5"/><text x="371" y="56" fill="#9A8CFF">ENGINE MODULES</text><text x="371" y="84">physics · collision</text><text x="371" y="106">screen wrap · render</text><text x="371" y="128">entity behavior</text><text x="371" y="150">score persistence</text><text x="371" y="200" fill="#5EF0A0">TESTED · VITEST</text>
<rect x="680" y="30" width="250" height="90" rx="6" fill="#0A0C0F" stroke="#5EF0A0" stroke-width="1.5"/><text x="696" y="56" fill="#5EF0A0">CI/CD</text><text x="696" y="80">GitHub Actions → Pages</text>
<rect x="680" y="140" width="250" height="90" rx="6" fill="#0A0C0F" stroke="#FFC23D" stroke-width="1.5"/><text x="696" y="166" fill="#FFC23D">SERVICE WORKER</text><text x="696" y="190">offline play · cache bust</text>
<g stroke-width="1.5" fill="none"><path d="M280 130H355" stroke="#3CE3F0"/><path d="M605 80H680" stroke="#5EF0A0"/><path d="M605 180H680" stroke="#FFC23D"/></g>
<text x="292" y="122" fill="#7D8996">state</text></g></svg>'''

def chron_arch():
    return '''<svg class="diagram" viewBox="0 0 960 240" role="img" aria-labelledby="chT"><title id="chT">Chronos and Canon architecture: structured research datasets compile into typed application data; the client-side React app renders four views (texts, evidence graph, timeline, map); the interface is localized in three languages while original-language sources stay untranslated; a service worker and update check keep installed copies current.</title>
<g font-family="Martian Mono, monospace" font-size="11" fill="#A3AFBC">
<rect x="30" y="40" width="220" height="160" rx="6" fill="#0A0C0F" stroke="#5EF0A0" stroke-width="1.5"/><text x="46" y="66" fill="#5EF0A0">DATASETS</text><text x="46" y="92">texts · manuscripts</text><text x="46" y="114">sites · relationships</text><text x="46" y="136">evidence strength</text>
<rect x="320" y="40" width="300" height="160" rx="6" fill="#0A0C0F" stroke="#3CE3F0" stroke-width="1.5"/><text x="336" y="66" fill="#3CE3F0">TYPED CLIENT APP</text><text x="336" y="92">side-by-side texts</text><text x="336" y="114">evidence graph</text><text x="336" y="136">dual timeline · map</text><text x="336" y="170">4 research modes</text>
<rect x="690" y="40" width="240" height="70" rx="6" fill="#0A0C0F" stroke="#9A8CFF" stroke-width="1.5"/><text x="706" y="66" fill="#9A8CFF">I18N</text><text x="706" y="90">EN · ES · PT interface</text>
<rect x="690" y="130" width="240" height="70" rx="6" fill="#0A0C0F" stroke="#FFC23D" stroke-width="1.5"/><text x="706" y="156" fill="#FFC23D">PWA</text><text x="706" y="180">offline · update check</text>
<g stroke-width="1.5" fill="none"><path d="M250 120H320" stroke="#5EF0A0"/><path d="M620 75H690" stroke="#9A8CFF"/><path d="M620 165H690" stroke="#FFC23D"/></g><text x="258" y="112" fill="#7D8996">compile</text></g></svg>'''
