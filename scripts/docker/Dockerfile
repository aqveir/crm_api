# Select the DNF Amazon Linux 2023 edition
FROM amazonlinux:latest

LABEL Name=aqveir/aqveir-api Version=0.0.1

# Update the DNF package of the Amazon Linux 2023 edition
RUN dnf update -y; \
    dnf install -y sudo; \
    dnf install -y git; \
    dnf install -y curl; \
    dnf install -y zip; \
    dnf install -y unzip; \
    cd /

# CRM-API Docker file Environemnt Variables
ENV PORT=8989

# Install Apache and PHP8.1 and essential libraries
RUN dnf install -y httpd; \
    dnf install -y php8.2; \
    dnf install -y php8.2-devel php-pear libzip libzip-devel php-json php-curl; \
    dnf clean metadata;
RUN dnf install -y php-{cli,common,dom,pear,fpm,gd,bcmath,mbstring,mysqlnd,xml,intl};
RUN cd /

# Build PHP8.1 Zip work-around
# RUN dnf install -y php8.2-devel php-pear libzip libzip-devel php-json php-curl
RUN pecl install zip
RUN echo "extension=zip.so" | sudo tee /etc/php.d/20-zip.ini

# Latest composer release
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Install Project files from the repo
RUN cd /; mkdir -p aqveir; \
    cd /aqveir; \
    git clone https://github.com/aqveir/crm_api.git aqveir-api;
RUN cd /aqveir/aqveir-api

WORKDIR /aqveir/aqveir-api

# Checkout the branch
RUN git config --global --add safe.directory /aqveir/aqveir-api
RUN git checkout master
RUN git pull -f origin master

# Install dependencies
RUN composer install

# Copy the environment file
COPY .env .

# Set ownership and permissions for the folder
RUN chown -R apache:apache /aqveir
RUN chmod -R 777 /aqveir/aqveir-api/bootstrap /aqveir/aqveir-api/public /aqveir/aqveir-api/storage

#Open ports
EXPOSE 80/tcp
EXPOSE 443/tcp
EXPOSE ${PORT}/tcp

# Copy apache configuration & restart apache server
COPY aqveir-apache.conf /etc/httpd/conf.d
RUN chmod 644 /etc/httpd/conf.d/aqveir-apache.conf

# Run the shell command
RUN chmod +x crm_reload.sh
RUN bash crm_reload.sh false false

# Start the Apache Server
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

RUN echo "ServerName localhost" >> /etc/httpd/conf/httpd.conf
CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
#CMD php artisan serve --host 0.0.0.0 --port ${PORT}
