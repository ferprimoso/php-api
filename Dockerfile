# Use the base Yii2 PHP image
FROM yiisoftware/yii2-php:8.2-apache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /app

# Copy your application code into the container
COPY . /app

# Expose port 80
EXPOSE 80