# Используем официальный PHP образ с FPM
FROM php:8.2-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip curl \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql

# Устанавливаем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Копируем файлы проекта
WORKDIR /var/www
COPY . .

# Устанавливаем зависимости PHP
RUN composer install --no-dev --optimize-autoloader

# Разрешаем запись
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
