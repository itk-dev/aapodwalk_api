# Aapodwalk api

## Development

Getting started:

```shell name=install
docker compose pull
docker compose up --detach --remove-orphans
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

## Fixtures

```shell
docker compose exec phpfpm composer fixtures-load
```

If you run the fixtures, the following user will be available:

| Username            | Password    |
|---------------------|-------------|
| admin@example.com    | apassword   |

And this token for the frontend:

| Name     | Token  |
|----------|--------|
| api user | 123    |

For creating more users, run the following command

```shell
docker compose exec phpfpm bin/console app:user:add EMAIL PASSWORD
```

## Lint lint lint
```shell
docker compose exec phpfpm composer coding-standards-apply
```

Or perhaps:

```shell
docker compose exec phpfpm composer coding-standards-check
```

## Update api spec

```shell
docker compose exec phpfpm composer update-api-spec
```
