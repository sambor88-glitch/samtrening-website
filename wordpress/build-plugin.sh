#!/usr/bin/env bash
# Buduje wtyczkę WordPressa do wgrania przez WP-admin (Wtyczki -> Dodaj wtyczkę -> Wyślij wtyczkę na serwer).
# Renderer jest jeden — wordpress/sw-blog-listing.php — i jest kopiowany do wtyczki przy budowaniu.
set -euo pipefail

HERE="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
SLUG="samtrening-blog-listing"
BUILD="$HERE/dist/$SLUG"
ZIP="$HERE/dist/$SLUG.zip"

rm -rf "$HERE/dist"
mkdir -p "$BUILD/inc"

cp "$HERE/plugin/$SLUG.php" "$BUILD/$SLUG.php"
cp "$HERE/sw-blog-listing.php" "$BUILD/inc/sw-blog-listing.php"

cat > "$BUILD/INSTRUKCJA.txt" <<'TXTEOF'
SAMtrening — lista wpisów na /blog/

INSTALACJA (3 kliknięcia, bez edycji plików motywu)
1. WP-admin -> Wtyczki -> Dodaj wtyczkę -> Wyślij wtyczkę na serwer
2. Wybierz samtrening-blog-listing.zip -> Zainstaluj teraz -> Włącz wtyczkę
3. Wyczyść cache (Cache Enabler / WP Super Cache) i otwórz /blog/

COFNIĘCIE
Wtyczki -> Wyłącz. Strona wraca do stanu przed instalacją — wtyczka nie
zmienia ani jednego pliku motywu i nie dotyka bazy danych.

CO ROBI
Na stronie /blog/ podmienia trzy statyczne sekcje makiety (filtry,
wyróżniony wpis, siatka wpisów) na wpisy pobierane z WordPressa.
Klasy CSS i atrybuty data-* są te same co w makiecie, więc JS filtrów
w motywie działa bez zmian.

GDY PODMIANA SIĘ NIE UDA
Strona zostaje bez zmian (zasada: wszystko albo nic), a w kokpicie
pojawia się żółty komunikat z powodem. Nic się nie psuje.

WERSJA DOCELOWA (czystsza)
Zamiast podmiany przez bufor wyjścia można wywołać funkcję wprost
w szablonie inc/content/content-blog.php:
    <?php samtrening_blog_listing(); ?>
Wtyczka rozpoznaje, że szablon już renderuje listę, i sama się wycofuje —
można zostawić ją włączoną.

FILTRY DLA PROGRAMISTY
  samtrening_blog_autoswap          (bool)  wyłącza podmianę przez bufor
  samtrening_blog_is_listing_page   (bool)  własne wykrywanie strony /blog/
  sw_blog_fallback_image            (string) zdjęcie zastępcze; '' = placeholder
TXTEOF

( cd "$HERE/dist" && zip -qr "$SLUG.zip" "$SLUG" )
rm -rf "$BUILD"

echo "Gotowe: $ZIP"
unzip -l "$ZIP"
