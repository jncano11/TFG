FROM php:8.1-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Activar mod_rewrite por si usas .htaccess
RUN a2enmod rewrite

# Copiar todo el contenido del proyecto TFG (incluyendo views, public, etc.)
COPY . /var/www/html/TFG

# Cambiar el DocumentRoot a la carpeta de views
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/TFG/views|' /etc/apache2/sites-available/000-default.conf

# Asegura index.php como archivo por defecto
RUN echo "DirectoryIndex index.php index.html" > /etc/apache2/conf-available/directoryindex.conf && \
    a2enconf directoryindex

# Dar permisos adecuados
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Exponer el puerto 80
EXPOSE 80
