# DESAFIO_INNYX

### projeto consiste em uma aplicação API REST que está sendo implementado usando containers Dockers.

- **Comando para iniciar o projeto no modo de desenvolvimento**
    ```
    sudo docker-compose up -d --build
    ```

- **Comando para DESTRUIR containers - Containers definidos no docker-compose.yml**
    ```
    sudo docker-compose down
    ou
    sudo docker compose down
    ```

- **Comando para LISTAR todos os container EM EXECUÇÃO**
    ```
    sudo docker ps
    ```
- **Comando para ACESSAR um container específico**
    ```
    sudo docker exec -it frontend bash
    ou 
    sudo docker compose exec frontend bash

    sudo docker exec -it backend bash
    ou 
    sudo docker compose exec backend bash
    ```
### Atualizando os containers
- **No backend**
```
    composer install
```

- **No frontend**
```
    npm install
```
### Inicializando os servers dos containers.
    
- **No container do backend**
    ```
    php artisan serve --host=0.0.0.0
    ```
- **No container do frontend**
    ```
    npm run dev
    ```

### Executando as migration

- **No container do backend**
    ```
    php artisan migrate
    php artisan db:seed
    ```

### Acessando o sistema.
- **No seu navegador**
```
http://localhost:5179/
```
- **Usuario e senha se encontram no DatabaseSeeder.php**
```
usuário: test@example.com
senha: 123456

```

### Tecnologias usadas.
- **Vue.js v.3**
- **TypeScript**
- **PrimeVue como suporte de UI componentes**
_ **PrimeFlex para utilitário de CSS do PrimeVue**
- **Pinia para store do Vue**
- **Sweetalert2 para pop-up de alertas**
- **Lodash uma biblioteca para manipulação de arrays e objetcs**

### Tela de Login.

![tela de login](<Captura de tela de 2024-04-10 10-39-41.png>)

- **Na tela de login o Usuário é o email cadastrado**

### Tela Inicio.
![Tela Inicio](<Captura de tela de 2024-04-10 12-33-46.png>)

- **No Header temos um menu composto por (Inicio,Cadastro[Usuário,Categoria],User)**
- **Abaixo temos um campo de busca de produtos que pode ser ou por nome do produto ou por descrição do produto**
- **Logo abaixo do buscar, temos um botão de cadastrar onde abrirá uma dialog para cadastro do produto**
- **Dialog do cadastro:**
![Dialog do cadastro](<Captura de tela de 2024-04-10 12-38-43.png>)

- **Mais a baixo temos a lista dos produtos organizados por estilização em grid podendo alterar a visualização entre lista ou grid**

- **Os produtos tem sua visualização organizada da seguinte forma: Imagem, Categoria, Nome, descrição, data de validade e preço. Após temos dois botões, um de alterar os dados do produto e um para excluir um produto.**

- **Dialog do Alterar:**
![Dialog do Alterar](<Captura de tela de 2024-04-10 12-50-57.png>)
- **Parar alterar uma imagem clique em "Choose", escolha a imagem e depois clique em "Upload", e por fim no botão Alterar para fazer as alterações**

- **No botao excluir temos um sweetalert:**
![botao excluir](<Captura de tela de 2024-04-10 12-53-22.png>)

### Tela Cadastro Usuários.
![Tela Cadastro Usuários](<Captura de tela de 2024-04-10 12-58-27.png>)

- **Seguindo a mesma logica da tela inicio, é possivel cadastrar um usuário, alterar e excluir o mesmo**
- **Também é possivel buscar um usuario por nome ou email**

## Tela Cadastro de Categoria
![Tela Cadastro de Categoria](<Captura de tela de 2024-04-10 13-03-09.png>)

- **Segue a mesma logica da tela de usuário**

