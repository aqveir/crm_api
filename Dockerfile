FROM composer:latest as vendor
WORKDIR /aqveir/aqveir-api
COPY composer.json composer.json
COPY composer.lock composer.lock
RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

FROM amazonlinux:latest
LABEL Name=ellaisys/aqveir-api Version=0.0.1
RUN yum install -y sudo; \
    yum update -y; \
    cd /

# Install Apache and PHP7.4 and essential libraries
RUN amazon-linux-extras install -y httpd httpd_modules; \
    amazon-linux-extras install -y php7.4; \
    cd /

#RUN systemctl enable httpd

# Install Project files
RUN mkdir -p aqveir

#ENTRYPOINT ["/etc/httpd"] 
#CMD ["sh", "-DFOREGROUND"]

#Open ports
EXPOSE 80/tcp
EXPOSE 443/tcp
