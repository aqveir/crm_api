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
RUN yum update -y; \
    yum install -y sudo; \
    yum install -y git; \
    cd /

# Install Apache and PHP7.4 and essential libraries
RUN amazon-linux-extras install -y httpd httpd_modules; \
    amazon-linux-extras install -y php7.4; \
    yum clean metadata; \
    yum install -y php php-{pear,cli,cgi,common,curl,mbstring,gd,mysqlnd,gettext,bcmath,json,xml,fpm,intl,zip}; \
    cd /

# Install Project files
RUN mkdir -p aqveir; \
    # git clone git@bitbucket.org:ellaisys/eis_crmomni_api.git; \
    chown -R apache:apache /aqveir

# Update the apache config file
# RUN touch /etc/httpd/conf/aqveir-apache-ssl.conf
# RUN printf " \
#         ## \
#         ## AQVEIR SOLUTION - PROD (SSL Virtual Host Context) \
#         ## \
#         ## https://*.aqveir.com OR https://api.aqveir.com \
#         ## \
#         <VirtualHost _default_:443> \
#             # General setup for the virtual host, inherited from global configuration \
#             DocumentRoot "/aqveir/prod/public" \
#             ServerName api.aqveir.com:443 \
#             ServerAlias *.aqveir.com \
#             DirectoryIndex index.php \
#             \
#             # Use separate log files for the SSL virtual host; note that LogLevel \
#             # is not inherited from httpd.conf. \
#             ErrorLog logs/ssl_error_log \
#             TransferLog logs/ssl_access_log \
#             LogLevel warn \
#             \
#             # SSL Engine Switch: \
#             SSLEngine on \
#             \
#             # SSL Protocol support: \
#             SSLProtocol all -SSLv3 \
#             \
#             # SSL Cipher Suite: \
#             SSLCipherSuite HIGH:MEDIUM:!aNULL:!MD5:!SEED:!IDEA \
#             \
#             # Speed-optimized SSL Cipher configuration: \
#             #SSLCipherSuite RC4-SHA:AES128-SHA:HIGH:MEDIUM:!aNULL:!MD5 \
#             #SSLHonorCipherOrder on \
#             \
#             # Server Certificate: \
#             SSLCertificateFile /etc/letsencrypt/live/aqveir.com/cert.pem \
#             \
#             # Server Private Key: \
#             SSLCertificateKeyFile /etc/letsencrypt/live/aqveir.com/privkey.pem \
#             \
#             # Server Certificate Chain: \
#             SSLCertificateChainFile /etc/letsencrypt/live/aqveir.com/fullchain.pem \
#             \
#             # Certificate Authority (CA): \
#             #SSLCACertificateFile /etc/pki/tls/certs/ca-bundle.crt \
#             \
#             # Client Authentication (Type): \
#             #SSLVerifyClient require \
#             #SSLVerifyDepth  10 \
#             \
#             #   Access Control: \
#             #   With SSLRequire you can do per-directory access control based \
#             #   on arbitrary complex boolean expressions containing server \
#             #   variable checks and other lookup directives.  The syntax is a \
#             #   mixture between C and Perl.  See the mod_ssl documentation \
#             #   for more details. \
#             #<Location /> \
#             #SSLRequire (    %{SSL_CIPHER} !~ m/^(EXP|NULL)/ \
#             #            and %{SSL_CLIENT_S_DN_O} eq "Snake Oil, Ltd." \
#             #            and %{SSL_CLIENT_S_DN_OU} in {"Staff", "CA", "Dev"} \
#             #            and %{TIME_WDAY} >= 1 and %{TIME_WDAY} <= 5 \
#             #            and %{TIME_HOUR} >= 8 and %{TIME_HOUR} <= 20       ) \
#             #</Location>
#             \
#             # SSL Engine Options: \
#             #SSLOptions +FakeBasicAuth +ExportCertData +StrictRequire \
#             \
#             <Files ~ "\.(cgi|shtml|phtml|php3?)$"> \
#                 SSLOptions +StdEnvVars \
#             </Files> \
#             \
#             <Directory "/aqveir/prod/public"> \
#                 Options Indexes FollowSymLinks \
#                 AllowOverride All \
#                 SSLOptions +StdEnvVars \
#             \
#                 <IfVersion < 2.3 > \
#                     Order allow,deny \
#                     Allow from all \
#                 </IfVersion> \
#             \
#                 <IfVersion >= 2.3 > \
#                     Require all granted \
#                 </IfVersion> \
#             </Directory> \
#             \
#             # SSL Protocol Adjustments: \
#             BrowserMatch "MSIE [2-5]" \
#                 nokeepalive ssl-unclean-shutdown \
#                 downgrade-1.0 force-response-1.0 \
#             \
#             # Per-Server Logging: \
#             CustomLog logs/ssl_request_log \
#                 "%t %h %{SSL_PROTOCOL}x %{SSL_CIPHER}x \"%r\" %b" \
#             \
#         </VirtualHost>"
#RUN echo "Include '/etc/httpd/conf/aqveir-apache-ssl.conf'" >> /etc/httpd/conf/httpd.conf

#Open ports
EXPOSE 80/tcp
EXPOSE 443/tcp

#ENTRYPOINT ["/"]
# Start the Apache Server
CMD ["/usr/sbin/httpd","-DFOREGROUND"]
