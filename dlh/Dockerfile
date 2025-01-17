# Gunakan image PHP 7.4 dengan Apache
FROM php:7.4-apache

# Instal ekstensi PHP yang diperlukan
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

# Salin kode aplikasi ke direktori root Apache
COPY . /var/www/html/

# Setel izin yang sesuai
RUN chown -R www-data:www-data /var/www/html

# Aktifkan modul mod_rewrite Apache
RUN a2enmod rewrite

# Konfigurasi Apache untuk memungkinkan .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Expose port 80 untuk mengakses aplikasi
EXPOSE 80