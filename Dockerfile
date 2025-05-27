FROM php:8.1-apache

# Instala extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copia el proyecto completo
COPY . /var/www/html/

# Establece la carpeta views como raíz del servidor
RUN sed -i 's|/var/www/html|/var/www/html/TFG/views|g' /etc/apache2/sites-available/000-default.conf

# Configura index.php como archivo por defecto
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/directoryindex.conf && \
    a2enconf directoryindex

# Otorga permisos al servidor para acceder a los archivos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
