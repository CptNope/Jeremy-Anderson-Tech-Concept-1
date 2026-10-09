"""Rebuild everything served by GitHub Pages from the design sources.

  python3 design/tools/build_all.py            # sprite + logos + brand book + site
  python3 design/tools/build_all.py --wordmark # also re-outline the wordmark from the Archivo font

Steps
 1. (optional) identity/wordmark.py  -> identity/wordmark.json   (needs fontTools + design/fonts/Archivo-Variable.ttf)
 2. identity/logos.py                -> brand-book/assets/*.svg
 3. identity/sprite.py               -> identity/sprite.html     (inline <symbol> sprite)
 4. brand-book-src/part1..5.html     -> brand-book/index.html
 5. site-src/ (site.css, site.js, build/*.py) -> repository root (*.html, site.css, site.js)
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
head = ('<!doctype html>\n<html lang="en">\n<head>\n<meta charset="utf-8">\n'
        '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">\n'
        '<meta name="robots" content="noindex">\n<meta name="theme-color" content="#0A0C0F">\n'
        '<link rel="icon" href="assets/jat-mark.svg" type="image/svg+xml">\n</head>\n<body>\n')
back = ('<p style="position:fixed;right:12px;bottom:12px;margin:0;z-index:9"><a href="../" style="font:500 12px Martian Mono,monospace;'
        'background:#10141A;border:1px solid #2E3845;border-radius:6px;padding:8px 10px;color:#3CE3F0;text-decoration:none">View website concept →</a></p>')
(ROOT/'brand-book').mkdir(exist_ok=True)
(ROOT/'brand-book/index.html').write_text(head + body + '\n' + back + '\n</body>\n</html>\n')
print('> brand-book/index.html')

# site
shutil.copy(D/'site-src/site.css', ROOT/'site.css'); shutil.copy(D/'site-src/site.js', ROOT/'site.js')
run(sys.executable, D/'site-src/build/pages2.py', env={**__import__('os').environ, 'PAGES':'1', 'OUT':str(ROOT)+'/'})
print('Done.')
