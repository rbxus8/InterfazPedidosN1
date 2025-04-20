# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Instala la extensión mysqli
RUN docker-php-ext-install mysqli

# Copia tu código al contenedor
COPY . /var/www/html/

# Expone el puerto 80
EXPOSE 80

