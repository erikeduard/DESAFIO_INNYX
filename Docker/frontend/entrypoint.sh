#!/bin/sh

# Pegar o UID e GID do diretório montado
USER_ID=$(stat -c %u /home/node/app)
GROUP_ID=$(stat -c %g /home/node/app)

# Alterar o UID e GID do usuário www-data (supondo que o backend esteja rodando como www-data)
usermod -u $USER_ID node
groupmod -g $GROUP_ID node

# Garantir que o diretório tenha as permissões corretas
chown -R node:node /home/node/app

tail -f /dev/null