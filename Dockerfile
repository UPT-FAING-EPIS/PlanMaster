FROM php:8.2-apache

# Instala dependencias necesarias para PostgreSQL, GD y ZIP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    unzip \
    libicu-dev \
    && docker-php-ext-install pdo_pgsql pgsql gd zip intl

# Habilita mod_rewrite (opcional pero común para frameworks)
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