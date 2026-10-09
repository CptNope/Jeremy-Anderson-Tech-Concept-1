"""Build the inline SVG <symbol> sprite (mk, lk-h, lk-s, icon) used by the brand book and site."""
from pathlib import Path
HERE = Path(__file__).resolve().parent
ROOT = HERE.parents[1]  # repository root
import json
W = json.load(open(HERE/'wordmark.json'))
TRACE = "M9 50 Q9 64 23 64 Q33 64 36 57 L52 16 L68 64 H84 V16"
BAR = "M70 16 H100"
MK = f'<g style="stroke:var(--mk,#3CE3F0)" fill="none" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"><path d="{TRACE}"/><path d="{BAR}"/></g>'
def t(key, x, base, cap, var, dflt):
    s = cap/W[key]['cap']
    return f'<use href="#p-{key}" transform="translate({x:.2f} {base}) scale({s:.5f})" style="fill:var({var},{dflt})"/>', W[key]['w']*s
defs = f'<defs><path id="p-name" d="{W["name"]["d"]}"/><path id="p-tech" d="{W["tech"]["d"]}"/><path id="p-jat" d="{W["jat"]["d"]}"/></defs>'
# horizontal
cap=17; x0=124
a,w1=t('name',x0,16+cap,cap,'--lk-name','#E6ECF2'); b,w2=t('tech',x0,64,cap,'--lk-tech','#A3AFBC')
rx0=x0+w2+10; rx1=x0+w1; n=12
ticks=[f'<path d="M{rx0:.1f} 64H{rx1:.1f}"/>']+[f'<path d="M{rx0+(rx1-rx0)*i/n:.1f} 64V{64-(10 if i in (0,n,n//2) else 5)}"/>' for i in range(n+1)]
HW = x0+w1+6
lkh = f'<symbol id="lk-h" viewBox="1 6 {HW:.0f} 68">{MK}{a}{b}<g style="stroke:var(--lk-rule,#3D4855)" stroke-width="1.5" fill="none">{"".join(ticks)}</g></symbol>'
# stacked
cap=15; _,w1=t('name',0,0,cap,'--lk-name','#E6ECF2'); _,w2=t('tech',0,0,cap,'--lk-tech','#A3AFBC')
SW=max(w1,108); y1=68+22+cap; y2=y1+14+cap
a,_=t('name',(SW-w1)/2,y1,cap,'--lk-name','#E6ECF2'); b,_=t('tech',(SW-w2)/2,y2,cap,'--lk-tech','#A3AFBC')
lks = f'<symbol id="lk-s" viewBox="-4 6 {SW+8:.0f} {y2-2:.0f}"><g transform="translate({(SW-108)/2-1:.2f} 0)">{MK}</g>{a}{b}</symbol>'
mk = f'<symbol id="mk" viewBox="1 6 108 68">{MK}</symbol>'
grid=''.join(f'<path d="M{x} 8V120"/>' for x in range(24,120,16))+''.join(f'<path d="M8 {y}H120"/>' for y in range(24,120,16))
icon = f'<symbol id="icon" viewBox="0 0 128 128"><rect width="128" height="128" rx="28" fill="#0A0C0F"/><rect x="8" y="8" width="112" height="112" rx="20" fill="#10141A"/><g stroke="#1B222B" stroke-width="1">{grid}</g><g transform="translate(9.5 24)">{MK}</g></symbol>'
sprite = f'<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">{defs}{mk}{lkh}{lks}{icon}</svg>'
open(HERE/'sprite.html','w').write(sprite)
print(len(sprite), 'lk-h width', round(HW), 'lk-s', round(SW+8), round(y2))
