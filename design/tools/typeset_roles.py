"""Type role system for Jeremy Anderson Tech (Calibrated Signal).

One-time refactor tool and the record of which rule plays which role.
Each role fixes size, weight, width (font-stretch), line-height and tracking together,
so headings can no longer drift into near-identical one-off values.

  python3 design/tools/typeset_roles.py   # rewrites site.css and the overview CSS in place (idempotent)
"""
import re
from pathlib import Path

D = Path(__file__).resolve().parents[1]

# role -> declarations (size always via its token)
ROLES = {
    'display': dict(size='var(--t-display)', weight='640', stretch='125%', lh='.92', ls='-.035em'),
    'page':    dict(size='var(--t-page)',    weight='640', stretch='125%', lh='.95', ls='-.03em'),
    'section': dict(size='var(--t-section)', weight='620', stretch='118%', lh='1.05', ls='-.02em'),
    'title':   dict(size='var(--t-title)',   weight='600', stretch='112%', lh='1.15', ls='-.01em'),
    'item':    dict(size='var(--t-item)',    weight='600', stretch='108%', lh='1.35', ls='0'),
    'lede':    dict(size='var(--t-lede)',    lh='1.5'),
    'body':    dict(size='var(--t-body)'),
    'small':   dict(size='var(--t-small)'),
    'label':   dict(size='var(--t-label)'),
    'label-long': dict(size='var(--t-label-long)'),
    'data':    dict(size='var(--t-data)'),
    'data-lg': dict(size='var(--t-title)'),
    'specimen': dict(size='var(--t-specimen)'),
    'statement': dict(size='var(--t-statement)', weight='500', lh='1.2'),
}
PROP = dict(size='font-size', weight='font-weight', stretch='font-stretch', lh='line-height', ls='letter-spacing')

# selector -> role. Selectors are matched exactly as written in the stylesheet.
SITE = {
    '.hero h1': 'display',
    'h1': 'page', '.page-head h1': 'page', '.close h2': 'page',
    'h2': 'section', '.prose h2': 'section',
    'h3': 'title', '.svc-grid h3': 'title', '.work-card h3': 'title', '.work-meta h3': 'title', '.ch-row h3': 'title',
    'h4': 'item', '.post h3': 'item', '.faq summary': 'item', '.price-row .name': 'item', '.steps h3': 'item',
    '.strip .v': 'item', '.stat-strip .v': 'item',
    '.lede': 'lede',
    'body': 'body', '.svc-grid .outcome': 'body', '.input': 'body', '.hero .lede': 'body',
    '.ch-row .price .amt': 'data', '.price-row .amt': 'data', '.copy-line .mono': 'data',
    '.sticky-card .amt': 'data-lg',
    '.readout.long': 'label-long',
    '.concept-note': 'small', '.field .hint': 'small', '.field .msg': 'small', '.portrait .ph': 'small',
    '.btn-sm': 'small', '.menu-btn': 'small',
}
OVERVIEW = {
    '.ov-hero h1': 'display', '.deliv h3': 'title', '.pn': 'item',
    '.pd': 'small', '.files .mono': 'small', '.files .muted': 'small', '.rules li': 'body',
}
BOOK = {
    'body': 'body', 'h2': 'section', 'h3': 'title', 'h4': 'item', 'h3.as-h4': 'item', '.persona h4,.persona h3.as-h4': 'item',
    '.taglines .t': 'title', '.lede': 'lede', '.cover .sub': 'lede', '.sec-head p': 'body',
    '.cover h1': 'specimen', '.specimen .big': 'specimen', '.price-row .name': 'item', '.price-row .p': 'data', '.input': 'body',
    '.rail nav a': 'small', '.cap': 'small', '.note': 'small', '.lockups figcaption': 'small', '.apps figcaption': 'small',
    '.field label': 'small', '.photo-frame': 'small', '.cover-strip .v': 'label-long', '.sec-num': 'label-long',
    '.spec td.mono': 'label-long', '.elev div': 'small', '.misuse figcaption': 'small', '.sw .info': 'small', '.field .hint': 'small',
}
# Anything not listed: map the remaining literal sizes by value (all remaining .6875/.75/.8125rem rules are mono labels).
BY_VALUE = {'.6875rem': 'var(--t-label)', '.75rem': 'var(--t-label)', '.8125rem': 'var(--t-label)', '.625rem': 'var(--t-label)', '.5625rem': 'var(--t-label)',
            '.875rem': 'var(--t-small)', '.9375rem': 'var(--t-small)', '1.0625rem': 'var(--t-body)',
            '1.125rem': 'var(--t-item)', '1.1875rem': 'var(--t-item)'}

RULE = re.compile(r'(^|[{};\n])(\s*)([^{};@]+?)\{([^{}]*)\}')

def apply(css, mapping):
    def rule(m):
        pre, ws, sel, body = m.group(1), m.group(2), m.group(3), m.group(4)
        key = sel.strip()
        if 'font-size' not in body:
            return m.group(0)
        decls = [d for d in body.split(';') if d.strip()]
        if key in mapping:
            role = ROLES[mapping[key]]
            props = {PROP[k] for k in role}
            decls = [d for d in decls if d.split(':', 1)[0].strip() not in props]
            decls += [f'{PROP[k]}:{v}' for k, v in role.items()]
        else:
            out = []
            for d in decls:
                k, _, v = d.partition(':')
                if k.strip() == 'font-size' and v.strip() in BY_VALUE:
                    d = f'{k}:{BY_VALUE[v.strip()]}'
                out.append(d)
            decls = out
        return f'{pre}{ws}{sel}{{{";".join(decls)}}}'
    return RULE.sub(rule, css)

if __name__ == '__main__':
    css_path = D/'site-src/site.css'
    css = css_path.read_text()
    css_path.write_text(apply(css, SITE))
    ov = D/'overview-src/build_overview.py'
    s = ov.read_text()
    head, rest = s.split("CSS = '''", 1)
    block, tail = rest.split("'''", 1)
    ov.write_text(head + "CSS = '''" + apply(block, OVERVIEW) + "'''" + tail)
    for i in range(1, 6):   # brand book style blocks (BOOK map; book-only .8125rem prose maps to small)
        f = D/f'brand-book-src/part{i}.html'
        BY_VALUE_BOOK = dict(BY_VALUE, **{'.8125rem': 'var(--t-small)'})
        def book(m):
            global BY_VALUE
            saved, BY_VALUE = BY_VALUE, BY_VALUE_BOOK
            try: return '<style>' + apply(m.group(1), BOOK) + '</style>'
            finally: BY_VALUE = saved
        f.write_text(re.sub(r'<style>([\s\S]*?)</style>', book, f.read_text()))
    print('rewrote site.css and overview CSS')
