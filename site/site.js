(()=>{
'use strict';
const mq = q => (window.matchMedia ? matchMedia(q) : {matches:false});
const onMq = (m, fn) => { if (!m) return; if (m.addEventListener) m.addEventListener('change', fn); else if (m.addListener) m.addListener(fn); };
const reduce = mq('(prefers-reduced-motion: reduce)').matches;
/* Run fn once when el scrolls into view; run immediately where IntersectionObserver is missing. */
const whenVisible = (el, fn, threshold=.2) => {
  if (!('IntersectionObserver' in window)) { fn(); return; }
  const io = new IntersectionObserver((es)=>{ if (es.some(e=>e.isIntersecting)) { io.disconnect(); fn(); } }, {threshold});
  io.observe(el);
};
/* Isolate each feature so one failure never disables navigation or the form. */
const safe = (name, fn) => { try { fn(); } catch (err) { if (window.console) console.warn('[jat] ' + name + ' disabled:', err); } };
const root = getComputedStyle(document.documentElement);
const tok = n => root.getPropertyValue(n).trim();

/* ---------- Mobile navigation ---------- */
safe('navigation', () => {
  const nav = document.querySelector('.nav'), mb = document.querySelector('.menu-btn');
  if (!nav || !mb) return;
  const links = () => [...nav.querySelectorAll('a[href]'), mb];
  const isOpen = () => nav.classList.contains('open');
  const set = (open, returnFocus) => {
    nav.classList.toggle('open', open);
    mb.setAttribute('aria-expanded', String(open));
    mb.querySelector('.lbl').textContent = open ? 'Close' : 'Menu';
    document.body.style.overflow = open ? 'hidden' : '';
    if (open) { const first = nav.querySelector('a[href]'); if (first) first.focus(); }
    else if (returnFocus) mb.focus();
  };
  mb.addEventListener('click', () => set(!isOpen(), true));
  nav.addEventListener('click', e => { if (isOpen() && e.target.closest('a[href]')) set(false, false); });
  document.addEventListener('keydown', e => {
    if (!isOpen()) return;
    if (e.key === 'Escape') { e.preventDefault(); set(false, true); return; }
    if (e.key === 'Tab') {           /* keep focus inside the open menu */
      const f = links().filter(el => el.offsetParent !== null); if (!f.length) return;
      const first = f[0], last = f[f.length-1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  onMq(mq('(min-width:880px)'), e => { if (e.matches && isOpen()) set(false, false); });
});

/* ---------- Trace engine: noise → trigger → lock → burn-in ---------- */
const TRACE_D = "M9 50 Q9 64 23 64 Q33 64 36 57 L52 16 L68 64 H84 V16";
const NS = 'http://www.w3.org/2000/svg';
let MARK = null;
function markPoints(){
  if (MARK) return MARK;
  const s = document.createElementNS(NS,'svg'); s.setAttribute('width','0'); s.setAttribute('height','0'); s.style.position='absolute';
  const p = document.createElementNS(NS,'path'); p.setAttribute('d',TRACE_D); s.appendChild(p); document.body.appendChild(s);
  const L = p.getTotalLength(), n = 160, out=[];
  for (let i=0;i<=n;i++){ const q=p.getPointAtLength(L*i/n); out.push([q.x,q.y]); }
  s.remove(); return (MARK = out);
}
function axes(ctx,W,H,full){
  if(W<760&&full) return;
  ctx.save(); ctx.strokeStyle='rgba(159,180,200,.22)'; ctx.lineWidth=1; ctx.beginPath();
  const cy=Math.round(H/2)+.5, cx=Math.round(W/2)+.5;
  ctx.moveTo(0,cy); ctx.lineTo(W,cy); if(full){ ctx.moveTo(cx,0); ctx.lineTo(cx,H); }
  const dx=W/50; for(let i=1;i<50;i++){ const x=Math.round(i*dx)+.5, h=i%5?3:6; ctx.moveTo(x,cy-h); ctx.lineTo(x,cy+h); }
  if(full){ const dy=H/40; for(let j=1;j<40;j++){ const y=Math.round(j*dy)+.5, w=j%5?3:6; ctx.moveTo(cx-w,y); ctx.lineTo(cx+w,y); } }
  ctx.stroke(); ctx.restore();
}
function Scope(canvas, opt){
  const ctx = canvas.getContext && canvas.getContext('2d');
  if (!ctx) return {replay(){}};
  const o = Object.assign({markH:.36, markX:.7, markY:.45, sweeps:3, sweepMs:760, cursors:false, thin:1.6, color:tok('--ch1')||'#3CE3F0', mobile:null, place:null, onState:null, mark:true, startX:0}, opt);
  let sx=0,W=0,H=0,poly=[],mr=[0,0],bar=null,scale=1,done=false,raf=0,started=false,ptr=null;
  const seeds = Array.from({length:6},()=>[Math.random()*6.28, Math.random()*6.28]);
  function layout(){
    const r = canvas.getBoundingClientRect(); if (r.width<2||r.height<2){ poly=[]; return; }
    const dpr = Math.min(2, devicePixelRatio||1); W=r.width; H=r.height; canvas.width=W*dpr; canvas.height=H*dpr; ctx.setTransform(dpr,0,0,dpr,0,0);
    /* place(W,H) returns the mark centre and px-per-unit for the current layout mode. */
    let m, ox, oy;
    if (o.place){ const pl = o.place(W,H); m = {startX: pl.startX||0}; scale = pl.unit; ox = pl.cx - 54.5*scale; oy = pl.cy - 40*scale; }
    else { m = (o.mobile && W<760) ? Object.assign({},o,o.mobile) : o; scale = H*m.markH/56; ox = W*m.markX - 54.5*scale; oy = H*m.markY - 40*scale; }
    const mp = markPoints().map(([x,y])=>[ox+x*scale, oy+y*scale]);
    const yIn = oy+50*scale, yOut = oy+16*scale; poly=[];
    sx = Math.min(W*m.startX, mp[0][0]-60); for (let x=sx; x<mp[0][0]; x+=6) poly.push([x,yIn]);
    mr=[poly.length, poly.length+mp.length-1]; poly.push(...mp);
    for (let x=mp[mp.length-1][0]+6; x<=W+6; x+=6) poly.push([x,yOut]);
    bar=[[ox+70*scale,yOut],[ox+100*scale,yOut]];
  }
  const noisy=(k,amp,shift)=>{ const sd=seeds[k%seeds.length]; return poly.map(([x,y])=>[x+shift, y+amp*(Math.sin(x*.021+sd[0])*.6+Math.sin(x*.0047+sd[1])*.4+(Math.random()-.5)*.55)]); };
  function stroke(pts,a,b,w,al){ if(!pts.length||b<=a) return; b=Math.min(b,pts.length-1); ctx.globalAlpha=al; ctx.lineWidth=w; ctx.strokeStyle= w<=o.thin+.01 ? fadeStyle() : o.color; ctx.lineJoin='round'; ctx.lineCap='round'; ctx.beginPath(); ctx.moveTo(pts[a][0],pts[a][1]); for(let i=a+1;i<=b;i++) ctx.lineTo(pts[i][0],pts[i][1]); ctx.stroke(); ctx.globalAlpha=1; }
  function fadeStyle(){ if(!(sx>0)) return o.color; const g=ctx.createLinearGradient(sx,0,sx+110,0); const h=o.color.replace('#',''); const rgb=[0,2,4].map(i=>parseInt(h.substr(i,2),16)).join(','); g.addColorStop(0,`rgba(${rgb},0)`); g.addColorStop(1,`rgba(${rgb},1)`); return g; }
  function locked(burn){
    ctx.clearRect(0,0,W,H); if(o.axes) axes(ctx,W,H,true); if(!poly.length) return;
    stroke(poly,0,poly.length-1,o.thin,1);
    if (o.mark){ const w=o.thin+(8*scale-o.thin)*burn; stroke(poly,mr[0],mr[1],w,1);
      if (burn>0){ ctx.globalAlpha=burn; ctx.lineWidth=w; ctx.strokeStyle=o.color; ctx.lineCap='round'; ctx.beginPath(); ctx.moveTo(bar[0][0],bar[0][1]); ctx.lineTo(bar[1][0],bar[1][1]); ctx.stroke(); ctx.globalAlpha=1; } }
    if (ptr && o.cursors) cursors();
  }
  function cursors(){
    const [x,y]=ptr; ctx.save(); ctx.setLineDash([3,5]); ctx.lineWidth=1; ctx.strokeStyle=tok('--ink-300')||'#A3AFBC'; ctx.globalAlpha=.65;
    ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x,H); ctx.moveTo(0,y); ctx.lineTo(W,y); ctx.stroke(); ctx.restore();
    const dx=(x/W*10).toFixed(2), dy=((H/2-y)/H*8).toFixed(2), label=`X ${dx} div   Y ${dy>0?'+':''}${dy} div`;
    ctx.font=`400 11px ${tok('--f-mono')||'monospace'}`; if('fontStretch' in ctx) ctx.fontStretch='semi-condensed'; const tw=ctx.measureText(label).width, lx=Math.min(x+12,W-tw-18), ly=Math.max(y-12,22);
    ctx.fillStyle='rgba(7,9,11,.86)'; ctx.fillRect(lx-6,ly-15,tw+12,22); ctx.fillStyle=tok('--ink-100')||'#E6ECF2'; ctx.fillText(label,lx,ly);
  }
  function run(){
    cancelAnimationFrame(raf); done=false; layout(); const st=o.onState;
    if (reduce || !poly.length){ locked(1); done=true; st&&st('settled'); return; }
    const amps=[.2,.09,.03].map(a=>a*H), shifts=[(Math.random()-.5)*.24*W,(Math.random()-.5)*.1*W,(Math.random()-.5)*.025*W];
    const trails=[]; let k=0,t0=performance.now(),cur=noisy(0,amps[0],shifts[0]); st&&st('run');
    const frame=now=>{
      const t=now-t0;
      if (k<o.sweeps){
        const p=Math.min(1,t/o.sweepMs), idx=Math.floor(p*(cur.length-1));
        ctx.clearRect(0,0,W,H); if(o.axes) axes(ctx,W,H,true); trails.forEach((tr,i)=>stroke(tr,0,tr.length-1,o.thin,.14*(i+1)/trails.length)); stroke(cur,0,idx,o.thin,.95);
        const h=cur[idx]; if(h){ ctx.fillStyle=o.color; ctx.beginPath(); ctx.arc(h[0],h[1],2.6,0,6.3); ctx.fill(); }
        if (p>=1){ trails.push(cur); if(trails.length>2) trails.shift(); k++; t0=now; if(k<o.sweeps) cur=noisy(k,amps[k],shifts[k]); else st&&st('lock'); }
        raf=requestAnimationFrame(frame);
      } else { const b=Math.min(1,t/420), e=1-Math.pow(1-b,4); locked(o.mark?e:1); if(b<1) raf=requestAnimationFrame(frame); else { done=true; st&&st('settled'); } }
    };
    raf=requestAnimationFrame(frame);
  }
  if (o.cursors){ const host=canvas.parentElement;
    host.addEventListener('pointermove',e=>{ if(!done||e.pointerType==='touch') return; const r=canvas.getBoundingClientRect(); ptr=[e.clientX-r.left,e.clientY-r.top]; if(ptr[1]<0||ptr[1]>H){ptr=null;} locked(1); });
    host.addEventListener('pointerleave',()=>{ if(!done) return; ptr=null; locked(1); }); }
  let rT; addEventListener('resize',()=>{ clearTimeout(rT); rT=setTimeout(()=>{ if(done){ layout(); locked(1);} else if(started){ run(); } },150); });
  whenVisible(canvas, ()=>{ if(!started){ started=true; run(); } });
  return {replay:run};
}
window.JATScope = Scope;

safe('hero trace', () => {
const hero = document.getElementById('heroTrace');
if (hero){
  const hs = document.querySelector('.hero-state'), lbl = hs && hs.querySelector('.lbl');
  const words = {run:'Acquiring signal', lock:"Trig'd", settled:'Locked · stable'};
  /* Three layout modes, mirrored by the hero media queries in site.css:
     short landscape (phones on their side) -> compact, mark in the right column beside the copy
     narrow (< 880px)                         -> mark stacked in the band above the headline
     wide                                     -> mark in the right 40%, never crossing the copy column */
  const heroWrap = hero.parentElement.querySelector('.wrap');
  const shortLand = mq('(max-height:540px) and (orientation:landscape)'), narrow = mq('(max-width:879px)');
  const place = (W,H) => {
    const vh = window.innerHeight;
    if (shortLand.matches) { const unit = Math.min(.36*W/100, .44*vh/56); return {cx:.78*W, cy:Math.min(.46*H, .46*vh), unit, startX:.56}; }
    if (narrow.matches) { const band = parseFloat(getComputedStyle(heroWrap).paddingTop) || 200; const unit = Math.min(band*.58/56, .78*W/100); return {cx:.5*W, cy:band*.56, unit, startX:0}; }
    const unit = Math.min(.36*H/56, .38*W/100); return {cx:.77*W, cy:.36*H, unit, startX:.5};
  };
  Scope(hero,{axes:true, place, cursors:true,
    onState:s=>{ if(!hs) return; hs.dataset.state=s; lbl.textContent=words[s]; }});
}
document.querySelectorAll('canvas[data-trace]').forEach(c=>{
  const col = c.dataset.color ? tok(c.dataset.color) : undefined;
  Scope(c,{markH:+(c.dataset.h||.42), markX:+(c.dataset.x||.62), markY:+(c.dataset.y||.5), sweeps:+(c.dataset.sweeps||2), color:col||tok('--ch1'), mark:c.dataset.mark!=='false', cursors:c.dataset.cursors==='true'});
});
});

/* ---------- Page-head channel waveforms ---------- */
function wave(c){
  const ctx=c.getContext && c.getContext('2d'); if(!ctx) return; const kind=c.dataset.wave, col=tok(c.dataset.color||'--ch1')||'#3CE3F0'; let W,H,started=false;
  const f={
    step:x=>{ const x0=.22; if(x<x0) return .7; const t=(x-x0)/.07; return .32+(.7-.32)*Math.exp(-t)*Math.cos(t*2.4); },
    burst:x=>{ const e=Math.exp(-Math.pow((x-.55)/.16,2)); return .5-.28*e*Math.sin(x*70); },
    pulse:x=>{ const p=(x*5)%1; return p<.18?.28:.7; },
    sine:x=>.5-.18*Math.sin(x*Math.PI*2*3)
  }[kind]||(x=>.5);
  function size(){ const r=c.getBoundingClientRect(); const d=Math.min(2,devicePixelRatio||1); W=r.width; H=r.height; c.width=W*d; c.height=H*d; ctx.setTransform(d,0,0,d,0,0); }
  function draw(p){ ctx.clearRect(0,0,W,H); axes(ctx,W,H,false); ctx.strokeStyle=col; ctx.lineWidth=2; ctx.lineJoin='round'; ctx.lineCap='round';
    const g=ctx.createLinearGradient(0,0,W*.3,0); const h=col.replace('#',''); const rgb=[0,2,4].map(i=>parseInt(h.substr(i,2),16)).join(','); g.addColorStop(0,`rgba(${rgb},0)`); g.addColorStop(1,`rgba(${rgb},1)`); ctx.strokeStyle=g;
    ctx.beginPath(); const n=Math.max(2,Math.floor(W*p/2)); for(let i=0;i<=n;i++){ const x=i*2; const y=f(x/W)*H; i?ctx.lineTo(x,y):ctx.moveTo(x,y); } ctx.stroke();
    if(p<1){ const x=n*2, y=f(x/W)*H; ctx.fillStyle=col; ctx.beginPath(); ctx.arc(x,y,2.6,0,6.3); ctx.fill(); } }
  function run(){ size(); if(reduce){ draw(1); return; } const t0=performance.now(); const fr=now=>{ const p=Math.min(1,(now-t0)/900); draw(1-Math.pow(1-p,3)); if(p<1) requestAnimationFrame(fr); }; requestAnimationFrame(fr); }
  whenVisible(c, ()=>{ if(!started){ started=true; run(); } });
  let t; addEventListener('resize',()=>{ clearTimeout(t); t=setTimeout(()=>{ if(started){ size(); draw(1);} },150); });
}
safe('page-head waves', () => document.querySelectorAll('canvas[data-wave]').forEach(wave));

/* ---------- Versus: unaligned vendors vs one locked signal ---------- */
function drawVersus(c, mode){
  const ctx=c.getContext && c.getContext('2d'); if(!ctx) return;
  function paint(){
    const r=c.getBoundingClientRect(); if(r.width<2) return; const dpr=Math.min(2,devicePixelRatio||1);
    c.width=r.width*dpr; c.height=r.height*dpr; ctx.setTransform(dpr,0,0,dpr,0,0); const W=r.width,H=r.height;
    ctx.clearRect(0,0,W,H); ctx.lineJoin='round'; ctx.lineCap='round';
    if (mode==='many'){
      const cols=['--ch1','--ch2','--ch3','--ch4'].map(tok);
      cols.forEach((col,i)=>{ ctx.strokeStyle=col; ctx.globalAlpha=.85; ctx.lineWidth=1.5; ctx.beginPath();
        const ph=i*1.7, f=.012+i*.006, base=H*(.28+i*.11);
        for(let x=0;x<=W;x+=4){ const y=base+Math.sin(x*f+ph)*H*.06+Math.sin(x*.047+i)*H*.025; x?ctx.lineTo(x,y):ctx.moveTo(x,y); }
        ctx.stroke(); });
      ctx.globalAlpha=1;
    } else {
      const col=tok('--ch1'); ctx.strokeStyle=col; ctx.lineWidth=2; ctx.beginPath();
      const y0=H*.62, y1=H*.3, x0=W*.3;
      for(let x=0;x<=W;x+=3){ let y; if(x<x0) y=y0; else { const t=(x-x0)/(W*.11); y=y1+(y0-y1)*Math.exp(-t)*Math.cos(t*2.6); } x?ctx.lineTo(x,y):ctx.moveTo(x,y); }
      ctx.stroke();
      ctx.setLineDash([3,5]); ctx.strokeStyle=tok('--ch2'); ctx.globalAlpha=.7; ctx.lineWidth=1; ctx.beginPath(); ctx.moveTo(0,y1); ctx.lineTo(W,y1); ctx.stroke(); ctx.setLineDash([]); ctx.globalAlpha=1;
      ctx.font=`400 11px ${tok('--f-mono')||'monospace'}`; if('fontStretch' in ctx) ctx.fontStretch='semi-condensed'; ctx.fillStyle=tok('--ch2'); ctx.fillText('TARGET', W-58, y1-8);
    }
  }
  paint(); let t; addEventListener('resize',()=>{ clearTimeout(t); t=setTimeout(paint,150); });
}
safe('comparison traces', () => document.querySelectorAll('canvas[data-versus]').forEach(c=>drawVersus(c,c.dataset.versus)));

/* ---------- Copy buttons ---------- */
document.querySelectorAll('[data-copy-text]').forEach(btn=>btn.addEventListener('click',()=>{
  const text=btn.dataset.copyText, label=btn.textContent;
  const ok=()=>{ btn.dataset.done=''; btn.textContent='Copied'; setTimeout(()=>{ delete btn.dataset.done; btn.textContent=label; },1600); };
  const fallback=()=>{ const sel=btn.previousElementSibling; if(sel){ const r=document.createRange(); r.selectNodeContents(sel); const s=getSelection(); s.removeAllRanges(); s.addRange(r);} btn.textContent='Selected · press copy'; };
  try{ navigator.clipboard.writeText(text).then(ok,fallback); }catch(e){ fallback(); }
}));

/* ---------- Contact form ---------- */
safe('contact form', () => {
  const form = document.getElementById('leadForm'); if (!form) return;
  const status = document.getElementById('formStatus');
  const submit = form.querySelector('[type=submit]');
  const DRAFT = 'jat-inquiry-draft';
  const label = el => (form.querySelector(`label[for="${el.id}"]`)||{}).textContent || el.name;
  const domain = /^(https?:\/\/)?([a-z0-9-]+\.)+[a-z]{2,}(\/\S*)?$/i;
  const rules = {
    name: v => { v=v.trim(); return v.length < 2 ? 'Add your name so we know who to reply to.' : v.length > 100 ? 'Use 100 characters or fewer.' : true; },
    email: v => { v=v.trim(); return !v ? 'Add your email address so we can reply.' : v.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? 'Check the email address. It should look like you@company.com.' : true; },
    site: v => { v=v.trim(); return !v || domain.test(v) ? true : 'Add the full address, like mycompany.com, or leave it blank.'; },
    message: v => { const n=v.trim().length; return n < 20 ? `Tell us a little more about what the system should do (${20-n} more character${20-n===1?'':'s'}).` : n > 4000 ? 'Keep it under 4,000 characters. You can share more on the call.' : true; }
  };
  /* Wire each error message to its field so screen readers read it on focus. */
  Object.keys(rules).forEach(name => {
    const el = form.elements[name]; if (!el) return;
    const msg = el.closest('.field').querySelector('.msg'); if (!msg) return;
    msg.id = `${name}-error`; msg.removeAttribute('role');
  });
  const describe = (el, on) => {
    const ids = (el.getAttribute('aria-describedby')||'').split(' ').filter(Boolean).filter(i => i !== `${el.name}-error`);
    if (on) ids.push(`${el.name}-error`);
    ids.length ? el.setAttribute('aria-describedby', ids.join(' ')) : el.removeAttribute('aria-describedby');
  };
  function check(el){
    const f = el.closest('.field'), rule = rules[el.name]; if (!rule) return true;
    const r = rule(el.value);
    if (r === true){ f.removeAttribute('data-err'); el.removeAttribute('aria-invalid'); describe(el,false); return true; }
    f.setAttribute('data-err',''); el.setAttribute('aria-invalid','true'); f.querySelector('.msg span').textContent = r; describe(el,true); return false;
  }
  Object.keys(rules).forEach(name => {
    const el = form.elements[name]; if (!el) return;
    el.addEventListener('blur', () => { if (el.value.trim()) check(el); });
    el.addEventListener('input', () => { if (el.hasAttribute('aria-invalid')) check(el); });   /* clear errors as soon as they are fixed */
  });
  /* Message counter */
  const msgEl = form.elements.message, counter = document.getElementById('msgCount');
  const count = () => { if (!counter) return; const n = msgEl.value.trim().length; counter.textContent = `${n.toLocaleString()} / 4,000`; counter.dataset.over = n > 4000 ? '' : null; if (n<=4000) delete counter.dataset.over; };
  if (msgEl) { msgEl.addEventListener('input', count); }
  /* Keep a draft on this device so a refresh or failed send never loses the message. */
  const fields = ['name','email','org','site','budget','when','message'];
  const save = () => { try { const d = {}; fields.forEach(n => { if (form.elements[n]) d[n] = form.elements[n].value; }); d.channel = [...form.querySelectorAll('[name=channel]:checked')].map(i=>i.value); sessionStorage.setItem(DRAFT, JSON.stringify(d)); } catch(_){} };
  try {
    const d = JSON.parse(sessionStorage.getItem(DRAFT) || 'null');
    if (d) { fields.forEach(n => { if (d[n] && form.elements[n]) form.elements[n].value = d[n]; }); (d.channel||[]).forEach(v => { const c = form.querySelector(`[name=channel][value="${CSS.escape(v)}"]`); if (c) c.checked = true; }); }
  } catch(_){}
  count();
  form.addEventListener('input', save); form.addEventListener('change', save);

  const say = (kind, parts) => {               /* build status text without innerHTML */
    status.hidden = false; kind ? status.dataset.kind = kind : status.removeAttribute('data-kind');
    status.textContent = '';
    parts.forEach(p => status.appendChild(typeof p === 'string' ? document.createTextNode(p) : p));
    status.focus();
  };
  let busy = false;
  form.addEventListener('submit', e => {
    e.preventDefault();
    if (busy) return;                                       /* ignore double submits */
    if (form.elements.company_url && form.elements.company_url.value) return;   /* honeypot: bots fill hidden fields */
    const bad = Object.keys(rules).map(n => form.elements[n]).filter(el => el && !check(el));
    if (bad.length){
      const list = document.createElement('ul');
      bad.forEach(el => { const li = document.createElement('li'); const a = document.createElement('a'); a.href = '#'+el.id; a.textContent = label(el).replace(/\s*\(optional\)\s*/,''); a.addEventListener('click', ev => { ev.preventDefault(); el.focus(); }); li.appendChild(a); list.appendChild(li); });
      const strong = document.createElement('strong'); strong.textContent = `${bad.length} field${bad.length>1?'s need':' needs'} attention before sending:`;
      say('', [strong, list]);
      return;
    }
    if (navigator.onLine === false){
      const strong = document.createElement('strong'); strong.textContent = 'You appear to be offline. ';
      say('', [strong, 'Your message is saved on this device. Reconnect and send again, or email info@jeremyanderson.tech.']);
      return;
    }
    busy = true; submit.disabled = true; submit.setAttribute('aria-busy','true');
    const lbl = submit.firstChild; const orig = lbl.textContent; lbl.textContent = 'Checking… ';
    setTimeout(() => {
      busy = false; submit.disabled = false; submit.removeAttribute('aria-busy'); lbl.textContent = orig;
      const ch = [...form.querySelectorAll('[name=channel]:checked')].map(i=>i.value).join(', ') || 'not specified';
      const strong = document.createElement('strong'); strong.textContent = 'Ready to send. ';
      say('ok', [strong, `This concept page isn't connected to email yet, so nothing was sent and your draft stays on this device. In the live site this goes to info@jeremyanderson.tech, and a written proposal follows within three business days of the discovery call. Channels: ${ch}.`]);
    }, 700);
  });
});

/* ---------- Year ---------- */
safe('year', () => document.querySelectorAll('[data-year]').forEach(el=>el.textContent=new Date().getFullYear()));
})();
