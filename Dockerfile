# Pobiera oficjalny obraz PHP z Apache
FROM php:8.2-apache

# Kopiuje pliki z katalogu projektu do katalogu serwera WWW w kontenerze
COPY . /var/www/html/

# Ustawia port 80 do wystawienia na zewnątrz
EXPOSE 80

