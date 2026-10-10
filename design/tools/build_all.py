"""Rebuild everything served by GitHub Pages from the design sources.

  python3 design/tools/build_all.py            # sprite + logos + brand book + site
  python3 design/tools/build_all.py --wordmark # also re-outline the wordmark from the Archivo font

Steps
 1. (optional) identity/wordmark.py  -> identity/wordmark.json   (needs fontTools + design/fonts/Archivo-Variable.ttf)
 2. identity/logos.py                -> brand-book/assets/*.svg
 3. identity/sprite.py               -> identity/sprite.html     (inline <symbol> sprite)
 4. brand-book-src/part1..5.html     -> brand-book/index.html
 5. site-src/ (site.css, site.js, build/*.py) -> site/ (*.html, site.css, site.js)
 6. overview-src/build_overview.py -> index.html (concept overview with the concept bar)
"""
import subprocess, sys, shutil
from pathlib import Path
ROOT = Path(__file__).resolve().parents[2]
D = ROOT/'design'
def run(*args, **kw):
    print('>', ' '.join(map(str,args))); subprocess.run(args, check=True, **kw)
if '--wordmark' in sys.argv:
    run(sys.executable, D/'identity/wordmark.py')
run(sys.executable, D/'identity/logos.py')
run(sys.executable, D/'identity/sprite.py')

# brand book
sprite = (D/'identity/sprite.html').read_text()
body = ''.join((D/f'brand-book-src/part{i}.html').read_text() for i in range(1,6)).replace('<!--SPRITE-->', sprite)
sys.path.insert(0, str(D/'site-src/build'))
from common import stack_tables, long_readouts  # noqa: E402
body = long_readouts(stack_tables(body))
head = ('<!doctype html>\n<html lang="en">\n<head>\n<meta charset="utf-8">\n'
        '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n'
        '<meta name="robots" content="noindex">\n<meta name="theme-color" content="#0A0C0F">\n'
        '<link rel="icon" href="assets/jat-mark.svg" type="image/svg+xml">\n</head>\n<body>\n')
sys.path.insert(0, str(D/'site-src/build'))
from common import concept_bar, stack_tables  # noqa: E402
# The concept bar is fixed in the brand book so the fixed section rail can sit beneath it.
bar = '<a class="skip" href="#top">Skip to content</a>\n' + concept_bar('../', 'brand') + (
    '<style>.cbar{position:fixed;top:0;left:0;right:0;z-index:60}'
    'body{padding-top:calc(var(--cbar-h) + env(safe-area-inset-top,0px))}'
    '.rail{top:calc(var(--cbar-h) + env(safe-area-inset-top,0px))!important}'
    'html{scroll-padding-top:calc(var(--cbar-h) + 24px)}'
    '@media (max-width:959px){html{scroll-padding-top:calc(var(--cbar-h) + 72px)}}'
    # phones on their side: the concept bar scrolls away so only the 56px section bar stays
    '@media (max-height:540px) and (orientation:landscape) and (max-width:959px){'
    '.cbar{position:static}body{padding-top:0}.rail{top:env(safe-area-inset-top,0px)!important}html{scroll-padding-top:72px}}'
    '</style>')
back = ''
head = head.replace('<body>\n', '<body>\n' + bar + '\n')
(ROOT/'brand-book').mkdir(exist_ok=True)
(ROOT/'brand-book/index.html').write_text(head + body + '\n' + back + '\n</body>\n</html>\n')
print('> brand-book/index.html')

# site
(ROOT/'site').mkdir(exist_ok=True)
shutil.copy(D/'site-src/site.css', ROOT/'site/site.css'); shutil.copy(D/'site-src/site.js', ROOT/'site/site.js')
run(sys.executable, D/'site-src/build/pages2.py', env={**__import__('os').environ, 'PAGES':'1', 'OUT':str(ROOT/'site')+'/'})

# overview (repository root)
run(sys.executable, D/'overview-src/build_overview.py')
print('Done.')
