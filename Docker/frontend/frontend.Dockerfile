FROM node:20

#USER node
WORKDIR /home/node/app
#COPY frontend/ .
#RUN npm install
#RUN npm install -g @vue/cli


# Copiar o script de entrada
COPY Docker/frontend/entrypoint.sh /usr/local/bin/

# Torná-lo executável
RUN chmod +x /usr/local/bin/entrypoint.sh

# Definir o script de entrada
ENTRYPOINT ["entrypoint.sh"]

#EXPOSE 5173
#CMD ["tail", "-f", "/dev/null"]
#CMD ["npm", "run", "dev"]