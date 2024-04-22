FROM php:8.2-fpm
LABEL authors="erikpires"

ARG user=desafio
ARG uid=1000

RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user

# Definindo o diretório do laravel
WORKDIR /var/www/html

#RUN apt-get update && apt-get install -y \
#    zip \
#    unzip

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd xdebug zip pdo pdo_mysql

# instalando composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar o script de entrada
COPY Docker/backend/entrypoint.sh /usr/local/bin/

# Torná-lo executável
RUN chmod +x /usr/local/bin/entrypoint.sh

# Definir o script de entrada
#ENTRYPOINT ["entrypoint.sh"]
USER $user
#Iniciando o servidor PHP
CMD [ "php-fpm" ]
