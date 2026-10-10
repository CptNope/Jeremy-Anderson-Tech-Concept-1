"""Self-hosted font build: instance the Google Fonts variable TTFs to the axis ranges in use and subset to Latin.

Sources (OFL 1.1, no Reserved Font Name):
  https://raw.githubusercontent.com/google/fonts/main/ofl/archivo/Archivo%5Bwdth%2Cwght%5D.ttf
  https://raw.githubusercontent.com/google/fonts/main/ofl/martianmono/MartianMono%5Bwdth%2Cwght%5D.ttf
Put both in design/fonts/ as Archivo-Variable.ttf and MartianMono-Variable.ttf, then:
  pip install fonttools brotli && python3 design/tools/subset_fonts.py
"""
from pathlib import Path
R = Path(__file__).resolve().parents[2]
from fontTools.ttLib import TTFont
from fontTools.varLib import instancer
from fontTools import subset
U="U+0020-007E,U+00A0-00FF,U+0131,U+0152-0153,U+0394,U+02C6,U+02DA,U+02DC,U+2013-2014,U+2018-201E,U+2022,U+2026,U+2039-203A,U+20AC,U+2122,U+2190-2193,U+2212,U+2248,U+2264-2265"
def run(src,out,limits):
    f=TTFont(str(src))
    print(src,[ (a.axisTag,a.minValue,a.defaultValue,a.maxValue) for a in f['fvar'].axes])
    f=instancer.instantiateVariableFont(f,limits)
    f.save(str(R/'design/fonts/_tmp.ttf'))
    opts=subset.Options(); opts.flavor='woff2'; opts.layout_features=['kern','liga','calt','tnum','lnum','case','ccmp','locl','mark','mkmk']; opts.name_IDs=[1,2,3,4,6]; opts.hinting=False; opts.desubroutinize=True
    s=subset.Subsetter(opts); ft=TTFont(str(R/'design/fonts/_tmp.ttf'))
    s.populate(unicodes=subset.parse_unicodes(U)); s.subset(ft)
    ft.flavor='woff2'; ft.save(str(out))
    import os; print(out,os.path.getsize(out))
A, M = R/'design/fonts/Archivo-Variable.ttf', R/'design/fonts/MartianMono-Variable.ttf'
run(A, R/'fonts/archivo-var.woff2', {'wdth':(100,125),'wght':(400,700)})        # site + overview
run(A, R/'fonts/archivo-var-full.woff2', {'wdth':(62,125),'wght':(400,700)})    # brand book (width slider runs 62-125)
run(M, R/'fonts/martian-mono-var.woff2', {'wdth':(87.5,100),'wght':(400,500)})
(R/'design/fonts/_tmp.ttf').unlink(missing_ok=True)
