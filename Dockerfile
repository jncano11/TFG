# Imagen base con Apache y PHP
FROM php:8.2-apache

# Habilitamos extensiones necesarias para PDO con MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo el contenido del proyecto al contenedor
COPY . /var/www/html/

# Cambiamos el directorio raíz para que apunte a /TFG/views
ENV APACHE_DOCUMENT_ROOT /var/www/html/TFG/views

# Actualizamos la configuración de Apache para que apunte al nuevo DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Damos permisos a los archivos (opcional)
RUN chown -R www-data:www-data /var/www/html

# Exponemos el puerto 80 (Render lo usa automáticamente)
EXPOSE 80
