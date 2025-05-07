# Use the httpd base image
FROM httpd:latest

# Install PHP and Apache
RUN apt-get update && \
    apt-get install -y php8.2 php8.2-dev apache2 \
    && apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Apache configuration
COPY aqveir-apache.conf /usr/local/apache2/conf/aqveir-apache.conf

# Set the port for Apache to listen on
ENV PORT=8989

# Update Apache configuration to listen on the specified port
RUN sed -i "s/Listen 80/Listen $PORT/" /usr/local/apache2/conf/httpd.conf

# Set the ServerName directive
RUN echo "ServerName localhost" >> /usr/local/apache2/conf/httpd.conf

# Create necessary directories
RUN mkdir -p /aqveir/aqveir-api

# Set ownership and permissions for the folder
RUN chown -R www-data:www-data /aqveir

# Copy Apache configuration file for the virtual host
COPY aqveir-apache.conf /etc/apache2/sites-enabled/

# Copy your project files into the Apache document root directory
COPY . /etc/apache2/sites-available

# Expose port 80
EXPOSE 80

# Start Apache server
# CMD ["httpd", "-D", "FOREGROUND", "2>/dev/null"]

# Start Apache server
CMD ["httpd", "-D", "FOREGROUND"]
