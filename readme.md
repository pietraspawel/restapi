# REST API

## Autoryzacja.
Odbywa się za pomocą Basic Auth.
Dane dla testowej bazy:
`testuser:12345678`

## Pobranie wszystkich produktów z bazy.
#### Request:
`GET /products?page=1&pagesize=10`
Parametry `page` i `pagesize` są opcjonalne.
Domyślnie przyjmują wartości, odpowiednio: `1` i `10`.
`page` określa numer wyświetlanej strony.
`pagesize` określa rozmiar strony.

#### Response:
`200 OK`. Zwraca JSONa z listą produktów.

## Pobranie konkretnego produktu z bazy.
#### Request:
`GET /products/id`
#### Response:
`200 OK`. Zwraca JSONa z produktem. Gdy nie ma produktu o takim id w bazie, to zwraca pustego JSONa.

## Dodanie produktu do bazy.
#### Request:
`POST /products` + JSON
#### Response:
`201 OK`. Gdy produkt zostanie dodany do bazy.
`400 Data error`. Gdy wystąpi błąd, najczęściej w strukturze JSONa.

## Aktualizacja produktu w bazie.
#### Request:
`PUT /products/id` + JSON
#### Response:
`200 OK`. Gdy produkt zostanie zaktualizowany.
`400 Data error`. Gdy wystąpi błąd, najczęściej w strukturze JSONa.

## Usunięcie produktu z bazy.
#### Request:
`DELETE /products/id`
#### Response:
`200 OK`. Usuwa produkt z bazy.

## Format JSONa.
#### Przykład:
```{
    "price": 123,                           cena produktu
    "quantity": 1234,                       ilość na stanie
    "pl": {                                 nazwa języka
        "name": "jakiś produkt",            nazwa produktu po polsku
        "description": "opis produktu"      opis produktu po polsku
    },
    "en": {
        "name": "something",            
        "description": "product\'s description"     
    }
}
```

#### Dla zapytania POST:
`price` - nieobowiązkowe, domyślnie = `0`
`quantity` - nieobowiązkowe, domyślnie = `0`
`<i>nazwa_języka</i>` - musi być zdefiniowany przynajmniej jeden, 
w przykładowej bazie danych są to: pl, en i xx.
`name` - musi być określona nazwa przynajmniej w jednym języku
`description` - nieobowiązkowe, domyślnie pusty string

#### Dla zapytania PUT:
Żadne z pól nie jest obowiązkowe. Zostaną zaktualizowane tylko podane. 
Dodatkowo, jeśli dla produktu została podana nazwa i/lub opis w języku w którym 
wcześniej produkt nie został opisany, to nazwa i/lub opis w tym języku zostaną 
dodane do bazy (pod warunkiem oczywiście, że język istnieje w bazie).

## Przykłady zapytań w PHP cURL.
#### Pobranie wszystkich produktów.
```$ch = curl_init("http://restapi.demo.pietraspawel.pl/products");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_exec($ch);
curl_close($ch);
die();
```

#### Pobranie konkretnego produku.
```$ch = curl_init("http://restapi.demo.pietraspawel.pl/products/10");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_exec($ch);
curl_close($ch);
die();
```

#### Dodanie produktu.
```$data = '
{
    "quantity": 8901,
    "pl": {
        "name": "inny produkt",
        "description": "Litwo, Ojczyzno moja"
    },
    "en": {
        "name": "some thing"
    }
}';
$ch = curl_init("http://restapi.demo.pietraspawel.pl/products");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_exec($ch);
curl_close($ch);
die();
```

#### Aktualizacja produktu.
```$data = '
{
    "price": 666,
    "quantity": 6666,
    "en": {
        "description": "fork\'s description"
    },
    "xx": {
        "name": "videlez",
        "description": "dsfsdf sdfsf"
    }
}';
$ch = curl_init("http://restapi.demo.pietraspawel.pl/products/10");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_exec($ch);
curl_close($ch);
die();
```

#### Usunięcie produktu.
```$ch = curl_init("http://restapi.demo.pietraspawel.pl/products/12");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_exec($ch);
curl_close($ch);
die();
```
