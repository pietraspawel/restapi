<?php

/**
 * Help site.
 */

namespace pietras;

?>
<h1>REST API</h1>

<h2>Autoryzacja.</h2>
<p>Odbywa się za pomocą Basic Auth.<br />
Dane dla testowej bazy:<br />
<code>testuser:12345678</code></p>

<h2>Pobranie wszystkich produktów z bazy.</h2>
<h4>Request:</h4>
<code>GET /products?page=1&pagesize=10</code>
<p>Parametry <code>page</code> i <code>pagesize</code> są opcjonalne.<br />
Domyślnie przyjmują wartości, odpowiednio: <code>1</code> i <code>10</code>.<br />
<code>page</code> określa numer wyświetlanej strony.<br />
<code>pagesize</code> określa rozmiar strony.<br />
</p>
<h4>Response:</h4>
<p><code>200 OK</code>. Zwraca JSONa z listą produktów.</p>

<h2>Pobranie konkretnego produktu z bazy.</h2>
<h4>Request:</h4>
<code>GET /products/id</code>
<h4>Response:</h4>
<p><code>200 OK</code>. Zwraca JSONa z produktem. Gdy nie ma produktu o takim id w bazie, to zwraca pustego JSONa.</p>

<h2>Dodanie produktu do bazy.</h2>
<h4>Request:</h4>
<p><code>POST /products</code> + JSON</p>
<h4>Response:</h4>
<p><code>201 OK</code>. Gdy produkt zostanie dodany do bazy.<br />
<code>400 Data error</code>. Gdy wystąpi błąd, najczęściej w strukturze JSONa.</p>

<h2>Aktualizacja produktu w bazie.</h2>
<h4>Request:</h4>
<p><code>PUT /products/id</code> + JSON</p>
<h4>Response:</h4>
<p><code>200 OK</code>. Gdy produkt zostanie zaktualizowany.<br />
<code>400 Data error</code>. Gdy wystąpi błąd, najczęściej w strukturze JSONa.</p>

<h2>Usunięcie produktu z bazy.</h2>
<h4>Request:</h4>
<p><code>DELETE /products/id</code></p>
<h4>Response:</h4>
<p><code>200 OK</code>. Usuwa produkt z bazy.</p>

<h2>Format JSONa.</h2>
<h4>Przykład:</h4>
<pre><code>{
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
</code></pre>

<h4>Dla zapytania POST:</h4>
<p><code>price</code> - nieobowiązkowe, domyślnie = <code>0</code><br />
<code>quantity</code> - nieobowiązkowe, domyślnie = <code>0</code><br />
<code><i>nazwa_języka</i></code> - musi być zdefiniowany przynajmniej jeden, 
w przykładowej bazie danych są to: pl, en i xx.<br />
<code>name</code> - musi być określona nazwa przynajmniej w jednym języku<br />
<code>description</code> - nieobowiązkowe, domyślnie pusty string</p>

<h4>Dla zapytania PUT:</h4>
<p>Żadne z pól nie jest obowiązkowe. Zostaną zaktualizowane tylko podane. 
Dodatkowo, jeśli dla produktu została podana nazwa i/lub opis w języku w którym 
wcześniej produkt nie został opisany, to nazwa i/lub opis w tym języku zostaną 
dodane do bazy (pod warunkiem oczywiście, że język istnieje w bazie).</p>

<h2>Przykłady zapytań w PHP cURL.</h2>
<h4>Pobranie wszystkich produktów.</h4>
<pre><code>$ch = curl_init("http://restapi.demo.pietraspawel.pl/products");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_exec($ch);
curl_close($ch);
die();
</code></pre>

<h4>Pobranie konkretnego produku.</h4>
<pre><code>$ch = curl_init("http://restapi.demo.pietraspawel.pl/products/10");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_exec($ch);
curl_close($ch);
die();
</code></pre>

<h4>Dodanie produktu.</h4>
<pre><code>$data = '
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
</code></pre>

<h4>Aktualizacja produktu.</h4>
<pre><code>$data = '
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
</code></pre>

<h4>Usunięcie produktu.</h4>
<pre><code>$ch = curl_init("http://restapi.demo.pietraspawel.pl/products/12");
curl_setopt($ch, CURLOPT_USERPWD, "testuser:12345678");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_exec($ch);
curl_close($ch);
die();
</code></pre>
