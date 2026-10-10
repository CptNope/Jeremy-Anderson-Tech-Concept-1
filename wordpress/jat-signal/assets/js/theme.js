/* JAT Signal front end: navigation, signal traces, role line, copy buttons, inquiry form.
   Ported from the Calibrated Signal concept. Every feature is isolated so one failure never
   disables navigation or the form. Strings come from JAT_I18N (inc/setup.php). */
(()=>{
'use strict';
const T = Object.assign({menu:'Menu',close:'Close',copy:'Copy',copied:'Copied',select:'Selected · press copy',states:{run:'Acquiring signal',lock:"Trig'd",settled:'Locked · stable'},form:{}}, window.JAT_I18N||{});
const fmt = (s, v) => String(s||'').replace(/%(\d\$)?[sd]/, v);
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
/* Tokens never change at runtime: read each once (a live computed-style read forces a style recalc). */
const TOK = new Map(); let rootCS = null;
const tok = n => { if (!TOK.has(n)) { rootCS = rootCS || getComputedStyle(document.documentElement); TOK.set(n, rootCS.getPropertyValue(n).trim()); } return TOK.get(n); };

/* ---------- Mobile navigation ---------- */
safe('navigation', () => {
  const nav = document.querySelector('.nav'), mb = document.querySelector('.menu-btn');
  if (!nav || !mb) return;
  const links = () => [...nav.querySelectorAll('a[href]'), mb];
  const isOpen = () => nav.classList.contains('open');
  const set = (open, returnFocus) => {
    nav.classList.toggle('open', open);
    mb.setAttribute('aria-expanded', String(open));
    const l = mb.querySelector('.lbl'); if (l) l.textContent = open ? T.close : T.menu;
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
/* The mark is constant geometry, so sample it with arithmetic instead of SVG getPointAtLength
   (which forced a full-document layout per call). 160 points, evenly spaced by arc length. */
function markPoints(){
  if (MARK) return MARK;
  const fine=[[9,50]], q=(a,b,c,t)=>(1-t)*(1-t)*a+2*(1-t)*t*b+t*t*c;
  [[9,64,23,64],[33,64,36,57]].forEach(([cx,cy,x,y])=>{ const [x0,y0]=fine[fine.length-1]; for(let i=1;i<=48;i++){ const t=i/48; fine.push([q(x0,cx,x,t),q(y0,cy,y,t)]); } });
  [[52,16],[68,64],[84,64],[84,16]].forEach(p=>fine.push(p));
  const cum=[0]; for(let i=1;i<fine.length;i++) cum.push(cum[i-1]+Math.hypot(fine[i][0]-fine[i-1][0],fine[i][1]-fine[i-1][1]));
  const L=cum[cum.length-1], n=160, out=[]; let j=1;
  for(let i=0;i<=n;i++){ const d=L*i/n; while(j<cum.length-1&&cum[j]<d) j++; const s=cum[j]-cum[j-1], t=s?(d-cum[j-1])/s:0; out.push([fine[j-1][0]+(fine[j][0]-fine[j-1][0])*t, fine[j-1][1]+(fine[j][1]-fine[j-1][1])*t]); }
  return (MARK = out);
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
document.querySelectorAll('.hero canvas.hero-trace').forEach(hero => {
  const host = hero.parentElement;
  const hs = host.querySelector('.hero-state'), lbl = hs && hs.querySelector('.lbl');
  const keepLabel = hs && hs.classList.contains('has-badge');   /* a custom badge stays; only its dot reports the state */
  /* Three layout modes, mirrored by the hero media queries in theme.css:
     short landscape (phones on their side) -> compact, mark in the right column beside the copy
     narrow (< 880px)                         -> mark stacked in the band above the headline
     wide                                     -> mark in the right 40%, never crossing the copy column */
  const heroWrap = host.querySelector('.wrap');
  const shortLand = mq('(max-height:540px) and (orientation:landscape)'), narrow = mq('(max-width:879px)');
  const place = (W,H) => {
    const vh = window.innerHeight;
    if (shortLand.matches) { const unit = Math.min(.36*W/100, .44*vh/56); return {cx:.78*W, cy:Math.min(.46*H, .46*vh), unit, startX:.56}; }
    if (narrow.matches) { const band = (heroWrap && parseFloat(getComputedStyle(heroWrap).paddingTop)) || 200; const unit = Math.min(band*.58/56, .78*W/100); return {cx:.5*W, cy:band*.56, unit, startX:0}; }
    const unit = Math.min(.36*H/56, .38*W/100); return {cx:.77*W, cy:.36*H, unit, startX:.5};
  };
  Scope(hero,{axes:true, place, cursors:true,
    onState:s=>{ if(!hs) return; hs.dataset.state=s; if(lbl && !keepLabel) lbl.textContent=T.states[s]||s; }});
});
document.querySelectorAll('canvas[data-trace]').forEach(c=>{
  const col = c.dataset.color ? tok(c.dataset.color) : undefined;
  Scope(c,{markH:+(c.dataset.h||.42), markX:+(c.dataset.x||.62), markY:+(c.dataset.y||.5), sweeps:+(c.dataset.sweeps||2), color:col||tok('--ch1'), mark:c.dataset.mark!=='false', cursors:c.dataset.cursors==='true'});
});
});


/* ---------- Hero role line: cycles through the roles list ---------- */
safe('role line', () => document.querySelectorAll('.hero-role[data-roles]').forEach(el => {
  let roles; try { roles = JSON.parse(el.dataset.roles); } catch(_) { return; }
  const out = el.querySelector('.role-text'); if (!out || !Array.isArray(roles) || roles.length < 2) return;
  const wrap = el.dataset.wrap === '1', show = r => wrap ? `{ ${r} }` : r;
  if (reduce) return;                     /* reduced motion: the first role stays; the full list is in the visually hidden text */
  let i = 0, timer = 0;
  const tick = () => {
    out.classList.add('out');
    setTimeout(() => { i = (i + 1) % roles.length; out.textContent = show(roles[i]); out.classList.remove('out'); }, 320);
  };
  const start = () => { if (!timer) timer = setInterval(tick, 3200); };
  const stop = () => { clearInterval(timer); timer = 0; };
  document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
  whenVisible(el, start, 0);
}));

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
  let shown=false; whenVisible(c, ()=>{ shown=true; paint(); }, 0); let t; addEventListener('resize',()=>{ if(!shown) return; clearTimeout(t); t=setTimeout(paint,150); });
}
safe('comparison traces', () => document.querySelectorAll('canvas[data-versus]').forEach(c=>drawVersus(c,c.dataset.versus)));

/* ---------- Copy buttons ---------- */
safe('copy buttons', () => document.querySelectorAll('[data-copy-text]').forEach(btn=>btn.addEventListener('click',()=>{
  const text=btn.dataset.copyText, label=btn.dataset.label||(btn.dataset.label=btn.textContent);
  const ok=()=>{ btn.dataset.done=''; btn.textContent=T.copied; setTimeout(()=>{ delete btn.dataset.done; btn.textContent=label; },1600); };
  const fallback=()=>{ const sel=btn.previousElementSibling; if(sel){ const r=document.createRange(); r.selectNodeContents(sel); const s=getSelection(); s.removeAllRanges(); s.addRange(r);} btn.textContent=T.select; setTimeout(()=>{ btn.textContent=label; },2400); };
  try{ navigator.clipboard.writeText(text).then(ok,fallback); }catch(e){ fallback(); }
})));

/* ---------- Inquiry form (jat/contact-form) ---------- */
safe('contact form', () => document.querySelectorAll('form[data-jat-form]').forEach(form => {
  const F = T.form || {};
  const status = form.querySelector('.form-status');
  const submit = form.querySelector('[type=submit]');
  const DRAFT = 'jat-inquiry-draft';
  form.setAttribute('novalidate', '');                     /* our messages replace the browser bubbles */
  const label = el => (form.querySelector(`label[for="${el.id}"]`)||{}).textContent || el.name;
  const domain = /^(https?:\/\/)?([a-z0-9-]+\.)+[a-z]{2,}(\/\S*)?$/i;
  const rules = {
    name: v => { v=v.trim(); return v.length < 2 ? F.name : v.length > 100 ? F.nameLong : true; },
    email: v => { v=v.trim(); return !v ? F.email : v.length > 254 || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) ? F.emailBad : true; },
    site: v => { v=v.trim(); return !v || domain.test(v) ? true : F.site; },
    message: v => { const n=v.trim().length; return n < 20 ? fmt(F.msgShort, 20-n) : n > 4000 ? F.msgLong : true; }
  };
  const field = name => form.elements[name];
  /* Wire each error message to its field so screen readers read it on focus. */
  Object.keys(rules).forEach(name => {
    const el = field(name); if (!el) return;
    const msg = el.closest('.field') && el.closest('.field').querySelector('.msg'); if (!msg) return;
    msg.id = `${el.id}-error`;
  });
  const describe = (el, on) => {
    const id = `${el.id}-error`;
    const ids = (el.getAttribute('aria-describedby')||'').split(' ').filter(i => i && i !== id);
    if (on) ids.push(id);
    ids.length ? el.setAttribute('aria-describedby', ids.join(' ')) : el.removeAttribute('aria-describedby');
  };
  const mark = (el, text) => {
    const f = el.closest('.field'); if (!f) return;
    const span = f.querySelector('.msg span');
    if (text === true || !text){ f.removeAttribute('data-err'); el.removeAttribute('aria-invalid'); describe(el,false); return true; }
    f.setAttribute('data-err',''); el.setAttribute('aria-invalid','true'); if (span) span.textContent = text; describe(el,true); return false;
  };
  const check = el => { const rule = rules[el.name]; return rule ? mark(el, rule(el.value)) : true; };
  Object.keys(rules).forEach(name => {
    const el = field(name); if (!el) return;
    el.addEventListener('blur', () => { if (el.value.trim()) check(el); });
    el.addEventListener('input', () => { if (el.hasAttribute('aria-invalid')) check(el); });   /* clear errors as soon as they are fixed */
  });
  /* Message counter */
  const msgEl = field('message'), counter = form.querySelector('[data-count]');
  const count = () => { if (!counter || !msgEl) return; const n = msgEl.value.trim().length; counter.textContent = `${n.toLocaleString()} / ${(4000).toLocaleString()}`; if (n > 4000) counter.dataset.over = ''; else delete counter.dataset.over; };
  if (msgEl) msgEl.addEventListener('input', count);
  /* Keep a draft on this device so a refresh, an expired form or a failed send never loses the message. */
  const fields = ['name','email','org','site','budget','when','message'];
  const boxes = () => [...form.querySelectorAll('[name="channel[]"]')];
  const save = () => { try { const d = {}; fields.forEach(n => { if (field(n)) d[n] = field(n).value; }); d.channel = boxes().filter(i=>i.checked).map(i=>i.value); sessionStorage.setItem(DRAFT, JSON.stringify(d)); } catch(_){} };
  const clear = () => { try { sessionStorage.removeItem(DRAFT); } catch(_){} };
  if (status && status.dataset.kind === 'ok') clear();      /* no-JS round trip came back sent */
  try {
    const d = JSON.parse(sessionStorage.getItem(DRAFT) || 'null');
    if (d) { fields.forEach(n => { if (d[n] && field(n)) field(n).value = d[n]; }); (d.channel||[]).forEach(v => { const c = boxes().find(b => b.value === v); if (c) c.checked = true; }); }
  } catch(_){}
  count();
  form.addEventListener('input', save); form.addEventListener('change', save);

  const say = (kind, parts) => {               /* build status text without innerHTML */
    if (!status) return;
    status.hidden = false; kind ? status.dataset.kind = kind : status.removeAttribute('data-kind');
    status.textContent = '';
    parts.forEach(p => { if (p == null || p === '') return; status.appendChild(typeof p === 'string' ? document.createTextNode(p) : p); });
    status.focus();
  };
  const strong = t => { const s = document.createElement('strong'); s.textContent = t + ' '; return s; };
  const listBad = bad => {
    const list = document.createElement('ul');
    bad.forEach(el => { const li = document.createElement('li'); const a = document.createElement('a'); a.href = '#'+el.id; a.textContent = label(el).replace(/\s*\((optional|facultatif|opcional)\)\s*/i,''); a.addEventListener('click', ev => { ev.preventDefault(); el.focus(); }); li.appendChild(a); list.appendChild(li); });
    return list;
  };
  let busy = false;
  const done = () => { busy = false; if (submit){ submit.disabled = false; submit.removeAttribute('aria-busy'); const l = submit.querySelector('.lbl'); if (l && l.dataset.orig) l.textContent = l.dataset.orig; } };
  form.addEventListener('submit', e => {
    if (!window.fetch || !window.FormData) return;           /* very old browsers: plain POST, the server redirects back */
    e.preventDefault();
    if (busy) return;                                       /* ignore double submits */
    const bad = Object.keys(rules).map(field).filter(el => el && !check(el));
    if (bad.length){ say('err', [strong(fmt(F.attention, bad.length)), listBad(bad)]); return; }
    if (navigator.onLine === false){ say('err', [strong(F.offline), F.offline2]); return; }
    busy = true;
    if (submit){ submit.disabled = true; submit.setAttribute('aria-busy','true'); const l = submit.querySelector('.lbl'); if (l){ l.dataset.orig = l.dataset.orig || l.textContent; l.textContent = F.sending; } }
    fetch(form.getAttribute('action'), {   /* not form.action: the hidden input named "action" shadows it */
      method:'POST', body:new FormData(form), credentials:'same-origin', headers:{'Accept':'application/json'}})
      .then(r => r.json().catch(() => ({status: r.ok ? 'sent' : 'error'})))
      .then(res => {
        done();
        const s = res && res.status;
        if (s === 'sent'){
          clear(); form.reset(); count();
          Object.keys(rules).forEach(n => { const el = field(n); if (el) mark(el, true); });
          say('ok', [strong(F.sent), F.sent2]);
        } else if (s === 'invalid' && res.errors){
          const els = Object.keys(res.errors).map(n => { const el = field(n); if (el) mark(el, res.errors[n]); return el; }).filter(Boolean);
          say('err', [strong(fmt(F.attention, els.length || 1)), els.length ? listBad(els) : '']);
        } else if (s === 'expired'){ say('err', [res.message || F.expired]);
        } else if (s === 'limited'){ say('err', [res.message || F.failed]);
        } else { say('err', [F.failed]); }
      })
      .catch(() => { done(); say('err', navigator.onLine === false ? [strong(F.offline), F.offline2] : [F.failed]); });
  });
}));

/* ---------- Year ---------- */
safe('year', () => document.querySelectorAll('[data-year]').forEach(el=>el.textContent=new Date().getFullYear()));
})();
