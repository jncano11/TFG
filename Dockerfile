FROM php:8.2-apache

# Instala extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copia TODO el proyecto al servidor web
COPY . /var/www/html/

# Cambia directorio por defecto si tu index está en views/
WORKDIR /var/www/html/views

# Establece permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
