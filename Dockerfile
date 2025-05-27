FROM php:8.1-apache

RUN docker-php-ext-install pdo pdo_mysql

# Copiar todo desde el contexto actual (ya dentro de TFG)
COPY . /var/www/html

# Cambiar la raíz a /var/www/html/views
RUN sed -i 's|/var/www/html|/var/www/html/views|g' /etc/apache2/sites-available/000-default.conf

# Asegura index.php como archivo por defecto
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/directoryindex.conf && \
    a2enconf directoryindex

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
