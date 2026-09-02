# pola-wyboru

# Nowa wtyczka WooCommerce – konfigurator produktu GASSU

Chcę stworzyć nową wtyczkę do WooCommerce, której zadaniem będzie dodanie na karcie produktu, za pomocą shortcode, konfiguratora produktu.

Produkty nie są produktami wariantowymi WooCommerce. Każda wysokość obcasa jest osobnym produktem, posiadającym własne SKU, adres URL i możliwość niezależnego wyświetlania oraz filtrowania w sklepie.

## 1. Rodzaj podeszwy

Pierwszym polem ma być lista rozwijana:

**Rodzaj podeszwy**

Opcje:

* welur,
* skóra.

Wybrana wartość jest opcją zamawianego produktu i musi zostać dodana do danych pozycji zamówienia.

Jeżeli klient wybierze np. „skóra”, a następnie zmieni wysokość obcasa i zostanie przekierowany do innego produktu, wybór „skóra” musi zostać zachowany i automatycznie zaznaczony na nowej stronie produktu.

## 2. Wysokość obcasa

Drugim polem ma być lista rozwijana:

**Wysokość obcasa**

Zmiana tego pola nie zmienia wariantu WooCommerce, lecz powoduje przejście na stronę odpowiedniego, osobnego produktu.

Produkty odpowiadające sobie pod względem wysokości obcasa powinny być mapowane przede wszystkim na podstawie:

* atrybutu pa_**model**,
* atrybutu pa_**wysokosc_obcasa**,
* identyfikatora wersji kolorystycznej. **pa_wersja_kolorystyczna,**

Po zmianie wysokości obcasa klient powinien zostać przekierowany do produktu:

* o tym samym modelu,
* w dokładnie tej samej wersji kolorystycznej,
* ale z inną wysokością obcasa.

Przykład:

„Paula 8 cm – kolorowe sandałki...”

oraz

„Paula 10 cm – kolorowe sandałki...”

to dwa oddzielne produkty, ale należące do tej samej grupy modelu i wersji kolorystycznej. Zmiana wysokości z 8 cm na 10 cm powinna przenieść klienta bezpośrednio do drugiego produktu.

###

## 3. Nazwy wyświetlane dla wysokości obcasów

Wartość techniczna atrybutu wysokości obcasa nie zawsze powinna być identyczna z wartością wyświetlaną klientowi.

Przykład:

wartość atrybutu produktu:
**8 cm**

wartość wyświetlana klientowi:
**7,5–8,5 cm**

Wynika to z tego, że faktyczna wysokość obcasa zmienia się zależnie od rozmiaru buta – mniejsze rozmiary mają nieco niższy, a większe nieco wyższy obcas.

W ustawieniach wtyczki powinna więc znajdować się sekcja pozwalająca przypisać własną nazwę wyświetlaną do każdej istniejącej wartości atrybutu „Wysokość obcasa”.

Wtyczka powinna automatycznie pobrać dostępne wartości tego atrybutu, np.:

5,5 cm → [pole nazwy wyświetlanej]

7 cm → [pole nazwy wyświetlanej]

8 cm → [pole nazwy wyświetlanej]

9,5 cm → [pole nazwy wyświetlanej]

10 cm → [pole nazwy wyświetlanej]

11 cm → [pole nazwy wyświetlanej]

Administrator będzie mógł ręcznie ustawić tekst widoczny dla klienta.

## 4. Tęgość / szerokość buta

Kolejnym polem rozwijanym ma być:

**Tęgość buta**

Opcje:

* wąska,
* standardowa,
* szeroka.

Domyślnie zawsze zaznaczona ma być:

**standardowa**

Wybrana tęgość powinna zostać zapisana jako informacja przy pozycji zamówienia.

Zmiana tęgości nie powoduje przechodzenia do innego produktu.

## 5. Poduszka pod palce

Kolejnym polem rozwijanym ma być:

**Poduszka pod palce**

Opcje:

* dodatkowa miękka pianka,
* bez dodatkowej pianki – twardo pod palcami.

Wybrana wartość również powinna zostać zapisana przy pozycji zamówienia.

## 6. Indywidualna kolorystyka lub nietypowy rozmiar

Pod polami wyboru powinien znajdować się checkboxy:

**1 Chcę zmienić kolorystykę na własną **

Po jego zaznaczeniu powinno pojawić się pole tekstowe, w którym klient będzie mógł opisać swoje wymagania.

Przykładowy placeholder: Opisz zmianę kolorystyki

**2 chcę zamówić nietypowy rozmiar**

Przykładowy placeholder: podaj informacje dotyczące nietypowego rozmiaru.

Informacja wpisana przez klienta musi zostać zapisana przy pozycji zamówienia.

W przypadku zamówienia nietypowego rozmiaru klient powinien otrzymać dodatkowy komunikat informujący, że produkt wykonany w nietypowym rozmiarze nie podlega zwrotowi.

Powinien zostać również wyświetlony link:

**Sprawdź, jak zmierzyć stopy**

prowadzący do:

`/tabela-rozmiarow/`

## 7. Zachowywanie konfiguracji podczas zmiany wysokości obcasa

To bardzo istotny element działania wtyczki.

Jeżeli klient skonfiguruje produkt, np.:

* podeszwa: skóra,
* tęgość: szeroka,
* poduszka: dodatkowa miękka pianka,

a następnie zmieni wysokość obcasa, wtyczka przekieruje go do odpowiedniego produktu, ale wszystkie wcześniej wybrane opcje powinny zostać zachowane.

Po otwarciu nowego produktu konfigurator powinien więc automatycznie przywrócić poprzednie wartości.

Można to rozwiązać np. przez parametry URL, sessionStorage/localStorage albo sesję WooCommerce. Implementacja techniczna może zostać dobrana przez programistę, pod warunkiem że mechanizm będzie niezawodny i nie będzie przenosił ustawień przypadkowo między niezwiązanymi produktami.

## 8. Shortcode

Cały konfigurator powinien być możliwy do umieszczenia na karcie produktu za pomocą shortcode, np.:

`[gassu_product_configurator]`

Shortcode powinien automatycznie rozpoznawać aktualnie wyświetlany produkt i na jego podstawie:

* ustalać model,
* ustalać kolor wariantu,
* ustalać aktualną wysokość obcasa,
* wyszukiwać pozostałe produkty należące do tej samej grupy,
* budować listę dostępnych wysokości obcasa.

Na liście wysokości powinny pojawiać się wyłącznie wysokości, dla których faktycznie istnieje opublikowany produkt odpowiadający aktualnemu modelowi i wersji kolorystycznej.

## 9. Najważniejsza zasada działania

Produktem bazowym jest kombinacja:

**Model + Kolor wariantu + Wysokość obcasa**

Model i kolor wariantu określają grupę odpowiadających sobie produktów.

Wysokość obcasa określa konkretny produkt i jego adres URL.

Pozostałe parametry:

* rodzaj podeszwy,
* tęgość,
* poduszka pod palce,
* indywidualna kolorystyka / nietypowy rozmiar

są konfiguracją zamówienia i nie tworzą osobnych produktów.
