# Aapodwalk api

## Development

Getting started:

```shell
docker compose up -d
docker compose exec phpfpm composer install
```

Install database

```shell
docker compose exec phpfpm bin/console doctrine:migrations:migrate
```

Load fixtures:

```shell
docker compose exec phpfpm composer fixtures-load
```

Create an admin user:

```shell
docker compose exec phpfpm bin/console app:user:add admin@aarhus.dk apassword
```
