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

## Fixtures

```shell
docker compose exec phpfpm composer fixtures-load
```

If you run the fixtures, the following user will be available:

| Username            | Password    |
|---------------------|-------------|
| john@example.com    | apassword   |

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
