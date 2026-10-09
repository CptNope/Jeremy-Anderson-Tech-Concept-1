(()=>{
'use strict';
const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
const root = getComputedStyle(document.documentElement);
const tok = n => root.getPropertyValue(n).trim();

/* ---------- Mobile navigation ---------- */
const nav = document.querySelector('.nav'), mb = document.querySelector('.menu-btn');
if (nav && mb){
  const set = open => { nav.classList.toggle('open', open); mb.setAttribute('aria-expanded', open); mb.querySelector('.lbl').textContent = open ? 'Close' : 'Menu'; document.body.style.overflow = open ? 'hidden' : ''; };
  mb.addEventListener('click', ()=> set(!nav.classList.contains('open')));
  addEventListener('keydown', e=>{ if (e.key==='Escape' && nav.classList.contains('open')){ set(false); mb.focus(); } });
  matchMedia('(min-width:1080px)').addEventListener('change', e=>{ if (e.matches) set(false); });
}

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
  const ctx = canvas.getContext('2d');
  const o = Object.assign({markH:.36, markX:.7, markY:.45, sweeps:3, sweepMs:760, cursors:false, thin:1.6, color:tok('--ch1')||'#3CE3F0', mobile:null, onState:null, mark:true, startX:0}, opt);
  let sx=0,W=0,H=0,poly=[],mr=[0,0],bar=null,scale=1,done=false,raf=0,started=false,ptr=null;
  const seeds = Array.from({length:6},()=>[Math.random()*6.28, Math.random()*6.28]);
  function layout(){
    const r = canvas.getBoundingClientRect(); if (r.width<2||r.height<2){ poly=[]; return; }
    const dpr = Math.min(2, devicePixelRatio||1); W=r.width; H=r.height; canvas.width=W*dpr; canvas.height=H*dpr; ctx.setTransform(dpr,0,0,dpr,0,0);
    const m = (o.mobile && W<760) ? Object.assign({},o,o.mobile) : o;
    scale = H*m.markH/56; const ox = W*m.markX - 54.5*scale, oy = H*m.markY - 40*scale;
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
    ctx.font=`500 11px ${tok('--f-mono')||'monospace'}`; const tw=ctx.measureText(label).width, lx=Math.min(x+12,W-tw-18), ly=Math.max(y-12,22);
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
  let rT; addEventListener('resize',()=>{ clearTimeout(rT); rT=setTimeout(()=>{ if(done){ layout(); locked(1);} },150); });
  new IntersectionObserver((es,ob)=>{ es.forEach(en=>{ if(en.isIntersecting&&!started){ started=true; run(); ob.disconnect(); } }); },{threshold:.2}).observe(canvas);
  return {replay:run};
}
window.JATScope = Scope;

const hero = document.getElementById('heroTrace');
if (hero){
  const hs = document.querySelector('.hero-state'), lbl = hs && hs.querySelector('.lbl');
  const words = {run:'Acquiring signal', lock:"Trig'd", settled:'Locked · stable'};
  Scope(hero,{axes:true, markH:.36, markX:.76, markY:.36, startX:.5, cursors:true, mobile:{markH:.11, markX:.5, markY:.12, startX:0},
    onState:s=>{ if(!hs) return; hs.dataset.state=s; lbl.textContent=words[s]; }});
}
document.querySelectorAll('canvas[data-trace]').forEach(c=>{
  const col = c.dataset.color ? tok(c.dataset.color) : undefined;
  Scope(c,{markH:+(c.dataset.h||.42), markX:+(c.dataset.x||.62), markY:+(c.dataset.y||.5), sweeps:+(c.dataset.sweeps||2), color:col||tok('--ch1'), mark:c.dataset.mark!=='false', cursors:c.dataset.cursors==='true'});
});

/* ---------- Page-head channel waveforms ---------- */
function wave(c){
  const ctx=c.getContext('2d'), kind=c.dataset.wave, col=tok(c.dataset.color||'--ch1')||'#3CE3F0'; let W,H,started=false;
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
  new IntersectionObserver((es,ob)=>{ if(es[0].isIntersecting&&!started){ started=true; run(); ob.disconnect(); } }).observe(c);
  let t; addEventListener('resize',()=>{ clearTimeout(t); t=setTimeout(()=>{ if(started){ size(); draw(1);} },150); });
}
document.querySelectorAll('canvas[data-wave]').forEach(wave);

/* ---------- Versus: unaligned vendors vs one locked signal ---------- */
function drawVersus(c, mode){
  const ctx=c.getContext('2d');
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
      ctx.font=`500 10px ${tok('--f-mono')||'monospace'}`; ctx.fillStyle=tok('--ch2'); ctx.fillText('TARGET', W-58, y1-8);
    }
  }
  paint(); let t; addEventListener('resize',()=>{ clearTimeout(t); t=setTimeout(paint,150); });
}
document.querySelectorAll('canvas[data-versus]').forEach(c=>drawVersus(c,c.dataset.versus));

/* ---------- Copy buttons ---------- */
document.querySelectorAll('[data-copy-text]').forEach(btn=>btn.addEventListener('click',()=>{
  const text=btn.dataset.copyText, label=btn.textContent;
  const ok=()=>{ btn.dataset.done=''; btn.textContent='Copied'; setTimeout(()=>{ delete btn.dataset.done; btn.textContent=label; },1600); };
  const fallback=()=>{ const sel=btn.previousElementSibling; if(sel){ const r=document.createRange(); r.selectNodeContents(sel); const s=getSelection(); s.removeAllRanges(); s.addRange(r);} btn.textContent='Selected · press copy'; };
  try{ navigator.clipboard.writeText(text).then(ok,fallback); }catch(e){ fallback(); }
}));

/* ---------- Contact form ---------- */
const form = document.getElementById('leadForm');
if (form){
  const status = document.getElementById('formStatus');
  const rules = {
    name: v=> v.trim().length>1 || 'Add your name so we know who to reply to.',
    email: v=> /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) || 'Add an email address like you@company.com.',
    message: v=> v.trim().length>=20 || 'Tell us a little more: what should the system do? (20 characters minimum)'
  };
  function check(el){
    const f=el.closest('.field'), rule=rules[el.name]; if(!rule) return true;
    const r=rule(el.value); if (r===true){ f.removeAttribute('data-err'); el.removeAttribute('aria-invalid'); return true; }
    f.setAttribute('data-err',''); el.setAttribute('aria-invalid','true'); f.querySelector('.msg span').textContent=r; return false;
  }
  form.querySelectorAll('input,textarea').forEach(el=>el.addEventListener('blur',()=>{ if(el.value) check(el); }));
  form.addEventListener('submit',e=>{
    e.preventDefault(); const bad=[...form.querySelectorAll('[name=name],[name=email],[name=message]')].filter(el=>!check(el));
    if (bad.length){ bad[0].focus(); status.hidden=false; status.removeAttribute('data-kind'); status.textContent=`${bad.length} field${bad.length>1?'s need':' needs'} attention before sending.`; return; }
    const ch=[...form.querySelectorAll('[name=channel]:checked')].map(i=>i.value).join(', ')||'not specified';
    status.hidden=false; status.dataset.kind='ok';
    status.innerHTML=`<strong>Ready to send.</strong> This concept page isn't connected to email yet, so nothing was sent. In the live site this goes to info@jeremyanderson.tech, and a written proposal follows within three business days of the discovery call. Summary: ${ch}.`;
    status.focus();
  });
}

/* ---------- Year ---------- */
document.querySelectorAll('[data-year]').forEach(el=>el.textContent=new Date().getFullYear());
})();
