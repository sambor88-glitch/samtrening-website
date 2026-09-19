# Schema JSON-LD — bloki do wdrożenia na produkcji

Materiał do zadania **[SW112233-71](https://maciej-samborski.atlassian.net/browse/SW112233-71)**
— „Schema Service (+ FAQPage gdzie jest FAQ) na stronach usługowych".

Bloki poniżej to dokładnie to, co wdrożone w tym repozytorium (prototyp statyczny),
przepisane na **slugi produkcyjne** i okrojone o to, co na produkcji generuje już Yoast.
Dokument jest generowany z plików HTML w repozytorium — jeśli zmieniasz schema, zmieniaj ją w HTML.

## Zasady przyjęte w zadaniu

| Zasada | Jak zrealizowana |
|---|---|
| `provider` → ExerciseGym ze strony głównej | referencja po `@id`: `https://www.samtrening.com/#business` |
| `areaServed` Kraków | `City` na stronach usługowych, `Place` + `containedInPlace` na landingach, `Country` (Polska) na e-treningu |
| ceny zgodne z treścią i GBP | 200 zł sesja 1:1, 280 zł sesja 2:1, 200 zł sesja online — dokładnie jak w cennikach na stronach |
| FAQPage tylko tam, gdzie FAQ jest widoczne | Stare Miasto i Dębniki **nie mają** sekcji FAQ → tylko `Service` |
| bez `AggregateRating` | usunięty ze strony głównej (samowystawiona ocena 4,9/44) |

## Zanim wkleisz — cztery rzeczy do sprawdzenia na produkcji

1. **`@id` węzła firmy.** Snippety odwołują się do `https://www.samtrening.com/#business`.
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
add_action( 'wp_head', function () {
    $graphs = [
        6533 => 'stare-miasto-krakow',   // ID strony => nazwa pliku JSON
        // ...
    ];
    if ( ! is_page() || ! isset( $graphs[ get_the_ID() ] ) ) {
        return;
    }
    $file = get_stylesheet_directory() . '/schema/' . $graphs[ get_the_ID() ] . '.json';
    if ( is_readable( $file ) ) {
        echo '<script type="application/ld+json">' . file_get_contents( $file ) . '</script>';
    }
}, 20 );
```

Pliki `.json` trzymaj w `wp-content/themes/samtrening-2026/schema/` — wtedy zmiana treści FAQ
to podmiana jednego pliku, bez dotykania PHP.

---

## 1. `/stare-miasto-krakow/` — Service

Bez FAQPage: strona nie ma widocznej sekcji FAQ. Jeśli FAQ powstanie, dopiero wtedy dokładamy blok.

```json
{
  "@context": "https://schema.org",
  "@type": "Service",
  "@id": "https://www.samtrening.com/stare-miasto-krakow/#service",
  "name": "Trener personalny Stare Miasto Kraków",
  "serviceType": "Trening personalny 1:1",
  "description": "Studio treningu personalnego na Starym Mieście w Krakowie. 1:1 sesje na Placu Na Groblach, pięć minut od Wawelu.",
  "url": "https://www.samtrening.com/stare-miasto-krakow/",
  "provider": {
    "@type": "ExerciseGym",
    "@id": "https://www.samtrening.com/#business",
    "name": "SAMtrening",
    "url": "https://www.samtrening.com/"
  },
  "areaServed": {
    "@type": "Place",
    "name": "Stare Miasto, Kraków",
    "containedInPlace": {
      "@type": "City",
      "name": "Kraków"
    }
  },
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Programy dostępne na Starym Mieście",
    "itemListElement": [
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/treningi-personalne/#service",
          "name": "Trening personalny 1:1"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/zdrowa-ciaza/#service",
          "name": "Trening w ciąży i po porodzie"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/e-trening/#service",
          "name": "E-trening personalny online"
        }
      }
    ]
  }
}
```

## 2. `/krakow-debniki/` — Service

Slug do potwierdzenia — ticket dopuszcza, że na produkcji jest inny.

```json
{
  "@context": "https://schema.org",
  "@type": "Service",
  "@id": "https://www.samtrening.com/krakow-debniki/#service",
  "name": "Trener personalny Kraków Dębniki",
  "serviceType": "Trening personalny 1:1",
  "description": "Studio treningu personalnego dla mieszkańców Dębnik. Pieszo lub rowerem przez most Grunwaldzki w 7 minut.",
  "url": "https://www.samtrening.com/krakow-debniki/",
  "provider": {
    "@type": "ExerciseGym",
    "@id": "https://www.samtrening.com/#business",
    "name": "SAMtrening",
    "url": "https://www.samtrening.com/"
  },
  "areaServed": {
    "@type": "Place",
    "name": "Dębniki, Kraków",
    "containedInPlace": {
      "@type": "City",
      "name": "Kraków"
    }
  },
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Programy dostępne dla mieszkańców Dębnik",
    "itemListElement": [
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/treningi-personalne/#service",
          "name": "Trening personalny 1:1"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/zdrowa-ciaza/#service",
          "name": "Trening w ciąży i po porodzie"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/e-trening/#service",
          "name": "E-trening personalny online"
        }
      }
    ]
  }
}
```

## 3. `/treningi-personalne/` — Service + FAQPage (8 pytań)

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "@id": "https://www.samtrening.com/treningi-personalne/#service",
      "name": "Trening personalny w Krakowie",
      "serviceType": "Trening personalny 1:1",
      "description": "Kameralne studio 1:1 w centrum Krakowa, Plac Na Groblach 23. Trening personalny z byłymi sportowcami wyczynowymi.",
      "url": "https://www.samtrening.com/treningi-personalne/",
      "provider": {
        "@type": "ExerciseGym",
        "@id": "https://www.samtrening.com/#business",
        "name": "SAMtrening",
        "url": "https://www.samtrening.com/"
      },
      "areaServed": {
        "@type": "City",
        "name": "Kraków"
      },
      "offers": [
        {
          "@type": "Offer",
          "name": "Sesja treningu personalnego 1:1",
          "priceCurrency": "PLN",
          "price": "200",
          "availability": "https://schema.org/InStock",
          "priceSpecification": {
            "@type": "UnitPriceSpecification",
            "priceCurrency": "PLN",
            "price": "200",
            "unitText": "sesja 60 min",
            "minPrice": "200"
          }
        },
        {
          "@type": "Offer",
          "name": "Sesja treningu personalnego 2:1 (w parze)",
          "priceCurrency": "PLN",
          "price": "280",
          "availability": "https://schema.org/InStock",
          "priceSpecification": {
            "@type": "UnitPriceSpecification",
            "priceCurrency": "PLN",
            "price": "280",
            "unitText": "sesja 60 min dla dwóch osób",
            "minPrice": "280"
          }
        }
      ]
    },
    {
      "@type": "FAQPage",
      "@id": "https://www.samtrening.com/treningi-personalne/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Ile kosztuje trening w SAMtrening i dlaczego?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Od 200 zł za sesję 1:1. Cenimy sobie doświadczenie, które zdobywaliśmy latami na arenach sportowych — Maciek na Mistrzostwach Świata, Kasia w Kadrze Polski, Bartek w sztukach walki. Cenimy prywatność studia, w którym jesteś jedynym klientem w sali. Cenimy kontakt między sesjami i monitorowanie Twoich postępów co 4 tygodnie. Nie sprzedajemy pakietów ani karnetów — płacisz wyłącznie za sesje, które rzeczywiście się odbyły."
          }
        },
        {
          "@type": "Question",
          "name": "Czy muszę być w formie zanim przyjdę?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Nie. Połowa naszych klientów zaczyna od zera lub po wieloletniej przerwie. Pierwsza sesja jest dobrana do twojego aktualnego poziomu — nie ma „za słabego\" startu. Jeśli kiedykolwiek miałeś wątpliwość, czy „już wypada zacząć\", to znak, że pora zacząć właśnie teraz."
          }
        },
        {
          "@type": "Question",
          "name": "Jak często trzeba trenować, żeby zobaczyć efekty?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Dwa razy w tygodniu to absolutne minimum. Standardowo polecamy trzy sesje tygodniowo — tyle wystarczy większości klientów do osiągnięcia wymiernych efektów w ciągu 12 tygodni. Jeden trening tygodniowo to za mało, żeby cokolwiek się zmieniło — wtedy warto rozważyć alternatywnie e-trening online z częstszym kontaktem przez aplikację."
          }
        },
        {
          "@type": "Question",
          "name": "Czy mogę trenować rano przed pracą?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Studio jest otwarte od 5:30 — po sesji o 6:00 jesteś świeży, zregenerowany i gotowy na cały dzień pracy o 8:00. To nasz najpopularniejszy slot wśród przedsiębiorców i lekarzy z centrum Krakowa."
          }
        },
        {
          "@type": "Question",
          "name": "Czy oferujecie treningi w parach?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Format 2:1 (dwie osoby z jednym trenerem) kosztuje 40% więcej niż sesja indywidualna — czyli od 280 zł, co daje 140 zł od osoby. Polecamy parom, rodzeństwom, znajomym, a także rodzicom z nastoletnimi dziećmi o zbliżonym poziomie i celach. Wspólne trenowanie zwiększa systematyczność."
          }
        },
        {
          "@type": "Question",
          "name": "Co odróżnia SAMtrening od siłowni z trenerem?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Trzy rzeczy. Po pierwsze — formuła 1:1 w prywatnym studio, nie na ogólnodostępnej siłowni z muzyką, lustrami i 30 osobami obok. Po drugie — trenerzy to byli sportowcy wyczynowi z medalami Mistrzostw Polski, nie absolwenci weekendowych kursów. Po trzecie — pomiary, plan treningowy i stały kontakt między sesjami są wliczone w cenę, nie jako droga „dopłata premium\"."
          }
        },
        {
          "@type": "Question",
          "name": "Od jakiego wieku prowadzicie treningi dla dzieci?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Od 10 roku życia. Pracujemy z młodzieżą zaczynającą przygodę z siłownią, młodymi sportowcami przygotowującymi się pod konkretną dyscyplinę oraz z dziećmi z wadami postawy nabytymi w szkole. Treningi dobieramy do etapu rozwoju — ogólnorozwojowe, motoryczne, biegowe, korekcyjne lub siłowe (z naciskiem na bezpieczeństwo i naukę poprawnej techniki, nie na obciążenia). Pierwsza konsultacja zawsze z udziałem rodzica."
          }
        },
        {
          "@type": "Question",
          "name": "Gdzie dokładnie znajduje się studio?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Plac Na Groblach 23 w Krakowie — pięć minut pieszo od Wawelu, dziesięć minut od Rynku Głównego, siedem minut pieszo z Dębnik przez most Grunwaldzki. Najbliższy przystanek MPK: „Filharmonia\". W pobliżu kilka parkingów (Plac Na Groblach, Powiśle, parking podziemny pod ICE Kraków)."
          }
        }
      ]
    }
  ]
}
```

## 4. `/e-trening/` — Service + SoftwareApplication + FAQPage (7 pytań)

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "@id": "https://www.samtrening.com/e-trening/#service",
      "name": "E-trening personalny online",
      "serviceType": "Trening personalny online",
      "description": "E-trening z trenerem personalnym z Krakowa. Własna platforma SAMtrening z planem, chatem, pomiarami i sesjami online.",
      "url": "https://www.samtrening.com/e-trening/",
      "provider": {
        "@type": "ExerciseGym",
        "@id": "https://www.samtrening.com/#business",
        "name": "SAMtrening",
        "url": "https://www.samtrening.com/"
      },
      "areaServed": {
        "@type": "Country",
        "name": "Polska"
      },
      "availableChannel": {
        "@type": "ServiceChannel",
        "name": "Platforma SAMtrening",
        "serviceUrl": "https://app.samtrening.com"
      },
      "offers": {
        "@type": "Offer",
        "name": "Sesja online 1:1",
        "priceCurrency": "PLN",
        "price": "200",
        "availability": "https://schema.org/InStock",
        "priceSpecification": {
          "@type": "UnitPriceSpecification",
          "priceCurrency": "PLN",
          "price": "200",
          "unitText": "sesja online 60 min"
        }
      }
    },
    {
      "@type": "SoftwareApplication",
      "@id": "https://app.samtrening.com/#platform",
      "name": "Platforma SAMtrening",
      "applicationCategory": "HealthApplication",
      "operatingSystem": "Web (przeglądarka)",
      "url": "https://app.samtrening.com",
      "publisher": {
        "@id": "https://www.samtrening.com/#business"
      }
    },
    {
      "@type": "FAQPage",
      "@id": "https://www.samtrening.com/e-trening/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Czy potrzebuję dużo sprzętu w domu, żeby zacząć?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Nie. Na pierwszej konsultacji pytamy, co masz pod ręką, i układamy plan pod twoje realne możliwości. Większość naszych klientów e-treningu ma: matę, gumy oporowe, parę hantli i krzesło. To wystarczy, żeby pracować systematycznie przez pierwsze miesiące. Jeśli masz w domu siłownię — tym lepiej, ale to nie jest warunek wejściowy."
          }
        },
        {
          "@type": "Question",
          "name": "Jak wygląda sesja online — krok po kroku?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Logujesz się na Zoom lub Google Meet w umówionym przez platformę terminie. Trener widzi cię przez kamerę z laptopa lub telefonu ustawionego na statywie. Prowadzimy sesję dokładnie jak stacjonarnie — rozgrzewka, część główna, wyciszenie — tylko komunikacja odbywa się przez kamerę i mikrofon. Pełna sesja trwa 60 minut."
          }
        },
        {
          "@type": "Question",
          "name": "Ile razy w tygodniu trzeba trenować, żeby zobaczyć efekty?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Polecamy 2–3 sesje personalnie online z trenerem plus 1–2 samodzielne według planu w platformie. Łącznie 3–5 jednostek treningowych tygodniowo — tyle wystarczy, żeby zobaczyć wymierne efekty w ciągu pierwszych 12 tygodni. Jedna sesja tygodniowo to zwykle za mało, żeby cokolwiek realnie się zmieniło."
          }
        },
        {
          "@type": "Question",
          "name": "Czy można łączyć e-trening z treningiem stacjonarnym w studio?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak — i wielu naszych klientów tak właśnie pracuje. Na przykład dwa razy stacjonarnie w tygodniach, kiedy są w Krakowie, i online, kiedy wyjeżdżają służbowo. Plan, historia treningowa i pomiary są spójne — wszystko prowadzone na jednej platformie, przez tego samego trenera."
          }
        },
        {
          "@type": "Question",
          "name": "Co się stanie, jeśli internet zerwie się w trakcie sesji?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Wrócisz do połączenia — jeśli nie uda się nawiązać go w sensownym czasie, trener przeniesie pozostały czas na następną sesję. Nie ma kary za problemy techniczne, które nie są twoją winą."
          }
        },
        {
          "@type": "Question",
          "name": "Czy e-trening jest dla mnie, jeśli nigdy nie trenowałem systematycznie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Połowa naszych klientów e-treningu zaczyna od zera lub po wieloletniej przerwie. Na pierwszej sesji dostosowujemy intensywność do twojego aktualnego poziomu, a plan rośnie razem z tobą — krok po kroku. Paradoksalnie trening online bywa w takich sytuacjach wygodniejszy niż siłownia — nie krępuje cię obecność innych osób ani poczucie, że „wszyscy patrzą\"."
          }
        },
        {
          "@type": "Question",
          "name": "Czy rozliczenia naprawdę są w 100% online?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Po każdej odbytej sesji płacisz szybkim przelewem bezpośrednio z poziomu platformy SAMtrening. Bez fakturowania zaległych sesji, bez gotówki, bez karnetów do przedłużania. W panelu masz widok wszystkich płatności, sesji i dokumentów."
          }
        }
      ]
    }
  ]
}
```

## 5. `/oferta-3-sesje-200-zl/` — Service + Offer

**Ten blok jest nowy — nie ma odpowiednika w repozytorium**, bo strona istnieje tylko na produkcji
(SW112233-41). Przed wklejeniem sprawdź w treści strony i uzupełnij:

- czy sesja trwa 60 minut (jeśli 75 min — patrz SW112233-92 — popraw `unitText`),
- czy oferta ma datę końcową → dodaj `"validThrough": "RRRR-MM-DD"` w `offers`,
- czy warunek „tylko nowi klienci" jest opisany na stronie — `description` musi się z nim zgadzać.

```json
{
  "@context": "https://schema.org",
  "@type": "Service",
  "@id": "https://www.samtrening.com/oferta-3-sesje-200-zl/#service",
  "name": "Trening personalny Kraków — 3 sesje za 200 zł",
  "serviceType": "Trening personalny 1:1",
  "description": "Pakiet startowy dla nowych klientów SAMtrening: trzy sesje treningu personalnego 1:1 w studiu przy Placu Na Groblach 23 w Krakowie za 200 zł.",
  "url": "https://www.samtrening.com/oferta-3-sesje-200-zl/",
  "provider": {
    "@type": "ExerciseGym",
    "@id": "https://www.samtrening.com/#business",
    "name": "SAMtrening",
    "url": "https://www.samtrening.com/"
  },
  "areaServed": {
    "@type": "City",
    "name": "Kraków"
  },
  "offers": {
    "@type": "Offer",
    "name": "Pakiet startowy — 3 sesje treningu personalnego",
    "description": "Oferta wyłącznie dla nowych klientów, jednorazowo.",
    "priceCurrency": "PLN",
    "price": "200",
    "availability": "https://schema.org/InStock",
    "eligibleQuantity": {
      "@type": "QuantitativeValue",
      "value": 3,
      "unitText": "sesje"
    },
    "priceSpecification": {
      "@type": "UnitPriceSpecification",
      "priceCurrency": "PLN",
      "price": "200",
      "unitText": "pakiet 3 sesji"
    }
  }
}
```

## 6. `/kontakt/` — ContactPage + FAQPage (6 pytań)

`ContactPage` odwołuje się do firmy po `@id`, nie powiela jej danych.

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "ContactPage",
      "@id": "https://www.samtrening.com/kontakt/#contact",
      "name": "Kontakt — SAMtrening Kraków",
      "url": "https://www.samtrening.com/kontakt/",
      "about": {
        "@id": "https://www.samtrening.com/#business"
      },
      "mainEntity": {
        "@id": "https://www.samtrening.com/#business"
      }
    },
    {
      "@type": "FAQPage",
      "@id": "https://www.samtrening.com/kontakt/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Jak szybko odpowiadacie na formularze i maile?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "W dni robocze (pon–pt, 8:00–20:00) odpowiadamy zwykle w ciągu 2–4 godzin. Wiadomości wysłane wieczorem, w nocy lub w weekendy — obsługujemy następnego ranka. Jeśli temat jest pilny, zadzwoń bezpośrednio na +48 728 385 203 — w godzinach pracy studia (5:30–23:00) prawie zawsze ktoś odbiera."
          }
        },
        {
          "@type": "Question",
          "name": "Czy mogę przyjść do studia bez umawiania się?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, ale warto wcześniej zadzwonić. W studio w danym momencie zwykle trwa sesja 1:1, więc trenerzy są zajęci. Jeśli chcesz po prostu zobaczyć przestrzeń — napisz lub zadzwoń, umówimy się w oknie między sesjami. Pierwsza konsultacja (75 minut) i tak powinna być umówiona z wyprzedzeniem."
          }
        },
        {
          "@type": "Question",
          "name": "Czy można dzwonić bardzo wcześnie rano lub późno wieczorem?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Studio pracuje od 5:30 do 23:00, więc w tych godzinach telefon zwykle odbieramy. Najlepsze okno na spokojną rozmowę: między 10:00 a 12:00 lub między 15:00 a 17:00 — wtedy rotacja klientów jest najmniejsza. Ranne i wieczorne godziny są najbardziej zajęte treningami."
          }
        },
        {
          "@type": "Question",
          "name": "Co dalej po wysłaniu formularza?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Oddzwaniamy lub piszemy w 2–4 godziny. W pierwszej wiadomości prosimy zwykle o doprecyzowanie kilku szczegółów (godziny dogodne do treningów, czy już kiedyś trenowałeś, czy masz konkretne dolegliwości). Potem umawiamy konsultację — w studio lub online. Bez zobowiązań."
          }
        },
        {
          "@type": "Question",
          "name": "Jestem już Waszym klientem — gdzie się zalogować do platformy?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Do platformy SAMtrening logujesz się pod adresem app.samtrening.com — tam znajdziesz swój plan, chat z trenerem, pomiary i historię sesji. Link widoczny też w górnym menu każdej strony (przycisk „Zaloguj się\"). Jeśli zapomniałeś/aś hasła lub masz problem z dostępem — napisz na biuro@samtrening.com."
          }
        },
        {
          "@type": "Question",
          "name": "Czy dzwoniąc dostanę od razu odpowiedź o cenach?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Ceny startują od 200 zł/h z trenerem za sesję 1:1, od 280 zł za sesję 2:1, sztywno 200 zł za sesję online. Kwota zależy od godziny treningu, częstotliwości oraz trenera, z którym chcesz trenować."
          }
        }
      ]
    }
  ]
}
```

---

## Poza listą z SW112233-71

Trzy rzeczy zrobione przy okazji, bo strony miały widoczną treść bez odpowiadającej jej schemy.

### `/zdrowa-ciaza/` — Service + Person + FAQPage (6 pytań)

Trzecia strona ofertowa. Miała 3 z 6 widocznych pytań w schema.

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Service",
      "@id": "https://www.samtrening.com/zdrowa-ciaza/#service",
      "name": "Trening w ciąży i po porodzie w Krakowie",
      "serviceType": "Trening okołoporodowy 1:1",
      "description": "Bezpieczny trening personalny dla kobiet w ciąży, po porodzie i planujących ciążę. Studio 1:1 na Placu Na Groblach w centrum Krakowa.",
      "url": "https://www.samtrening.com/zdrowa-ciaza/",
      "provider": {
        "@type": "ExerciseGym",
        "@id": "https://www.samtrening.com/#business",
        "name": "SAMtrening",
        "url": "https://www.samtrening.com/"
      },
      "areaServed": {
        "@type": "City",
        "name": "Kraków"
      },
      "offers": [
        {
          "@type": "Offer",
          "name": "Sesja treningu okołoporodowego 1:1",
          "priceCurrency": "PLN",
          "price": "200",
          "availability": "https://schema.org/InStock",
          "priceSpecification": {
            "@type": "UnitPriceSpecification",
            "priceCurrency": "PLN",
            "price": "200",
            "unitText": "sesja 60 min",
            "minPrice": "200"
          }
        },
        {
          "@type": "Offer",
          "name": "Sesja 2:1 — z partnerem lub koleżanką",
          "priceCurrency": "PLN",
          "price": "280",
          "availability": "https://schema.org/InStock",
          "priceSpecification": {
            "@type": "UnitPriceSpecification",
            "priceCurrency": "PLN",
            "price": "280",
            "unitText": "sesja 60 min dla dwóch osób",
            "minPrice": "280"
          }
        }
      ]
    },
    {
      "@type": "Person",
      "@id": "https://www.samtrening.com/zespol/#kasia",
      "name": "Katarzyna Samborska",
      "jobTitle": "Trenerka personalna, specjalistka treningu okołoporodowego",
      "worksFor": {
        "@id": "https://www.samtrening.com/#business"
      },
      "alumniOf": "Akademia Wychowania Fizycznego w Krakowie",
      "description": "Absolwentka AWF Kraków, reprezentantka Kadry Polski na 400 m przez płotki, 13+ lat jako trenerka specjalizująca się w treningu kobiet w ciąży, po porodzie i pracy z mięśniami dna miednicy."
    },
    {
      "@type": "FAQPage",
      "@id": "https://www.samtrening.com/zdrowa-ciaza/#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Czy można ćwiczyć w 8. miesiącu ciąży?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, pod warunkiem braku przeciwwskazań lekarskich i przy odpowiednio dobranej intensywności. W 8. miesiącu pracujemy głównie nad komfortem, mobilnością bioder i oddechem przygotowującym do porodu. Trening trwa krócej, ma charakter łagodny, a wiele ćwiczeń wykonujemy w pozycjach odciążających kręgosłup — na piłce, w klęku podpartym, w staniu z podparciem. Część moich klientek trenuje ze mną do samego porodu — ostatnia sesja na 3 dni przed porodem zdarzyła się już kilka razy."
          }
        },
        {
          "@type": "Question",
          "name": "Kiedy można wrócić do ćwiczeń po porodzie?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Standardowo po 6-tygodniowej kontroli po porodzie naturalnym i po 12 tygodniach po cesarskim cięciu — zawsze po konsultacji z ginekologiem lub położną. Pierwsze sesje to ocena stanu mięśni dna miednicy, rozstępu prostego brzucha i pracy oddechu. Nie zaczynamy od biegania, pompek czy brzuszków — to droga do kontuzji. Pełna odbudowa trwa zwykle 12 tygodni od startu."
          }
        },
        {
          "@type": "Question",
          "name": "Czy mogę ćwiczyć, jeśli nigdy wcześniej nie trenowałam?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak. Wiele kobiet w SAMtrening zaczyna regularną aktywność dopiero w ciąży. Dostosowujemy intensywność do twojego aktualnego stanu — nie ma „za słabego\" startu. Ważne tylko, żeby lekarz prowadzący nie miał przeciwwskazań. Drugi trymestr jest idealnym momentem, żeby zacząć, jeśli wcześniej nie ćwiczyłaś systematycznie."
          }
        },
        {
          "@type": "Question",
          "name": "Czy pracujesz z kobietami po cesarskim cięciu?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Tak, to jedna z większych grup moich klientek w Krakowie. Po cesarskim cięciu pracujemy wolniej — zaczynamy od oddechu, delikatnej aktywacji mięśni głębokich i mobilizacji blizny. Powrót do pełnej aktywności zajmuje zwykle 4–6 miesięcy, czasem dłużej. Nie ścigamy się z kalendarzem — priorytetem jest bezpieczne, trwałe zdrowie."
          }
        },
        {
          "@type": "Question",
          "name": "Czy sesja jest bezpieczna przy ciąży zagrożonej?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Przy ciąży z zaleceniem leżenia, łożysku przodującym, niewydolności szyjki lub innych przeciwwskazaniach medycznych treningu nie prowadzę — w takich sytuacjach zawsze przekierowuję do fizjoterapeutki uroginekologicznej lub proponuję łagodne ćwiczenia oddechowe pod kontrolą lekarza prowadzącego. Bezpieczeństwo twoje i dziecka jest zawsze priorytetem, nawet jeśli oznacza to wstrzymanie treningu na kilka miesięcy."
          }
        },
        {
          "@type": "Question",
          "name": "Gdzie dokładnie znajduje się studio?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Plac Na Groblach 23 w Krakowie — pięć minut pieszo od Wawelu, dziesięć minut od Rynku Głównego, siedem minut pieszo z Dębnik przez most Grunwaldzki. Najbliższy przystanek MPK: „Filharmonia\". W pobliżu kilka parkingów (Plac Na Groblach, Powiśle, parking podziemny pod ICE Kraków) — co bywa ważne w zaawansowanej ciąży."
          }
        }
      ]
    }
  ]
}
```

### Strona główna — FAQPage (5 pytań)

Wchodzi w [SW112233-29](https://maciej-samborski.atlassian.net/browse/SW112233-29).

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "@id": "https://www.samtrening.com/#faq",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Ile kosztuje trening personalny?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Jedna sesja 1:1 kosztuje od 200 zł za 60 minut. Nie sprzedajemy pakietów ani abonamentów — płacisz za odbyte sesje. Szczegółowy cennik wszystkich usług znajdziesz na stronach poszczególnych ofert."
      }
    },
    {
      "@type": "Question",
      "name": "Czy mogę trenować rano przed pracą?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Tak. Studio otwarte jest codziennie od 5:30 do 23:00, również w weekendy. Najpopularniejsze sloty wśród przedsiębiorców to 6:00, 6:30 i 7:00 — jesteś po treningu zanim większość miasta zaczyna pracę. Prysznic w studio dostępny."
      }
    },
    {
      "@type": "Question",
      "name": "Czy prowadzicie treningi w ciąży?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Tak. Kasia Samborska specjalizuje się w treningu kobiet w ciąży, po porodzie i w połogu. Ma 13 lat doświadczenia i wykształcenie fizjoterapeutyczne w zakresie dna miednicy. Szczegółowe informacje znajdziesz na stronie Zdrowa Ciąża."
      }
    },
    {
      "@type": "Question",
      "name": "Co jeśli muszę odwołać trening?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Bez problemu. Odwołanie na 24 godziny przed usługą jest bezpłatne — wystarczy SMS lub telefon. Wiemy, że grafiki zapracowanych ludzi zmieniają się w ostatniej chwili, i tego nie karzemy. Jeśli odwołujesz później, prosimy o kontakt — zwykle przesuwamy sesję na inny termin."
      }
    },
    {
      "@type": "Question",
      "name": "Czy trenujecie również dzieci?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Tak — od 10 roku życia. Pracujemy z młodymi sportowcami (piłka nożna, siatkówka, gimnastyka) oraz z dziećmi, dla których trening to podstawa ogólnej sprawności i zdrowej postawy. Zawsze w obecności rodzica lub po jego wyraźnej zgodzie."
      }
    }
  ]
}
```

### Strona główna — katalog trzech programów

Do dopisania do istniejącego bloku `ExerciseGym` na produkcji (nie wklejaj całego węzła, tylko to pole):

```json
{
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Programy SAMtrening",
    "itemListElement": [
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/treningi-personalne/#service",
          "name": "Trening personalny 1:1"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/zdrowa-ciaza/#service",
          "name": "Trening w ciąży i po porodzie"
        }
      },
      {
        "@type": "Offer",
        "itemOffered": {
          "@type": "Service",
          "@id": "https://www.samtrening.com/e-trening/#service",
          "name": "E-trening personalny online"
        }
      }
    ]
  }
}
```

---

## Definition of Done (z ticketu)

- [ ] Rich Results Test / Schema Validator bez błędów dla każdej z 6 stron
- [ ] `provider` wskazuje na istniejący węzeł firmy (sprawdzone w źródle strony głównej)
- [ ] Brak zdublowanego `BreadcrumbList` (Yoast vs własny blok)
- [ ] Ceny w schema = ceny w treści = ceny w Profilu Firmy w Google

Narzędzia: [Rich Results Test](https://search.google.com/test/rich-results),
[Schema Markup Validator](https://validator.schema.org/).
