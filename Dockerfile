FROM php:8.1-apache

RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo dentro del contenedor
COPY . /var/www/html

# Asegurar que Apache use index.php o index.html
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/directoryindex.conf && \
    a2enconf directoryindex

# Habilitar mod_rewrite si usas rutas limpias
RUN a2enmod rewrite

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
