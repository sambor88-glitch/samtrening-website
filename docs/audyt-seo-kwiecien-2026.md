# Audyt SEO samtrening.com — kwiecień 2026

Skrót z pełnego audytu (`Audyt_SEO_samtrening_kwiecien_2026.docx`). W tym pliku: problemy, które projekt rozwiązuje.

## ✅ Problemy rozwiązane przez ten redesign

### 3.2. Title tag strony głównej bez "Kraków" i bez marki
- **Było:** "Trening personalny dla każdego poziomu zaawansowania"
- **Jest:** "Trener Personalny Kraków Centrum | SAMtrening — Studio 1:1 pod Wawelem"

### 3.3. Pusty H1 na stronie /blog/
- **Było:** H1 to pusty znak `#` bez tekstu
- **Jest:** "Blog SAMtrening — trening, zdrowie, dieta. Porady trenera z Krakowa"

### 3.5. URL z emoji w wpisie narciarskim
- **Problem:** `/2025/12/02/%f0%9f%8e%bf-trening-narciarski-krakow/` — emoji w URL
- **Rozwiązanie:** w nowym systemie slug URL-e zgodnie z planem contentowym (bez emoji, bez dat, bez polskich znaków)

### 3.7. Stopka pokazuje © 2023 na 7 z 8 podstron
- **Rozwiązanie:** dynamiczny rok w stopce (`<script>document.getElementById('year').textContent = new Date().getFullYear();</script>`)

### 3.8. Zero danych strukturalnych (Schema.org)
- **Wdrożone typy:**
  - `LocalBusiness` (strona główna)
  - `Service` (strony oferty + landingi z areaServed)
  - `Person` (Kasia, Maciek, Bartek + autor wpisu)
  - `BlogPosting` (wpisy blogowe)
  - `Blog` (strona /blog/)
  - `FAQPage` (strony oferty, kontakt, e-trening)
  - `BreadcrumbList` (wszystkie podstrony)
  - `ContactPage` + `HealthAndBeautyBusiness` (strona kontaktowa)
  - `SoftwareApplication` (platforma e-trening)
  - `WebPage` (RODO)

### 3.9. Strona /kontakt/ to pusta karta
- **Było:** tylko numer, email, adres
- **Jest:** mapa, godziny (5:30-23:00), dojazd (MPK, pieszo, autem, rowerem), formularz, NAP, sąsiedztwo (6 kart), FAQ, 4 kanały kontaktu bezpośredniego

### 4.8. Niespójne dane kontaktowe (NAP)
- **Było:** różne telefony (+48 728 385 203 vs +48 690 060 736), różne domeny (.com vs .pl)
- **Jest:**
  - Telefon: `+48 728 385 203` (konsekwentnie)
  - Email: `biuro@samtrening.com` (konsekwentnie)
  - Domena: `samtrening.com` (konsekwentnie)

## ✅ Treści ofertowe

Audyt wskazuje, że każda strona ofertowa musi zawierać:
- H1 z lokalną frazą ✅
- Akapit wstępny (100-150 słów) ✅
- Sekcję "Dla kogo" (150-200 słów) ✅
- Sekcję "Jak pracujemy / Proces" (200-300 słów) ✅
- Sekcję "Efekty" (150-200 słów) ✅
- Sekcję "Cennik" (100-150 słów) ✅
- Sekcję FAQ (300-400 słów) ✅
- CTA z formularzem kontaktowym ✅

Wszystko wdrożone w 3 stronach ofertowych: treningi-personalne, zdrowa-ciaza, e-trening.

## 🔜 Do zrobienia po wdrożeniu WordPress

Audyt wymienia rekomendowane pluginy:
- [ ] **Rank Math SEO** — pełne SEO + schema (15 min konfiguracji)
- [ ] **Trustindex** — import opinii Google Business
- [ ] **ShortPixel** — konwersja obrazów do WebP
- [ ] **WP Rocket** — cache i optymalizacja
- [ ] **Complianz** — cookie banner zgodny z RODO + PKE 2024
- [ ] **Really Simple SSL** — wymuszenie HTTPS
- [ ] **WP-Optimize** — czyszczenie bazy danych

## 📊 Metryki do poprawy

Po wdrożeniu monitorować:
- Core Web Vitals (LCP, FID/INP, CLS) — cel: wszystkie zielone
- Pozycje na frazy lokalne: "trener personalny Kraków", "trener personalny Kraków Centrum", "trening po porodzie Kraków"
- Ruch organiczny — cel: 2x w 6 miesięcy
- Konwersje: wypełnione formularze konsultacji (cel: 20+ miesięcznie)
- Google Reviews — cel: 100+ opinii (obecnie 44)

---

**Pełny audyt:** `Audyt_SEO_samtrening_kwiecien_2026.docx` (w projekcie).
**Autor audytu:** zespół SEO SAMtrening.
**Data:** kwiecień 2026.
