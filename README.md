# Getting Started

## System requirements

- PHP >= 8.1.x
- Composer
- Symfony CLI
- PostgreSql

## Instalación

1. Clona este repositorio:

```bash
git clone https://github.com/leonjeff/aaxis-test
```

Then execute

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

And run the app executing the command

```bash
symfony server:start
```

You can access to the available endpoints using a client like postman using the following url's:

### HTTP method endpoints

| Method Type  | Endpoint                          |
| ------------ | --------------------------------- |
| GET          | http://localhost:8000/products    |
| GET by id    | http://localhost:8000/products/id |
| POST         | http://localhost:8000/products    |
| PUT          | http://localhost:8000/products/id |


You can use a payload similar to this:

```bash
[
  {
    "sku": "Producto A",
    "productName": "Nombre roducto A",
    "description": "Descripcion producto A",
    "createdAt": "2023-12-01",
    "updatedTt": "2023-12-05"
  },
  {
    "sku": "Producto B",
    "productName": "Nombre roducto B",
    "description": "Descripcion producto B",
    "createdAt": "2023-12-01",
    "updatedTt": "2023-12-05"
  }
]
```