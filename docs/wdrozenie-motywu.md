# Motyw `samtrening-2026` do gita — procedura

Realizacja opcji A z [`prototyp-vs-produkcja.md`](prototyp-vs-produkcja.md): kod, który faktycznie
działa na stronie, ma trafić do tego repozytorium.

## Kto co robi

Pierwszy krok musi wykonać człowiek z dostępem do serwera. Sesja Claude'a nie połączy się
z produkcją: polityka sieciowa środowiska blokuje `samtrening.com` (odpowiedź 403 na poziomie
proxy), więc ani HTTP, ani SSH do Lightsaila nie wchodzą w grę — także wtedy, gdyby ktoś
przekazał klucz.

Pobranie strony przez przeglądarkę też nie rozwiązuje sprawy: serwer wykonuje pliki PHP i oddaje
gotowy HTML. Szablony (`functions.php`, `content-*.php`, `page.php`) nie są dostępne z zewnątrz
w żadnej formie.

## Krok 1 — wyciągnij motyw z serwera

### Wariant a) SSH (zalecany)

```bash
# 1. Połącz się z instancją (użytkownik w obrazie Bitnami to „bitnami")
ssh -i ~/klucze/samtrening.pem bitnami@ADRES_IP

# 2. Znajdź katalog motywów — Bitnami trzyma go w jednym z dwóch miejsc
ls -d /opt/bitnami/wordpress/wp-content/themes/*/ 2>/dev/null \
  || ls -d /opt/bitnami/apps/wordpress/htdocs/wp-content/themes/*/

# 3. Spakuj sam motyw (podmień ścieżkę na tę, którą zwrócił krok 2)
tar -czf ~/samtrening-2026.tar.gz \
  -C /opt/bitnami/wordpress/wp-content/themes samtrening-2026

# 4. Rozłącz się i pobierz paczkę na swój komputer
exit
scp -i ~/klucze/samtrening.pem bitnami@ADRES_IP:~/samtrening-2026.tar.gz .
```

### Wariant b) bez SSH, z panelu WordPressa

Wtyczka do zarządzania plikami (np. „File Manager") pozwala zaznaczyć katalog
`wp-content/themes/samtrening-2026` i pobrać go jako ZIP. Wolniejsze i wymaga instalacji wtyczki,
ale nie potrzebuje klucza.

### Wariant c) git bezpośrednio na serwerze

Najbardziej przyszłościowe, bo późniejsze wdrożenie to zwykły `git pull` na serwerze:

```bash
cd /opt/bitnami/wordpress/wp-content/themes/samtrening-2026
git init && git add -A && git commit -m "Motyw samtrening-2026 — stan produkcyjny"
git remote add origin https://github.com/sambor88-glitch/samtrening-website.git
# dalej: push na osobną gałąź, scalenie przez PR
```

## Krok 2 — sprawdź paczkę, zanim trafi do repozytorium

**To jest moment, w którym najłatwiej wypchnąć sekret do gita.** Przed commitem:

```bash
tar -xzf samtrening-2026.tar.gz
grep -rniE "api[_-]?key|secret|password|token|smtp|AKIA" samtrening-2026/ | head -30
```

Jeśli cokolwiek wyjdzie — klucz API map, dane SMTP, token — **nie commituj tego**.
Takie wartości przenosi się do `wp-config.php` (poza repozytorium) i czyta przez
`defined('NAZWA') ? NAZWA : ''`. Sekret raz wypchnięty do gita zostaje w historii,
nawet po usunięciu z bieżącej wersji.

Wyrzuć też to, co nie jest kodem: `node_modules/`, `.cache/`, pliki `.map`, kopie zapasowe
(`*.bak`, `*-old.php`), katalogi cache wtyczek.

## Krok 3 — struktura repozytorium

```
samtrening-website/
├── theme/
│   └── samtrening-2026/     # kod, który działa na stronie
├── prototype/               # statyczny prototyp z kwietnia 2026 (dziś pliki leżą w katalogu głównym)
└── docs/                    # dokumentacja, notatki decyzyjne, bloki schema
```

Przeniesienie prototypu do `prototype/` robimy **razem** z wrzuceniem motywu, nie wcześniej —
pliki HTML z katalogu głównego publikuje dziś GitLab Pages (`.gitlab-ci.yml`) i prawdopodobnie
GitHub Pages. Przenosiny bez jednoczesnej poprawki CI zepsują podgląd prototypu, a bez motywu
w repozytorium nic za to nie dają.

Przy tych przenosinach trzeba poprawić `.gitlab-ci.yml`: zamiast `cp -r *.html public/`
ma być `cp -r prototype/* public/`.

## Krok 4 — wdrażanie ze zmian w gicie

Docelowo `main` jest źródłem prawdy, a serwer dostaje zmiany automatycznie. Szkic workflow
(nie włączaj, dopóki nie ma sekretów i środowiska testowego):

```yaml
name: Deploy theme
on:
  push:
    branches: [main]
    paths: ['theme/samtrening-2026/**']

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v7
      - name: Klucz SSH
        run: |
          install -m 700 -d ~/.ssh
          echo "${{ secrets.LIGHTSAIL_SSH_KEY }}" > ~/.ssh/id_ed25519
          chmod 600 ~/.ssh/id_ed25519
          ssh-keyscan -H "${{ secrets.LIGHTSAIL_HOST }}" >> ~/.ssh/known_hosts
      - name: Wyślij motyw
        run: |
          rsync -az --delete \
            theme/samtrening-2026/ \
            bitnami@${{ secrets.LIGHTSAIL_HOST }}:/opt/bitnami/wordpress/wp-content/themes/samtrening-2026/
```

Sekrety do ustawienia w Settings → Secrets and variables → Actions:
`LIGHTSAIL_HOST`, `LIGHTSAIL_SSH_KEY` (klucz wyłącznie do wdrożeń, nie ten sam, którym logujesz się ręcznie).

Uwaga na `--delete`: usuwa z serwera pliki, których nie ma w repozytorium. To jest zaleta
(koniec z ręcznymi poprawkami „na żywo", o których nikt nie pamięta), ale pierwszy przebieg
zrób z `--dry-run` i przeczytaj listę.

## Krok 5 — środowisko testowe

Dziś każda zmiana w motywie idzie prosto na produkcję, która zbiera leady. Przy wdrożeniach
z CI to ryzyko rośnie, bo zmiany będą częstsze.

Najtańszy wariant: druga instancja Lightsail w eu-central-1 (Frankfurt) z kopią bazy,
wdrażana z gałęzi `staging`. Rząd wielkości 10–20 USD miesięcznie przy najmniejszym planie —
do policzenia dokładnie przy wyborze rozmiaru instancji. Alternatywa za zero złotych:
lokalny WordPress (Local, DDEV) na komputerze, bez publicznego adresu.

## Czego ta procedura nie obejmuje

Baza danych i katalog `wp-content/uploads` zostają poza repozytorium — treści stron, wpisy
i zdjęcia są zarządzane w panelu WordPressa i nie wersjonujemy ich w gicie. Do repozytorium
trafia wyłącznie kod motywu.

## Stan

- [ ] Krok 1 — motyw wyciągnięty z serwera (potrzebny dostęp, po stronie Maćka)
- [ ] Krok 2 — paczka sprawdzona pod kątem sekretów
- [ ] Krok 3 — motyw w `theme/`, prototyp w `prototype/`, poprawione CI
- [ ] Krok 4 — wdrażanie z CI (wymaga sekretów)
- [ ] Krok 5 — decyzja o środowisku testowym
