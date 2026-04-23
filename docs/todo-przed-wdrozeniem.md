# TODO przed wdrożeniem produkcyjnym

Lista rzeczy do zrobienia, zanim projekt pójdzie na produkcję (samtrening.com). Stan: kwiecień 2026, projekt w fazie prototypu HTML.

---

## 🔥 Krytyczne (muszą być przed startem)

### Integracja formularzy
- [ ] **Formularz kontaktowy** (`kontakt.html`, `index.html`, strony oferty) — obecnie demo z `alert()`. W produkcji: Contact Form 7 lub WPForms w WordPressie.
- [ ] **Newsletter** (`blog.html`) — integracja z Mailchimp lub MailerLite.
- [ ] **Mailer** — SendGrid / Mailgun / Amazon SES dla wysyłki maili z formularzy.

### NAP — ujednolicenie danych kontaktowych (audyt 4.8)
- [x] Email: `biuro@samtrening.com` (było mieszanie z `.pl`)
- [x] Telefon: `+48 728 385 203` (było również `+48 690 060 736`)
- [x] Domena: `samtrening.com`
- [ ] **Zaktualizować także:** Google Business Profile, stopki w mailach, wizytówki, materiały marketingowe

### Zdjęcia
- [ ] Zdjęcia zespołu (Kasia, Maciek, Bartek) — obecnie avatary literowe
- [ ] Zdjęcia studia (sekcja Kontakt, Lokalizacja, landingi)
- [ ] Zdjęcia wpisów blogowych (featured image + hero)
- [ ] Prawdziwe opinie klientów (obecnie są wzorcowe) — import z Google Business

### Google Maps
- [ ] Klucz API Google Maps (obecnie iframe jest placeholderem)
- [ ] Weryfikacja Google Business (jeśli jeszcze nie)

### WordPress
- [ ] **Migracja do custom theme** — każdy plik HTML do szablonu PHP
- [ ] Custom Post Types: Blog Posts, Testimoniale, Team Members
- [ ] Menu zarządzane z WP admina
- [ ] ACF lub metaboxy dla pól dynamicznych (FAQ, cennik)

---

## 🟡 Ważne (w pierwszym miesiącu po starcie)

### SEO / Analityka
- [ ] Sitemap.xml
- [ ] Robots.txt
- [ ] Google Search Console — weryfikacja, submit sitemap
- [ ] Google Analytics 4 (GA4) — instalacja + cele konwersji
- [ ] Bing Webmaster Tools
- [ ] Schema.org weryfikacja w Rich Results Test

### RODO / Cookies (Prawo komunikacji elektronicznej 2024)
- [ ] **Cookie banner** — Complianz (WordPress) lub custom
- [ ] Integracja bannera z Google Analytics (consent mode v2)
- [ ] Integracja z Meta Pixel (jeśli będzie)

### Blog content
- [ ] Pozostałe 29 wpisów wg `docs/plan-contentowy.md`
- [ ] Odświeżenie 3 starych wpisów z 2018 (bonus w planie)

### Performance
- [ ] Optymalizacja obrazów — WebP, lazy loading, responsive srcset
- [ ] Minifikacja CSS i JS
- [ ] CDN (Cloudflare darmowy plan)
- [ ] Cache: WP Rocket lub W3 Total Cache

---

## 🟢 Nice-to-have (po wdrożeniu)

### Rozszerzenia
- [ ] Platforma `app.samtrening.com` — teraz tylko placeholder, docelowo pełna aplikacja (dashboard, plan treningowy, chat, pomiary, płatności)
- [ ] Kalendarz rezerwacji online
- [ ] System opinii klientów (integracja z Google Reviews przez plugin)
- [ ] Wersja angielska (klientami bywają expaci z centrum Krakowa)

### Contentmarketing
- [ ] Instagram + Facebook wg planu (pliki `Instagram_SAMtrening_2026.docx`, `Facebook_SAMtrening_2026.docx`)
- [ ] YouTube shorts / reels
- [ ] Podcast (opcjonalnie)

### Techniczne
- [ ] Monitoring uptime (np. UptimeRobot — darmowy)
- [ ] Backup automatyczny (WP: UpdraftPlus)
- [ ] SSL Labs A+ grade
- [ ] Lighthouse 95+ score (wszystkie metryki)

---

## 📋 Weryfikacje linii MPK

W treści stron są wspomniane numery linii autobusów i tramwajów przystanek "Filharmonia". Do weryfikacji przed wdrożeniem:
- Autobus 124, 194
- Tramwaj 2, 13, 18, 20

Źródło: mpk.krakow.pl (sprawdzić aktualny rozkład).

## 📋 Pozostałe detale

- [ ] Weryfikacja istnienia subdomeny `app.samtrening.com` (wpis DNS)
- [ ] Godziny otwarcia na Google Business (5:30 — 23:00 codziennie)
- [ ] Social media linki — prawdziwe URL-e zamiast placeholderów (`facebook.com/samtrening`, `instagram.com/samtrening`, `g.co/samtrening`)

---

**Właściciel TODO:** SAMtrening
**Ostatnia aktualizacja:** 23.04.2026
