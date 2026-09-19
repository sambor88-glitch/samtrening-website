# -*- coding: utf-8 -*-
"""Generuje docs/schema-json-ld.md z bloków JSON-LD w prototype/.

Bloki są przepisywane na slugi produkcyjne i pozbawiane BreadcrumbList,
który na produkcji generuje Yoast. Uruchamiaj po każdej zmianie schemy
w plikach prototypu, żeby dokument nie rozjechał się z kodem:

    python3 tools/schema-doc.py
"""
import re, json

ROOT = __import__('os').path.dirname(__import__('os').path.dirname(__import__('os').path.abspath(__file__))) + '/'
BASE = "https://www.samtrening.com/"
BIZ_ID = BASE + "#business"
BLOCK_RE = re.compile(r'<script type="application/ld\+json">(.*?)</script>', re.S)

SLUG_MAP = {
    BASE + "trener-personalny-stare-miasto-krakow/": BASE + "stare-miasto-krakow/",
    BASE + "trener-personalny-krakow-debniki/": BASE + "krakow-debniki/",
}

def nodes_of(path):
    src = open(ROOT + 'prototype/' + path, encoding='utf-8').read()
    data = json.loads(BLOCK_RE.search(src).group(1))
    return data.get("@graph", [data])

def pick(path, types):
    out = json.dumps([n for n in nodes_of(path) if n["@type"] in types], ensure_ascii=False)
    for old, new in SLUG_MAP.items():
        out = out.replace(old, new)
    return json.loads(out)

def block(nodes):
    payload = ({"@context": "https://schema.org", "@graph": nodes} if len(nodes) > 1
               else dict({"@context": "https://schema.org"}, **nodes[0]))
    return json.dumps(payload, ensure_ascii=False, indent=2)

oferta_url = BASE + "oferta-3-sesje-200-zl/"
oferta = {
    "@type": "Service",
    "@id": oferta_url + "#service",
    "name": "Trening personalny Kraków — 3 sesje za 200 zł",
    "serviceType": "Trening personalny 1:1",
    "description": "Pakiet startowy dla nowych klientów SAMtrening: trzy sesje treningu personalnego 1:1 w studiu przy Placu Na Groblach 23 w Krakowie za 200 zł.",
    "url": oferta_url,
    "provider": {"@type": "ExerciseGym", "@id": BIZ_ID, "name": "SAMtrening", "url": BASE},
    "areaServed": {"@type": "City", "name": "Kraków"},
    "offers": {
        "@type": "Offer",
        "name": "Pakiet startowy — 3 sesje treningu personalnego",
        "description": "Oferta wyłącznie dla nowych klientów, jednorazowo.",
        "priceCurrency": "PLN",
        "price": "200",
        "availability": "https://schema.org/InStock",
        "eligibleQuantity": {"@type": "QuantitativeValue", "value": 3, "unitText": "sesje"},
        "priceSpecification": {"@type": "UnitPriceSpecification", "priceCurrency": "PLN",
                               "price": "200", "unitText": "pakiet 3 sesji"}
    }
}

doc = f"""# Schema JSON-LD — bloki do wdrożenia na produkcji

Materiał do zadania **[SW112233-71](https://maciej-samborski.atlassian.net/browse/SW112233-71)**
— „Schema Service (+ FAQPage gdzie jest FAQ) na stronach usługowych".

Bloki poniżej to dokładnie to, co wdrożone w tym repozytorium (prototyp statyczny),
przepisane na **slugi produkcyjne** i okrojone o to, co na produkcji generuje już Yoast.
Dokument jest generowany z plików HTML w repozytorium — jeśli zmieniasz schema, zmieniaj ją w HTML.

## Zasady przyjęte w zadaniu

| Zasada | Jak zrealizowana |
|---|---|
| `provider` → ExerciseGym ze strony głównej | referencja po `@id`: `{BIZ_ID}` |
| `areaServed` Kraków | `City` na stronach usługowych, `Place` + `containedInPlace` na landingach, `Country` (Polska) na e-treningu |
| ceny zgodne z treścią i GBP | 200 zł sesja 1:1, 280 zł sesja 2:1, 200 zł sesja online — dokładnie jak w cennikach na stronach |
| FAQPage tylko tam, gdzie FAQ jest widoczne | Stare Miasto i Dębniki **nie mają** sekcji FAQ → tylko `Service` |
| bez `AggregateRating` | usunięty ze strony głównej (samowystawiona ocena 4,9/44) |

## Zanim wkleisz — cztery rzeczy do sprawdzenia na produkcji

1. **`@id` węzła firmy.** Snippety odwołują się do `{BIZ_ID}`.
   Sprawdź w źródle strony głównej, jakie `@id` ma tam realnie blok `ExerciseGym` — jeśli inne,
   podmień je we wszystkich `provider`, `about` i `worksFor`. Zły `@id` = wisząca referencja.
2. **Duplikaty z Yoast.** Yoast wysyła na każdej stronie `WebPage`, `WebSite`, `Organization`
   i `BreadcrumbList`. Dlatego snippety poniżej **nie zawierają `BreadcrumbList`** — nie dubluj go.
3. **Kod pocztowy.** Produkcja ma `31-101` i to jest wartość poprawna
   (Plac Na Groblach). W prototypie było `30-101` — poprawione.
4. **Pole `image` na stronie głównej.** W prototypie wskazuje na
   `wp-content/themes/sam/img/sam-logo1.png` — to ścieżka **starego** motywu („sam"),
   a produkcja działa na `samtrening-2026`. Sprawdź, czy ten plik nadal się otwiera;
   jeśli nie, podmień na logo z aktualnego motywu. Martwy `image` w danych firmy to realny błąd.

## Jak wdrożyć

Wariant zalecany — `functions.php` motywu `samtrening-2026`, warunkowo po ID strony:

```php
add_action( 'wp_head', function () {{
    $graphs = [
        6533 => 'stare-miasto-krakow',   // ID strony => nazwa pliku JSON
        // ...
    ];
    if ( ! is_page() || ! isset( $graphs[ get_the_ID() ] ) ) {{
        return;
    }}
    $file = get_stylesheet_directory() . '/schema/' . $graphs[ get_the_ID() ] . '.json';
    if ( is_readable( $file ) ) {{
        echo '<script type="application/ld+json">' . file_get_contents( $file ) . '</script>';
    }}
}}, 20 );
```

Pliki `.json` trzymaj w `wp-content/themes/samtrening-2026/schema/` — wtedy zmiana treści FAQ
to podmiana jednego pliku, bez dotykania PHP.

---

## 1. `/stare-miasto-krakow/` — Service

Bez FAQPage: strona nie ma widocznej sekcji FAQ. Jeśli FAQ powstanie, dopiero wtedy dokładamy blok.

```json
{block(pick("lokalizacje/stare-miasto-krakow.html", ("Service",)))}
```

## 2. `/krakow-debniki/` — Service

Slug do potwierdzenia — ticket dopuszcza, że na produkcji jest inny.

```json
{block(pick("lokalizacje/krakow-debniki.html", ("Service",)))}
```

## 3. `/treningi-personalne/` — Service + FAQPage (8 pytań)

```json
{block(pick("treningi-personalne.html", ("Service", "FAQPage")))}
```

## 4. `/e-trening/` — Service + SoftwareApplication + FAQPage (7 pytań)

```json
{block(pick("e-trening.html", ("Service", "SoftwareApplication", "FAQPage")))}
```

## 5. `/oferta-3-sesje-200-zl/` — Service + Offer

**Ten blok jest nowy — nie ma odpowiednika w repozytorium**, bo strona istnieje tylko na produkcji
(SW112233-41). Przed wklejeniem sprawdź w treści strony i uzupełnij:

- czy sesja trwa 60 minut (jeśli 75 min — patrz SW112233-92 — popraw `unitText`),
- czy oferta ma datę końcową → dodaj `"validThrough": "RRRR-MM-DD"` w `offers`,
- czy warunek „tylko nowi klienci" jest opisany na stronie — `description` musi się z nim zgadzać.

```json
{block([oferta])}
```

## 6. `/kontakt/` — ContactPage + FAQPage (6 pytań)

`ContactPage` odwołuje się do firmy po `@id`, nie powiela jej danych.

```json
{block(pick("kontakt.html", ("ContactPage", "FAQPage")))}
```

---

## Poza listą z SW112233-71

Trzy rzeczy zrobione przy okazji, bo strony miały widoczną treść bez odpowiadającej jej schemy.

### `/zdrowa-ciaza/` — Service + Person + FAQPage (6 pytań)

Trzecia strona ofertowa. Miała 3 z 6 widocznych pytań w schema.

```json
{block(pick("zdrowa-ciaza.html", ("Service", "Person", "FAQPage")))}
```

### Strona główna — FAQPage (5 pytań)

Wchodzi w [SW112233-29](https://maciej-samborski.atlassian.net/browse/SW112233-29).

```json
{block(pick("index.html", ("FAQPage",)))}
```

### Strona główna — katalog trzech programów

Do dopisania do istniejącego bloku `ExerciseGym` na produkcji (nie wklejaj całego węzła, tylko to pole):

```json
{json.dumps({"hasOfferCatalog": next(n for n in nodes_of("index.html") if n["@id"] == BIZ_ID)["hasOfferCatalog"]}, ensure_ascii=False, indent=2)}
```

---

## Definition of Done (z ticketu)

- [ ] Rich Results Test / Schema Validator bez błędów dla każdej z 6 stron
- [ ] `provider` wskazuje na istniejący węzeł firmy (sprawdzone w źródle strony głównej)
- [ ] Brak zdublowanego `BreadcrumbList` (Yoast vs własny blok)
- [ ] Ceny w schema = ceny w treści = ceny w Profilu Firmy w Google

Narzędzia: [Rich Results Test](https://search.google.com/test/rich-results),
[Schema Markup Validator](https://validator.schema.org/).
"""

open(ROOT + 'docs/schema-json-ld.md', 'w', encoding='utf-8').write(doc)
print("✔ docs/schema-json-ld.md —", len(doc), "znaków")
