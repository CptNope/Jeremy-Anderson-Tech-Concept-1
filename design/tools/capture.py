"""Capture full-page desktop (1440) and mobile (390) screenshots of built pages into .impeccable/review/.
Usage: python3 design/tools/capture.py index platforms brand-book/index
Requires: pip install playwright && playwright install chromium (and network access for Google Fonts)."""
import sys
from pathlib import Path
from playwright.sync_api import sync_playwright
ROOT = Path(__file__).resolve().parents[2]
OUT = ROOT/'.impeccable'/'review'; OUT.mkdir(parents=True, exist_ok=True)
pages = sys.argv[1:] or ['index']
with sync_playwright() as p:
    b = p.chromium.launch()
    for name in pages:
        for label, w, h in [('desktop',1440,900),('mobile',390,844)]:
            pg = b.new_page(viewport={'width':w,'height':h}); errs=[]
            pg.on('pageerror', lambda e: errs.append(str(e)))
            pg.goto((ROOT/f'{name}.html').as_uri()); pg.wait_for_timeout(1200)
            H = pg.evaluate('document.documentElement.scrollHeight')
            for y in range(0, H, 600): pg.evaluate(f'window.scrollTo(0,{y})'); pg.wait_for_timeout(90)
            pg.evaluate('window.scrollTo(0,0)'); pg.wait_for_timeout(3200)
            sw = pg.evaluate('document.documentElement.scrollWidth')
            slug = name.replace('/','-')
            pg.screenshot(path=str(OUT/f'{slug}-{label}.png'), full_page=True)
            print(slug, label, 'height', H, 'scrollWidth', sw, 'errors', errs[:3]); pg.close()
    b.close()
