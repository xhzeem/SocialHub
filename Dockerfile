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

# Copy application files from app folder
COPY app/ /var/www/html/

# Install Twig in the correct directory
WORKDIR /var/www/html
RUN composer require twig/twig:^3.0

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/public/uploads && \
    chown -R www-data:www-data /var/www/html/public/uploads && \
    chmod -R 755 /var/www/html/public/uploads

# Configure Apache to serve from public directory
RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /var/www/html/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    ' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /var/www/html/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Configure Apache to serve different app on port 8080
RUN echo "Listen 8080" >> /etc/apache2/ports.conf && \
    echo "<VirtualHost *:8080>\n    DocumentRoot /var/www/html/internal\n    ErrorLog \${APACHE_LOG_DIR}/error_8080.log\n    CustomLog \${APACHE_LOG_DIR}/access_8080.log combined\n\n    # Enable .htaccess for URL rewriting\n    <Directory /var/www/html/internal>\n        AllowOverride All\n        Require all granted\n    </Directory>\n</VirtualHost>" > /etc/apache2/sites-available/8080.conf && \
    a2ensite 8080.conf

EXPOSE 80
