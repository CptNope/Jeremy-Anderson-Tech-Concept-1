"""Outline the wordmark text (Archivo, wdth 125) into SVG path data -> wordmark.json.
Needs design/fonts/Archivo-Variable.ttf (see design/fonts/README.md) and fontTools."""
from pathlib import Path
HERE = Path(__file__).resolve().parent
ROOT = HERE.parents[1]  # repository root
import sys
from fontTools.ttLib import TTFont
from fontTools.varLib.instancer import instantiateVariableFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen

def outline(text, wdth, wght, tracking_em=0.0, size=100):
    f = TTFont(str(ROOT/'design/fonts/Archivo-Variable.ttf'))
    inst = instantiateVariableFont(f, {'wdth': wdth, 'wght': wght})
    gs = inst.getGlyphSet(); cmap = inst.getBestCmap(); upm = inst['head'].unitsPerEm
    hmtx = inst['hmtx']
    scale = size/upm
    pen = SVGPathPen(gs)
    x = 0
    for ch in text:
        g = cmap[ord(ch)]
        tp = TransformPen(pen, (scale,0,0,-scale,x,0))
        gs[g].draw(tp)
        x += hmtx[g][0]*scale + tracking_em*size
    x -= tracking_em*size
    capH = inst['OS/2'].sCapHeight*scale
    return pen.getCommands(), x, capH

import json
out = {}
for key,(t,wd,wg,tr) in {
  'name':('JEREMY ANDERSON',125,620,0.04),
  'tech':('TECH',125,380,0.32),
  'jat':('JAT',125,700,0.02),
}.items():
    d,w,cap = outline(t,wd,wg,tr)
    out[key]={'d':d,'w':round(w,2),'cap':round(cap,2)}
json.dump(out, open(HERE/'wordmark.json','w'))
print({k:(v['w'],v['cap']) for k,v in out.items()})
