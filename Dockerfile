FROM php:8.2-apache

# Sistem bağımlılıklarını yükle
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl

# Apache mod_rewrite'ı aktif et
RUN a2enmod rewrite

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# Tüm dosyaları kopyala
COPY . /var/www/html/

# Apache portu
EXPOSE 80

# Apache'yi başlat
CMD ["apache2-foreground"]
