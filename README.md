# E-commerce API

API REST de e-commerce desenvolvida para portfólio. O projeto gerencia catálogo, marcas, categorias, endereços e pedidos, com autenticação JWT, permissões administrativas, controle de estoque e upload de imagens.

## Tecnologias

- PHP 8.1+
- Laravel 10
- MySQL 8
- JWT (`tymon/jwt-auth`)
- Docker Compose para o banco de testes
- Postman

## Principais recursos

- Cadastro e autenticação de usuários via JWT.
- Catálogo público de produtos.
- Gestão administrativa de produtos, marcas e categorias.
- Upload de imagens no disco público do Laravel.
- Endereços vinculados ao usuário autenticado.
- Criação de pedidos com cálculo de preço no servidor, validação de estoque e transação de banco.
- Policies para limitar operações ao proprietário do recurso ou a administradores.
- Respostas JSON padronizadas com API Resources e `ApiResponse`.

## Instalação

Instale as dependências e crie o arquivo de ambiente:

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

Configure no `.env` uma base MySQL local para desenvolvimento:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

Depois execute as migrations, o seeder e o link público para arquivos enviados:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Usuário administrador de demonstração

O `DatabaseSeeder` cria o usuário abaixo quando executado:

| Campo | Valor |
| --- | --- |
| E-mail | `admin@ecommerce.test` |
| Senha | `password` |

Essas credenciais são destinadas apenas ao ambiente local/de demonstração.

## Autenticação JWT

Faça login em `POST /api/auth/login` com e-mail e senha. A resposta contém o `access_token`.

Envie esse token nas rotas protegidas:

```http
Authorization: Bearer {access_token}
```

Rotas públicas:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/produtos`
- `GET /api/produtos/{product}`

As demais rotas exigem autenticação. Ações de catálogo administrativo e gestão de pedidos são avaliadas pelas Policies: apenas administradores podem executar operações administrativas; endereços e pedidos são acessíveis somente pelo proprietário, salvo permissões administrativas específicas.

## Regras de negócio do pedido

- O cliente envia somente produtos, quantidades, endereço e data de entrega.
- Preços e total são calculados no backend a partir do catálogo atual.
- O estoque é verificado e bloqueado durante a criação do pedido.
- Pedido, itens e baixa de estoque são executados em uma única transação.
- Uma solicitação com estoque insuficiente é rejeitada sem criar pedido ou alterar inventário.

## Testes

O projeto usa um MySQL isolado na porta `3307` para que a suíte nunca utilize o banco de desenvolvimento.

```bash
docker compose -f docker-compose.testing.yml up -d --wait
php artisan test
```

Para recriar o banco de demonstração usado nos testes:

```bash
php artisan migrate:fresh --seed --env=testing
```

A cobertura atual inclui autenticação, autorização administrativa, Policies, catálogo, criação de produto com imagem, criação de pedido, cálculo de total e estoque insuficiente.

## Postman

Importe a collection [Ecommerce.postman_collection.json](postman/Ecommerce.postman_collection.json) no Postman.

A collection contém as rotas atuais e variáveis para URL, token JWT e IDs. Execute **Login e salvar token** antes de testar endpoints protegidos. Para criar ou alterar produtos, selecione um arquivo no campo `image` da requisição multipart.

## Estrutura da API

| Recurso | Rotas principais |
| --- | --- |
| Autenticação | `/api/auth/*` |
| Produtos | `/api/produtos` |
| Marcas | `/api/marcas` |
| Categorias | `/api/categorias` |
| Endereços | `/api/enderecos` |
| Compras | `/api/compras` |

## Licença

Projeto desenvolvido para fins de portfólio.
