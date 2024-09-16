# Use the base Yii2 PHP image
FROM yiisoftware/yii2-php:8.2-apache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /app

# Copy your application code into the container
COPY . /app

# Install PHP dependencies
RUN composer install --prefer-dist

# Expose port 80
EXPOSE 80

# Command to run Apache in the foreground
CMD ["apache2-foreground"]

