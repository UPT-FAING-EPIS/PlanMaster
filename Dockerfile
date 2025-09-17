FROM php:8.2-apache

# Instala dependencias necesarias para MySQL, GD, ZIP e intl
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mysqli gd zip intl

# Habilita mod_rewrite (útil para frameworks MVC)
RUN a2enmod rewrite

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html/

# Establece permisos apropiados
RUN chown -R www-data:www-data /var/www/html

# Cambia la raíz del servidor a /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Habilita la reescritura para public/.htaccess
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf
