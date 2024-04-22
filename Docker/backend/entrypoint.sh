#!/bin/sh

# Pegar o UID e GID do diretório montado
USER_ID=$(stat -c %u /var/www/html)
GROUP_ID=$(stat -c %g /var/www/html)

# Alterar o UID e GID do usuário www-data (supondo que o backend esteja rodando como www-data)
usermod -u $USER_ID www-data
groupmod -g $GROUP_ID www-data

# Garantir que o diretório tenha as permissões corretas
chown -R www-data:www-data /var/www/html

# Executar o comando original (CMD no Dockerfile)
#exec "$@"
# Iniciar o PHP-FPM
php-fpm