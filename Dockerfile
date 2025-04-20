# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Copia tu código al contenedor
COPY . /var/www/html/

# Expone el puerto 80
EXPOSE 80
