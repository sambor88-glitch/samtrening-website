# Prototyp a produkcja — co z tym repozytorium

Notatka decyzyjna, wrzesień 2026. Do rozstrzygnięcia przez Maćka.

## Problem w jednym zdaniu

To repozytorium to statyczny prototyp z kwietnia 2026, a strona produkcyjna to WordPress
z motywem `samtrening-2026`, którego w gicie nie ma — więc każda zmiana robiona tutaj
musi zostać przepisana ręcznie, żeby trafić na stronę.

## Dowody rozjazdu (stan na 19.09.2026)

| Element | To repozytorium | Produkcja | Źródło |
|---|---|---|---|
| `/krakow-centrum/` | żyje: w menu i w `sitemap.xml` | przekierowanie 301 na Stare Miasto, strona jako szkic | SW112233-28, wykonane 16.09 |
| slug landingu | `/trener-personalny-stare-miasto-krakow/` | `/stare-miasto-krakow/` | SW112233-28 |
| slug landingu | `/trener-personalny-krakow-debniki/` | `/krakow-debniki/` | SW112233-71 |
| główne CTA | „Umów konsultację" (8 miejsc) | „Zacznij od 3 sesji za 200 zł" | SW112233-77, Gotowe |
| kod pocztowy | było `30-101` (poprawione 19.09) | `31-101` | SW112233-29, komentarz z 16.09 |
| typ firmy w schema | było `HealthAndBeautyBusiness` (poprawione 19.09) | `ExerciseGym` | SW112233-29 |
| `image` w schema | `themes/sam/img/sam-logo1.png` | motyw nazywa się `samtrening-2026` | do sprawdzenia |

Strony, które istnieją na produkcji, a których tu nie ma:
`/oferta-3-sesje-200-zl/`, `/trening-medyczny-krakow/`, `/analiza-skladu-ciala-krakow/`,
`/cennik/`, `/trening-dla-kobiet-krakow/`, `/trening-w-parze-krakow/`,
`/trening-40-plus-krakow/`, `/rezerwacja/`.

Wniosek: rozjazd nie jest kosmetyczny i rośnie z każdym tygodniem. Prototyp pokazuje dziś
stan świata sprzed pięciu miesięcy, a Jira i strona żyją dalej.

## Co to kosztuje

Każde zadanie dotykające treści lub schemy robimy dwa razy: raz w prototypie, raz w WordPressie.
Przy zadaniu takim jak SW112233-71 to około godziny nadmiarowej pracy i — groźniejsze —
ryzyko, że wersje się rozjadą. Dzisiejszy przykład: kod pocztowy był błędny tutaj przez pięć
miesięcy, mimo że produkcja miała poprawny.

## Trzy opcje

### A. Motyw produkcyjny do tego repozytorium (rekomendacja)

`theme/samtrening-2026/` obok obecnych plików, prototyp przeniesiony do `prototype/`,
README tłumaczy podział.

- **Koszt wdrożenia:** 2–3 h (eksport motywu z Lightsail, commit, opis procesu wdrożenia).
- **Zysk:** jedno miejsce, jeden przepływ PR-ów, działający już mirror na GitLaba, historia prototypu zostaje.
- **Ryzyko:** trzeba ustalić, jak zmiany z gita trafiają na serwer — dziś nie ma procesu ani środowiska staging.

### B. Osobne repozytorium na motyw

`samtrening-theme`, a to repozytorium oznaczone jako archiwum.

- **Koszt:** 3–4 h (nowe repo, uprawnienia, CI, mirror).
- **Zysk:** czysty podział archiwum i kodu żywego.
- **Koszt stały:** dwa repozytoria do pilnowania zamiast jednego.

### C. Zostawiamy jak jest

- **Koszt:** ~1 h nadmiarowej pracy przy każdym zadaniu treściowym lub schema, plus rosnące
  ryzyko rozjazdu danych (ceny, NAP, slugi).
- **Zysk:** zero pracy dzisiaj.

## Rekomendacja

**Opcja A.** Mirror na GitLaba już działa, CI jest skonfigurowane, a jedno repozytorium to
jeden nawyk zamiast dwóch. Prototyp warto zachować — jest dokumentacją designu — ale przestać
go traktować jak kod, który ktoś kiedyś wdroży.

Przy okazji warto rozstrzygnąć brak środowiska staging: dziś zmiany w motywie idą prosto
na produkcję, co przy stronie zbierającej leady jest niepotrzebnym ryzykiem. Najtańszy wariant
to druga instancja Lightsail w eu-central-1 z kopią bazy — rząd wielkości 10–20 USD miesięcznie
przy najmniejszym planie, do policzenia osobno.

## Decyzja (19.09.2026)

**Maciek wybrał opcję A: jedno repozytorium.** Struktura katalogów jest już gotowa
— `theme/` czeka na motyw, prototyp przeniesiony do `prototype/`, publikowanie poprawione.
Procedura wciągnięcia motywu: [`wdrozenie-motywu.md`](wdrozenie-motywu.md).

Sprawdzone przy okazji: prywatne repozytorium `sambor88-glitch/samtrening.com` jest puste,
więc kodu motywu nie ma dziś nigdzie w gicie.

Do rozstrzygnięcia zostaje:

- [ ] Kto i kiedy wyciąga motyw z Lightsaila (wymaga dostępu do serwera — SW112233-10)
- [ ] Czy robimy środowisko testowe, czy świadomie zostajemy przy wdrożeniach prosto na produkcję
- [ ] Czy repozytorium ma pozostać publiczne, gdy trafi do niego kod produkcyjny
