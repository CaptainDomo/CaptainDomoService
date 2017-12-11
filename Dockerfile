FROM janes/alpine-lamp

# Install phpmyadmin
RUN apk update
RUN apk add phpmyadmin
RUN chown -R apache:apache /etc/phpmyadmin
RUN chown -R apache:apache /usr/share/webapps/phpmyadmin
RUN sed -i 's+Order allow,deny++' /etc/apache2/conf.d/phpmyadmin.conf
RUN sed -i 's+Allow from all+Require all granted+' /etc/apache2/conf.d/phpmyadmin.conf
RUN sed -i "s+AllowNoPassword'] = false;+AllowNoPassword'] = true;+" /etc/phpmyadmin/config.inc.php
RUN sed -i "s+localhost+localhost:3306+" /etc/phpmyadmin/config.inc.php

RUN apk add git
COPY ./composer* /composer-build/
WORKDIR /composer-build
RUN php composer.phar install
RUN cp -R ./vendor /www/vendor
WORKDIR /www

# Copy sources
COPY ./db/ /db
COPY ./src/ /www/src
COPY ./.htaccess /www/.htaccess
RUN ["chown", "-R", "apache:apache", "/www"]

# Enable mod_rewrite
RUN sed -i 's+#LoadModule rewrite_module modules/mod_rewrite.so+LoadModule rewrite_module modules/mod_rewrite.so+' /etc/apache2/httpd.conf

# Create CaptainDomo DB
RUN sed -i 's#mysql -uroot -e "create database db;"#mysql < /db/ddl.sql \&\& mysql < /db/test_data.sql#' /start.sh