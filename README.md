
# Projeto API REST com Yii2

Este projeto é uma API REST construída com Yii2 para gerenciar **clientes (customers)** e **livros (books)**, utilizando Docker para a containerização.

## Funcionalidades
- Gerenciamento de clientes (customers) e livros (books) via API REST.
- Autenticação JWT com tokens Bearer.
- Paginação, ordenação e filtros para listagem de recursos.
- Tratamento de erros com exceções personalizadas.
- Dockerizado para fácil desenvolvimento e implantação.

## Instalação e configuração:

Será necessaŕio ter o Docker e o DockerCompose instalado na sua máquina.
Link para instalção Windows e Mac https://www.docker.com/products/docker-desktop/. O Docker compose ja vem instalado com windows e mac. No linux confira se você ja tem a ultima versão do DockerCompose https://docs.docker.com/compose/install/ 

Aqui estão os passos para iniciar a aplicação e configurar seu ambiente:

### 1. Build e Execução dos Containers em Background

Para executar os containers Docker em segundo plano, utilize o comando abaixo com o parâmetro `-d`:

```bash
docker-compose up -d --build
```

Ao finalizar de montar e executar os containers, a aplicação estará rodando no localhost:8000

### 2. Criação de tabelas no banco usando migrations do yii2

Para criar as tabelas do banco basta executar as migrations dentro do container:

```bash
docker exec -it yii2_api_container ./yii migrate
```
### 2. Criação de usuário via comando no terminal

Podemos usar um comando para adicionar um usuário ao banco:

```bash
docker exec -it yii2_api_container ./yii app/add-user <username> <senha> <nome do usuário>
```
Substitua 'username', 'senha', e 'nome do usuário' pelos valores desejados.
Exemplo:

```bash
docker exec -it yii2_api_container ./yii app/add-user mario 123456 Mario Silva
```

### 3. (Opicional) Semear Books e Costumers

Caso deseje colocar dados nas tabelas Books e Costumers para testar as requesições de listagem, basta usar esses comandos:

Semear Books:

```bash
docker exec -it yii2_api_container ./yii app/seed-books
```
Semear Customers:

```bash
docker exec -it yii2_api_container ./yii app/seed-customers
```

## Rotas de Autenticação
- **POST /login**
  - Faz login com `username` e `password` e retorna `access_token` e `refresh_token`.

  Exemplo de requisição:
  ```makefile

  POST /login HTTP/1.1
  Host: 127.0.0.1:8000
  Content-Type: application/json

  {
    "username": "usuario",
    "password": "senha"
  }
  ```

- **POST /login/refresh_token**
  - aceita um `refresh_token` e retorna um novo`access_token` .

  Exemplo de requisição:
  ```makefile

  POST /login/refresh_token HTTP/1.1
  Host: 127.0.0.1:8000
  Content-Type: application/json

  {
    "refresh_token": "token",
  }
  ```

## Rotas da API

### GET /books 

Obtém uma lista de livros.
**Parâmetros de Query:**  
- `sort`: Define a ordenação dos resultados. Exemplo: `sort=title:ASC`.
 
- `filter`: Aplica filtros aos resultados. Exemplo: `filter[title]=exemplo`.
 
- `limit`: Define o número máximo de resultados retornados. Exemplo: `limit=10`.
 
- `offset`: Define o número de resultados a serem pulados. Exemplo: `offset=0`.
**Exemplo de Requisição:** 

```makefile
GET /books?sort=title:asc&filter[author]=mario&limit=10&offset=0 HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer access_token
```
**Exemplo de Resposta:** 

```json
[
    {
        "id": 1,
        "isbn": "9788567375113",
        "title": "titulo_livro",
        "author": "autor",
        "price": 10.00,
        "stock": 100
    },
    {
        "id": 2,
        "isbn": "9788567375114",
        "title": "outro_titulo_livro",
        "author": "outro_autor",
        "price": 20.00,
        "stock": 50
    }
    // Outros livros...
]
```

### POST /books 

Cria um novo livro.

**Obs:** Os livros são validados e preenchidos conforme a ISBN. Portanto só será aceitos livros com ISBN Validas
https://brasilapi.com.br/docs#tag/ISBN

**Exemplo de Requisição:** 

```makefile
POST /books HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer access_token
Content-Type: application/json

{
  "isbn": "9788567375113",
  "title": "titulo_livro",
  "author": "autor",
  "price": 10.00,
  "stock": 100
}
```
**Exemplo de Resposta:** **Resposta Sucesso (201 Created):** 

```json
{
  "id": 1,
  "isbn": "9788567375113",
  "title": "titulo_livro",
  "author": "autor",
  "price": 10.00,
  "stock": 100
}
```
**Resposta de Erro (400 Bad Request):** 

```json
{
  "errors": {
    "isbn": ["ISBN must be 13 characters."],
    "price": ["Price must be a number."],
    "stock": ["Stock must be an integer."]
  }
}
```

---

### GET /customers 

Obtém uma lista de clientes.
**Parâmetros de Query:**  
- `sort`: Define a ordenação dos resultados. Exemplo: `sort=name:asc`.
 
- `filter`: Aplica filtros aos resultados. Exemplo: `filter[name]=mario`.
 
- `limit`: Define o número máximo de resultados retornados. Exemplo: `limit=10`.
 
- `offset`: Define o número de resultados a serem pulados. Exemplo: `offset=0`.
**Exemplo de Requisição:** 

```makefile
GET /customers?sort=name:asc&filter[name]=mario&limit=10&offset=0 HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer access_token
```
**Exemplo de Resposta:** **Resposta Sucesso (200 OK):** 

```json
[
  {
    "id": 1,
    "cpf": "12345678991",
    "cep": "11075330",
    "name": "mario",
    "street": "rua",
    "number": "02",
    "city": "cidade",
    "state": "RJ",
    "sex": "M"
  },
  {
    "id": 2,
    "cpf": "98765432100",
    "cep": "22050030",
    "name": "outrocliente",
    "street": "rua exemplo",
    "number": "123",
    "city": "cidadeexemplo",
    "state": "SP",
    "sex": "F"
  }
]
```

### POST /customers 

Cria um novo cliente.

**Obs:** O endereço é validado e preenchidos conforme o CEP. Portanto só será aceitos CEPs validos
https://brasilapi.com.br/docs#tag/CEP-V2

**Obs:** O CPF é validado for regex. Portanto só será aceitos CPFs validos. Caso deseje gerar um CPF valido para teste:
https://www.4devs.com.br/gerador_de_cpf

**Exemplo de Requisição:** 

```makefile
POST /customers HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer access_token
Content-Type: application/json

{
  "cpf": "12345678991",
  "cep": "11075330",
  "name": "hseuashue",
  "street": "as",
  "number": "as",
  "city": "as",
  "state": "XD",
  "sex": "M"
}
```
**Exemplo de Resposta:** **Resposta Sucesso (201 Created):** 

```json
{
  "id": 1,
  "cpf": "12345678991",
  "cep": "11075330",
  "name": "hseuashue",
  "street": "as",
  "number": "as",
  "city": "as",
  "state": "XD",
  "sex": "M"
}
```
**Resposta de Erro (400 Bad Request):** 

```json
{
  "errors": {
    "cpf": ["CPF must be 11 digits."],
    "cep": ["CEP must be a valid format."],
    "name": ["Name is required."],
    "street": ["Street is required."],
    "number": ["Number is required."],
    "city": ["City is required."],
    "state": ["State must be 2 characters."],
    "sex": ["Sex must be either 'M' or 'F'."]
  }
}
```

---

### A Api também conta com outras endpoints de CRUD, aqui está uma lista de rotas que podemos utilizar:

| Endpoint      | Descrição   |
| ------------- | ------------- |
| `GET /books` | Obtenha todos os livros|
| `GET /books/:bookId` | Obtenha um livro pelo seu Id |
| `POST /books` | Cria um livro |
| `PUT /books/:bookId` | Edita um livro pelo seu Id|
| `DELETE /books/:bookId` | Deleta um livro pelo seu Id|
| `GET /customer` | Obtenha todos os clientes|
| `GET /customer/:customerId` | Obtenha um cliente pelo seu Id |
| `POST /customer` | Cria um cliente|
| `PUT /customer/:customerId` | Edita um cliente pelo seu Id|
| `DELETE /customer/:customerId` | Deleta um cliente pelo seu Id|


---

## Observações e Considerações

- Para fins de conveniência para quem for testar, optei por definir as variáveis de ambiente diretamente no arquivo docker-compose.yml. Esta abordagem facilita a configuração rápida do ambiente de desenvolvimento, mas não é considerada a melhor prática para ambientes de produção.
- Qualquer dúvida entrar em contato ferprimoso@gmail.com

