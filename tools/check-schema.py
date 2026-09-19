# -*- coding: utf-8 -*-
"""Sprawdza bloki JSON-LD w prototype/: czy parsują się poprawnie i czy pytania
w FAQPage zgadzają się co do znaku z sekcjami FAQ widocznymi na stronie.

Zasada z SW112233-71: FAQPage tylko tam, gdzie FAQ jest widoczne dla czytelnika.

    python3 tools/check-schema.py

Kod wyjścia 1, jeśli cokolwiek się nie zgadza — nadaje się do CI.
"""
import json, re, glob, html, sys

import os
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__))) + '/'
BLOCK_RE = re.compile(r'<script type="application/ld\+json">(.*?)</script>', re.S)
ITEM_RE = re.compile(r'<div class="faq__item">.*?<span>(?P<q>.*?)</span>', re.S)

def clean(s):
    s = re.sub(r'<[^>]+>', '', s)
    return re.sub(r'\s+', ' ', html.unescape(s)).strip()

ok = True
files = sorted(glob.glob(ROOT + 'prototype/**/*.html', recursive=True))
for f in files:
    src = open(f, encoding='utf-8').read()
    rel = f.replace(ROOT, '')
    for i, m in enumerate(BLOCK_RE.findall(src), 1):
        try:
            data = json.loads(m)
        except json.JSONDecodeError as e:
            print(f"✖ {rel} blok {i}: niepoprawny JSON — {e}")
            ok = False
            continue
        nodes = data.get('@graph', [data])
        types = [n.get('@type') for n in nodes]

        # FAQ: schema vs treść
        visible = [clean(q) for q in ITEM_RE.findall(src)]
        faq_nodes = [n for n in nodes if n.get('@type') == 'FAQPage']
        schema_qs = [q['name'] for n in faq_nodes for q in n.get('mainEntity', [])]
        if faq_nodes and sorted(schema_qs) != sorted(visible):
            print(f"✖ {rel}: FAQ schema ({len(schema_qs)}) ≠ treść ({len(visible)})")
            for q in set(schema_qs) ^ set(visible):
                print(f"    różnica: {q[:70]}")
            ok = False
        elif faq_nodes:
            print(f"✔ {rel}: FAQPage {len(schema_qs)}/{len(visible)} pytań zgodnych z treścią | {', '.join(types)}")
        elif visible:
            print(f"· {rel}: {len(visible)} widocznych pytań, bez FAQPage | {', '.join(types)}")
        else:
            print(f"✔ {rel}: JSON OK | {', '.join(types)}")

        # zakazane wg SW112233-71 / -38
        if 'aggregateRating' in m:
            print(f"✖ {rel}: nadal jest aggregateRating")
            ok = False
        if '30-101' in m:
            print(f"✖ {rel}: stary kod pocztowy w schema")
            ok = False

print()
print("WYNIK:", "wszystko OK" if ok else "SĄ BŁĘDY")
sys.exit(0 if ok else 1)
