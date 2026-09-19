# prototype/

Statyczny prototyp strony samtrening.com z kwietnia 2026 — 12 stron HTML, zero zależności,
design system „Motion & Strength" w każdym pliku.

**To jest archiwum, nie kod produkcyjny.** Strona działa dziś na WordPressie z motywem
`samtrening-2026`; prototyp pokazuje stan sprzed migracji i miejscami rozjeżdża się z produkcją
(slugi landingów, CTA, `krakow-centrum` przekierowane na produkcji). Szczegóły:
[`../docs/prototyp-vs-produkcja.md`](../docs/prototyp-vs-produkcja.md).

Przydaje się jako dokumentacja designu i jako źródło bloków JSON-LD —
[`../docs/schema-json-ld.md`](../docs/schema-json-ld.md) generuje się wprost z tych plików.

```bash
python3 -m http.server 8000   # → http://localhost:8000
```
