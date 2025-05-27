FROM php:8.1-apache

# Instala extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copia el contenido de la carpeta TFG dentro de la raíz del servidor
COPY TFG /var/www/html/TFG

# Cambia la raíz del servidor a /var/www/html/TFG/views
RUN sed -i 's|/var/www/html|/var/www/html/TFG/views|g' /etc/apache2/sites-available/000-default.conf

# Asegura que Apache cargue index.php por defecto
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/directoryindex.conf && \
    a2enconf directoryindex

# Otorga permisos correctos
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
