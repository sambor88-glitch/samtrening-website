# SAMtrening — redesign strony 2026

Nowa, statyczna wersja strony [samtrening.com](https://www.samtrening.com) — kameralnego studia treningu personalnego w centrum Krakowa (Plac Na Groblach 23).

**Status:** gotowy prototyp HTML/CSS/JS, przygotowany do migracji na WordPress.

---

## 🎯 O projekcie

Projekt powstał na bazie audytu SEO z kwietnia 2026 oraz gotowych treści (tradycyjne treningi personalne, zdrowa ciąża, e-trening online). Design system został wybrany w wariancie **"Motion & Strength"** — paleta czarny + electric lime (`#E8FF3E`), typografia Big Shoulders Display + DM Sans.

Każda strona ma **kompletny design system w jednym pliku** — to świadoma decyzja pod fazę prototypu. Po migracji do WordPressa wydzielimy wspólne style, componenty i szablony.

## 📁 Struktura plików

```
samtrening-website/
├── index.html                              # Strona główna
├── treningi-personalne.html                # Oferta: treningi personalne 1:1
├── zdrowa-ciaza.html                       # Oferta: trening okołoporodowy
├── e-trening.html                          # Oferta: treningi online + platforma
├── zespol.html                             # Kasia, Maciek, Bartek
├── blog.html                               # Lista wpisów + filtrowanie
├── kontakt.html                            # Formularz, mapa, dojazd
├── rodo.html                               # Polityka prywatności (RODO + PKE 2024)
│
├── blog/
│   └── trening-po-porodzie-krakow.html     # Template wpisu (przykład)
│
├── lokalizacje/
│   ├── krakow-centrum.html                 # Landing — biura, kancelarie
│   ├── stare-miasto-krakow.html            # Landing — mieszkańcy, hotele
│   └── krakow-debniki.html                 # Landing — druga strona Wisły
│
└── docs/
    ├── plan-contentowy.md                  # 30 tematów wpisów blogowych (kwi-sie 2026)
    ├── todo-przed-wdrozeniem.md            # Lista rzeczy do zrobienia
    └── audyt-seo-kwiecien-2026.md          # Podsumowanie audytu SEO
```

## 🚀 Jak uruchomić

Strona jest statyczna (HTML + CSS + vanilla JS) — nie wymaga żadnego buildu ani serwera.

**Lokalnie (najprostszy sposób):**
```bash
# Po prostu otwórz index.html w przeglądarce
open index.html           # macOS
xdg-open index.html       # Linux
start index.html          # Windows
```

**Lokalnie (z lokalnym serwerem — zalecane):**
```bash
# Python 3 (jeśli masz zainstalowany)
python3 -m http.server 8000
# → http://localhost:8000

# Node.js (jeśli masz zainstalowany)
npx serve .
```

Lokalny serwer jest lepszy, bo niektóre przeglądarki blokują część funkcji (fetch, moduły JS) przy otwarciu pliku bezpośrednio z dysku.

## 🌐 GitHub Pages (hosting darmowy)

Po wrzuceniu na GitHub można włączyć darmowy hosting:

1. **Settings** → **Pages**
2. **Source:** Deploy from a branch
3. **Branch:** `main`, folder `/root`
4. Zapisz — po kilku minutach strona będzie dostępna pod adresem `https://twoj-nick.github.io/samtrening-website/`

## 🛠 Technologie

- **HTML5** — semantyczny markup, Schema.org (LocalBusiness, Service, Person, BlogPosting, FAQPage, BreadcrumbList)
- **CSS3** — custom properties, CSS Grid, Flexbox, clamp() dla fluid typography
- **Vanilla JS** — zero bibliotek, tylko `IntersectionObserver` i podstawowy DOM
- **Fonty:** Big Shoulders Display, DM Sans, Fraunces (tylko na wpisach blogowych), JetBrains Mono — ładowane z Google Fonts

## 🎨 Design system

```css
/* Kolory */
--bg: #0A0908;           /* tło główne */
--ink: #FAFAF7;          /* tekst */
--accent: #E8FF3E;       /* electric lime — akcent */

/* Typografia */
--font-display: "Big Shoulders Display"   /* nagłówki, duże liczby */
--font-body: "DM Sans"                     /* body text, UI */
--font-mono: "JetBrains Mono"              /* labels, kody, meta */
--font-article: "Fraunces"                 /* serif — tylko wpisy blogowe */
```

## ✅ Co już działa

- 12 kompletnych stron z pełnym SEO (title, meta, canonical, OG, Schema.org)
- Responsywny design (breakpoints: 1200/1024/900/700/500 px)
- Linki wewnętrzne relatywne — działają lokalnie i na GitHub Pages
- Filtrowanie wpisów blogowych (kategoria + autor, bez przeładowania strony)
- Reading progress bar + sticky TOC na wpisach blogowych
- Sticky header z login-linkiem do `app.samtrening.com`
- Formularze kontaktowe (demo — w produkcji integracja z mailer-em)
- Accessibility: skip-links, aria-labels, semantyczne landmarks, focus states
- `prefers-reduced-motion` respektowany

## 🔜 TODO przed wdrożeniem produkcyjnym

Pełna lista: [`docs/todo-przed-wdrozeniem.md`](docs/todo-przed-wdrozeniem.md)

Najważniejsze:
- [ ] Integracja formularzy z backendem (Contact Form 7 lub mailer)
- [ ] Newsletter — Mailchimp / MailerLite
- [ ] Podmiana placeholderów na prawdziwe zdjęcia (zespół, studio, wpisy blogowe)
- [ ] Weryfikacja iframe Google Maps (klucz API)
- [ ] Migracja do WordPress theme
- [ ] Cookie banner (Complianz po migracji na WP)
- [ ] Pozostałe 29 wpisów blogowych wg planu contentowego

## 📞 Kontakt

**SAMTRENING Sp. z o.o.**
Plac Na Groblach 23, 30-101 Kraków
📞 +48 728 385 203
✉️ biuro@samtrening.com
🌐 samtrening.com

---

## 📄 Licencja

Kod strony jest własnością SAMTRENING Sp. z o.o. Nie używaj bez zgody właściciela.

Design system, treści blogowe, zdjęcia — wszystkie prawa zastrzeżone.
