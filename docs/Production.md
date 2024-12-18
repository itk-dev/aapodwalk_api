# Production

Edit `.env.local` and define some local variables:

``` shell
# .env.local

APP_ENV=prod

# See https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
DATABASE_URL="mysql://db:db@mariadb:3306/db?serverVersion=10.11.10-MariaDB&charset=utf8mb4"
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

MAILER_DSN="smtp://host.docker.internal:25?verify_peer=false"
MAILER_FROM="Aapodwalk <no-reply@example.com>"

# Variables for tasks
TASK_SITE_DOMAIN=aapodwalk_api.example.com
TASK_COMPOSER_INSTALL_ARGUMENTS='--no-dev --classmap-authoritative'
TASK_DOCKER_COMPOSE='docker compose --file docker-compose.server.yml --file docker-compose.server.override.yml'

# Optional (default defined in .env)
COMPOSE_PROJECT_NAME=backspace-podwalk-api
# Used in docker-compose.server.yml
COMPOSE_SERVER_DOMAIN=admin.backspace.srvitkstgweb01.itkdev.dk

# Or, alternatively
TASK_DOCKER_COMPOSE=itkdev-docker-compose-server
```

Check that you can connect to the database:

``` shell
task compose -- pull
task compose -- up --detach --remove-orphans
task composer-install
task console -- dbal:run-sql "SELECT NOW(), DATABASE()"
```

and that you can send email (used to unforget passwords):

``` shell
task console -- mailer:test --from=«the value of MAILER_FROM set in .env.local»
```

> [!TIP]
> Run
>
> ``` shell
> task console -- debug:container --env-var=MAILER_FROM
> ```
>
> to see the current value of `MAILER_FROM`.

If successful, install and update site:

``` shell
task console -- asset-map:compile
# https://symfony.com/doc/current/frontend/asset_mapper.html#serving-assets-in-dev-vs-prod
task site:update
task site:url
```

## Upgrading

To upgrade an already installed site, run

``` shell
task console -- asset-map:compile
task site:update
```
