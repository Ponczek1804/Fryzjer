# Działanie strony

plik konfiguracyjny apache `.htaccess` jest skonfigurowany w taki sposób aby wszystkie zapytania (adres url wpisane w pasku adresu) były przekierowywane do pliku index.php

W pliku `index.php` został skonfigurowany prosty routing. 


Na podstawie aktualnego adresu url zostaje odnaleziony odpowiedni Routing z tablicy routingu i zostaje wyrenderowany na stronie.

Generowanie strony przebiega w 3 etapach:
- przed wyrenderowaniem strony z pliku `@php/controler/authentication.php` zostaje uruchomiona funkcja `authentication_check()` która sprawdza czy dany użytkownik ma dostęp do danej strony. Jezeli nie to przekirowuje go do strony głównej
- nastepnie uruchamiany jest odpowiedni skrypt widoku z folderu `@php/views/` który generuje wewnętrzną część strony i zwraca `string`
- W ostatnim etapie generowania zostaje uruchomiona odpowiednia funkcja generujaca wybrany layout z folderu `@php/layouts/` np `layout_default()` która przyjmuje 2 parametry wyświetlany tytuł strony oraz widok w formacie `string` który został wygenerowany w poprzednim etapie
