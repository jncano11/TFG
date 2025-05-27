# Imagen base oficial de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias como PDO para MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copia todo el proyecto al directorio del servidor
COPY . /var/www/html/

# Cambia el DocumentRoot del Apache para que apunte a /var/www/html/TFG/views
RUN sed -i 's|/var/www/html|/var/www/html/TFG/views|g' /etc/apache2/sites-available/000-default.conf

# Establece el directorio de trabajo en la carpeta views
WORKDIR /var/www/html/TFG/views

# Da permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expone el puerto 80
EXPOSE 80
