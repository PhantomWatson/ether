FROM httpd:2

ARG WEB_USER
ARG WEB_GROUP
ARG APACHE_ROOT_DIR

#RUN apt-get install apache2
#RUN a2enmod ssl && a2enmod rewrite
COPY httpd-vhosts.conf ${APACHE_ROOT_DIR}/conf/extra/httpd-vhosts.conf
COPY httpd.conf ${APACHE_ROOT_DIR}/conf/httpd.conf

RUN chgrp -R ${WEB_GROUP} ${APACHE_ROOT_DIR}/conf/httpd.conf \
 && chgrp -R ${WEB_GROUP} ${APACHE_ROOT_DIR}/conf/extra/httpd-vhosts.conf

RUN mkdir -p /etc/apache2/ssl
COPY ./ssl/cert.pem /etc/apache2/ssl/cert.pem
COPY ./ssl/cert-key.pem /etc/apache2/ssl/cert-key.pem

#ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/


#RUN pecl install xdebug && docker-php-ext-enable xdebug

#COPY --from=composer /usr/bin/composer /usr/bin/composer

#EXPOSE 80

RUN usermod -u 1000 ${WEB_USER} \
 && groupmod -g 1000 ${WEB_GROUP} \
 && chgrp -R ${WEB_GROUP} ${APACHE_ROOT_DIR}

# Start Apache
# CMD apachectl -D FOREGROUND
