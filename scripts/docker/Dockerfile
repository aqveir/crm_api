# Select the DNF Amazon Linux 2023 edition
FROM amazonlinux:latest

# Metadata & Docker file Environemnt Variables
ENV APP_TRADE_NAME="aqveir"
ENV APP_API_NAME="aqveir-api"
ENV APP_API_PATH=/${APP_TRADE_NAME}/${APP_API_NAME}
ENV APP_PORT=8989
ENV APP_NAME=${APP_TRADE_NAME}/${APP_API_NAME}
ENV APP_VERSION="0.0.1"

LABEL Name=${APP_NAME} Version=${APP_VERSION}

# Update the DNF package of the Amazon Linux 2023 edition
RUN dnf update -y && dnf upgrade -y && dnf clean all

# Install essential tools
RUN dnf install -y git; \
    dnf install -y curl; \
    dnf install -y zip; \
    dnf install -y unzip; \
    dnf install -y wget; \
    dnf install -y amazon-linux-extras; \
    dnf clean all
RUN cd /

# Install Apache and PHP8.4 and essential libraries
RUN dnf install -y httpd; \
    dnf install -y php8.4; \
    dnf install -y php8.4-devel php-pear libzip libzip-devel php-json php-curl; \
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
RUN cd / && mkdir -p ${APP_TRADE_NAME} && \
    cd /${APP_TRADE_NAME} && \
    git clone https://github.com/aqveir/crm_api.git ${APP_API_NAME}
RUN cd ${APP_API_PATH}

WORKDIR ${APP_API_PATH}

# Checkout the branch
RUN git config --global --add safe.directory ${APP_API_PATH}
RUN git checkout master
RUN git pull -f origin master

# Install dependencies
RUN composer install

# Copy the environment file
COPY .env .

# Set ownership and permissions for the folder
RUN chown -R apache:apache ${APP_API_PATH} \
    && find ${APP_API_PATH} -type f -exec chmod 644 {} \; \
    && find ${APP_API_PATH} -type d -exec chmod 755 {} \; \
    && chmod -R 775 ${APP_API_PATH}/bootstrap/cache \
    && chmod -R 775 ${APP_API_PATH}/storage \
    && chmod -R 755 ${APP_API_PATH}/public

#Open ports
EXPOSE 80/tcp
EXPOSE 443/tcp
EXPOSE ${APP_PORT}/tcp

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
