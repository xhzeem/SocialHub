FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    mariadb-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Twig
RUN composer require twig/twig:^3.0

COPY . /var/www/html/

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 755 /var/www/html/uploads

# Configure Apache to listen on port 8080
RUN echo "Listen 8080" >> /etc/apache2/ports.conf && \
    echo "<VirtualHost *:8080>\n    DocumentRoot /var/www/html\n    ErrorLog \${APACHE_LOG_DIR}/error_8080.log\n    CustomLog \${APACHE_LOG_DIR}/access_8080.log combined\n</VirtualHost>" > /etc/apache2/sites-available/8080.conf && \
    a2ensite 8080.conf

EXPOSE 80 8080
