# Wdrożenie: dynamiczna lista wpisów na /blog/

Zamienia zahardkodowaną makietę `blog.html` (filtry + wyróżniony wpis + siatka)
na dynamiczne wpisy z WordPressa. Kod: [`wordpress/sw-blog-listing.php`](../wordpress/sw-blog-listing.php).

Klasy CSS i atrybuty `data-*` są identyczne jak w makiecie, więc **JS filtrów
w motywie działa bez zmian** — nie ruszamy `blog.html` ani skryptów.

---

## Krok 1 — kopia zapasowa

Przed czymkolwiek:

- **All-in-One WP Migration** → Export → Export to File, albo
- pobierz przez FTP/SFTP dwa pliki: `functions.php` i `inc/content/content-blog.php`
  z motywu `samtrening-2026` i zachowaj lokalnie.

Bez kopii nie ruszamy dalej — edycja `functions.php` przy błędzie składni kładzie
całą stronę (biały ekran).

## Krok 2 — wgraj kod do motywu

Dwie opcje, **B jest zalecana** (łatwiej aktualizować, `functions.php` nie puchnie):

**A) Wklejenie do functions.php**
Wklej całą zawartość `wordpress/sw-blog-listing.php` (bez pierwszej linii `<?php`)
na końcu `functions.php` motywu `samtrening-2026`.

**B) Osobny plik + require**
1. Skopiuj plik do motywu jako `inc/sw-blog-listing.php`.
2. Na końcu `functions.php` dodaj jedną linię:
   ```php
   require_once get_theme_file_path( 'inc/sw-blog-listing.php' );
   ```

## Krok 3 — podmień sekcje w szablonie

W `inc/content/content-blog.php` usuń trzy sekcje:

- `<section class="filters">` … `</section>`
- `<section class="featured">` … `</section>`
- `<section class="posts-section">` … `</section>` (razem z blokiem `#posts-empty` w środku)

W ich miejsce wstaw jedną linię:

```php
<?php samtrening_blog_listing(); ?>
```

Reszty pliku (hero, sekcja autorów, newsletter, stopka) nie ruszamy.

## Krok 4 — cache i weryfikacja

1. Wyczyść cache: **Cache Enabler** → Clear Cache, albo **WP Super Cache** → Delete Cache.
   Jeśli na stronie stoi CDN/Cloudflare — też purge.
2. Otwórz `/blog/` i sprawdź:
   - [ ] wyróżniony wpis = najnowszy wpis, z lime badge kategorii i „Najnowszy”
   - [ ] w siatce są wszystkie pozostałe wpisy
   - [ ] liczniki na pigułkach zgadzają się z liczbą kart po kliknięciu filtra
   - [ ] filtr autora działa (pigułki pojawiają się, gdy jest >1 autor)
   - [ ] „Zresetuj filtry” wraca do pełnej listy
   - [ ] konsola przeglądarki bez błędów JS
3. Jeśli coś się wysypie — usuń dodany kod z `functions.php` i przywróć
   `content-blog.php` z kopii z kroku 1.

---

## Konfiguracja po stronie WordPressa

**Slugi kategorii** muszą się zgadzać z makietą, bo CSS koloruje tło okładki
per kategoria (`.post[data-category="..."] .post__image`):

`treningi`, `zdrowa-ciaza`, `dieta`, `e-trening`, `zdrowie`, `sport`

Inny slug = karta dostanie neutralny gradient. Filtrowanie zadziała i tak.

**Kategoria „Bez kategorii”** jest pomijana — każdy wpis powinien mieć
przypisaną właściwą kategorię, inaczej pigułka kategorii będzie pusta.

**Okładki wpisów.** Wpis bez obrazka wyróżniającego dostaje zdjęcie zastępcze
`/wp-content/uploads/2026/05/studio-1200x800.jpg`. Jeśli tego pliku nie ma na
serwerze, wyłącz fallback — pokaże się placeholder z makiety zamiast zepsutego
obrazka i 404 w logach:

```php
add_filter( 'sw_blog_fallback_image', '__return_empty_string' );
```

**Zajawki.** Lista używa `get_the_excerpt()` — jeśli wpis nie ma ręcznej zajawki,
WordPress utnie początek treści. Dla wyróżnionego wpisu warto wpisać zajawkę
ręcznie (pole „Zajawka” w edytorze).

**Limit wpisów** to 60 (`posts_per_page`). Przy większej liczbie wpisów trzeba
dodać paginację albo doładowywanie — dziś wszystko idzie w jednym zapytaniu.

**Sticky posts nie działają** — „Wyróżniony” to zawsze najnowszy wpis.
WP_Query przestawia sticky tylko w zapytaniu głównym, a to jest zapytanie poboczne.

---

## Poprawki wprowadzone względem pierwszej wersji snippetu

Wszystkie zweryfikowane przez render markupu na atrapach funkcji WP:

| # | Problem | Skutek | Poprawka |
|---|---------|--------|----------|
| 1 | Liczniki na pigułkach obejmowały wyróżniony wpis, którego nie ma w siatce | „Wszystkie 5”, a po kliknięciu 4 karty; „Zdrowa ciąża 2” → 1 karta | Liczniki liczone tylko z wpisów w siatce |
| 2 | `str_word_count()` nie rozumie polskich znaków (ł, ą, ż) — rozbija wyrazy | Czas czytania zawyżony o ~25% (5 min zamiast 4) | Liczenie słów przez `preg_match_all('/\p{L}+/u')` |
| 3 | Kategoria wyróżnionego wpisu bez klasy `tag`, separator wklejony w ten sam `<span>` | Utrata lime badge i odstępów z makiety | `<span class="tag">` + osobny `<span class="dot">●</span>` |
| 4 | `esc_html( get_the_excerpt() )` | Podwójne escapowanie encji — polskie cudzysłowy „ ” wychodzą jako `&#8222;` | `wp_kses_post()` |
| 5 | Gałąź „brak wpisów” bez `#posts-empty`, `#posts-grid`, `#reset-filters` | `getElementById(...).addEventListener` → TypeError, cały JS bloga przestaje działać | Te same id co w makiecie (przycisk `hidden`) |
| 6 | Zdjęcie zastępcze zahardkodowane | 404 i zepsuty obrazek, jeśli pliku nie ma | Filtr `sw_blog_fallback_image`, fallback do placeholdera z makiety |
| 7 | `id="featured-title"` na eyebrow zamiast na `<h2>` | `aria-labelledby` wskazywał „Wyróżnione”, nie tytuł wpisu | id wrócił na `<h2>`, jak w makiecie |
