# SAMtrening — repozytorium strony

Jedno miejsce na kod strony [samtrening.com](https://www.samtrening.com) — kameralnego studia
treningu personalnego w centrum Krakowa (Plac Na Groblach 23).

**Uwaga: to repozytorium jest publiczne.** Nie commituj tu kluczy API, danych SMTP ani niczego,
co ma zostać prywatne. Sekret raz wypchnięty zostaje w historii i wymaga wymiany, nie usunięcia.

## Co gdzie leży

```
samtrening-website/
├── theme/
│   └── samtrening-2026/    # motyw WordPressa — kod, który działa na stronie
│                           # KATALOG JESZCZE PUSTY, patrz docs/wdrozenie-motywu.md
├── prototype/              # statyczny prototyp z kwietnia 2026 (archiwum designu)
├── docs/                   # dokumentacja, notatki decyzyjne, schema
└── tools/                  # skrypty: generator dokumentu schema, walidator
```

### `theme/` — produkcja

Docelowe miejsce motywu `samtrening-2026`, na którym działa strona. Dziś motyw żyje wyłącznie
na serwerze (AWS Lightsail) i nikt go jeszcze nie wyciągnął do gita — procedura krok po kroku:
[`docs/wdrozenie-motywu.md`](docs/wdrozenie-motywu.md).

Dopóki tego nie zrobimy, zmiany robione w prototypie **nie trafiają na stronę** — ktoś musi je
przepisać ręcznie. To był powód decyzji o jednym repozytorium:
[`docs/prototyp-vs-produkcja.md`](docs/prototyp-vs-produkcja.md).

### `prototype/` — archiwum

Statyczna wersja z kwietnia 2026: 12 stron HTML z pełnym SEO, design system „Motion & Strength",
zero zależności. Powstała przed migracją na WordPressa i pokazuje stan sprzed niej — slugi, CTA
i część treści różnią się już od produkcji.

Traktujemy ją jako **dokumentację designu**, nie jako kod do wdrożenia.

```bash
cd prototype && python3 -m http.server 8000   # → http://localhost:8000
```

Design system:

```css
--bg: #0A0908;           /* tło główne */
--ink: #FAFAF7;          /* tekst */
--accent: #E8FF3E;       /* electric lime — akcent */

--font-display: "Big Shoulders Display"   /* nagłówki, duże liczby */
--font-body: "DM Sans"                     /* body text, UI */
--font-mono: "JetBrains Mono"              /* labels, kody, meta */
--font-article: "Fraunces"                 /* serif — tylko wpisy blogowe */
```

Dane strukturalne w prototypie: `ExerciseGym`, `Service`, `ContactPage`, `Person`, `BlogPosting`,
`FAQPage`, `BreadcrumbList`, `OfferCatalog` — zgodnie z
[SW112233-71](https://maciej-samborski.atlassian.net/browse/SW112233-71). Bloki gotowe do wklejenia
na produkcji: [`docs/schema-json-ld.md`](docs/schema-json-ld.md).

### `docs/` — dokumentacja

| Plik | O czym |
|---|---|
| [`wdrozenie-motywu.md`](docs/wdrozenie-motywu.md) | jak wciągnąć motyw z Lightsaila do gita i jak potem wdrażać |
| [`prototyp-vs-produkcja.md`](docs/prototyp-vs-produkcja.md) | dlaczego jedno repozytorium, co się rozjechało, ile kosztuje utrzymywanie dwóch wersji |
| [`schema-json-ld.md`](docs/schema-json-ld.md) | bloki JSON-LD pod produkcję (generowane z plików w `prototype/`) |
| [`audyt-seo-kwiecien-2026.md`](docs/audyt-seo-kwiecien-2026.md) | podsumowanie audytu SEO |
| [`plan-contentowy.md`](docs/plan-contentowy.md) | 30 tematów wpisów blogowych |
| [`todo-przed-wdrozeniem.md`](docs/todo-przed-wdrozeniem.md) | lista rzeczy do zrobienia (częściowo nieaktualna po migracji na WP) |

### `tools/` — skrypty

```bash
python3 tools/check-schema.py   # czy JSON-LD się parsuje i czy FAQ w schema = FAQ na stronie
python3 tools/schema-doc.py     # regeneruje docs/schema-json-ld.md z plików prototypu
```

`check-schema.py` zwraca kod 1 przy niezgodności, więc nadaje się do CI. Pilnuje zasady
z SW112233-71: `FAQPage` tylko tam, gdzie FAQ jest widoczne dla czytelnika.

## Publikacja prototypu

**GitLab Pages** — działa automatycznie z gałęzi `main` (`.gitlab-ci.yml`), publikuje `prototype/`.

**GitHub Pages** — wyłączone domyślnie. Żeby włączyć:

1. Settings → Pages → Source: **GitHub Actions**
2. Settings → Secrets and variables → Actions → Variables → dodaj `PAGES_ENABLED` = `true`

Workflow [`.github/workflows/pages.yml`](.github/workflows/pages.yml) bez tej zmiennej nie robi nic,
więc nie zapala CI na czerwono w repozytorium z wyłączonym Pages.

**Kopia na GitLabie** — każdy push na GitHub jest kopiowany przez
[`.github/workflows/mirror-to-gitlab.yml`](.github/workflows/mirror-to-gitlab.yml).
GitHub pozostaje źródłem prawdy.

## Znane długi

- W `prototype/blog.html` jest 12 linków do wpisów, których jeszcze nie ma (plan contentowy).
  Trzy linki `/blog/` w `kontakt.html` są bezwzględne i nie działają lokalnie.
  To stan sprzed reorganizacji, nie jej skutek.
- `prototype/lokalizacje/krakow-centrum.html` istnieje, choć na produkcji ta strona jest
  przekierowana (301) na Stare Miasto — patrz SW112233-28.

## Kontakt

**SAMTRENING Sp. z o.o.**
Plac Na Groblach 23, 31-101 Kraków
+48 728 385 203 · biuro@samtrening.com · samtrening.com

## Licencja

Kod strony jest własnością SAMTRENING Sp. z o.o. Nie używaj bez zgody właściciela.
Design system, treści blogowe, zdjęcia — wszystkie prawa zastrzeżone.
