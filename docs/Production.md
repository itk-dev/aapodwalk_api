# Production

Edit `.env.local` and define some local variables:

``` shell
# .env.local

APP_ENV=prod

# See https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
DATABASE_URL="mysql://db:db@mariadb:3306/db?serverVersion=10.11.10-MariaDB&charset=utf8mb4"
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
MAILER_DSN=smtp://localhost:25

# Variables for tasks
TASK_SITE_DOMAIN=aapodwalk_api.example.com
TASK_COMPOSER_INSTALL_ARGUMENTS='--no-dev --classmap-authoritative'
TASK_DOCKER_COMPOSE='docker compose --env-file .env.docker.local --file docker-compose.server.yml --file docker-compose.server.override.yml'
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

If successful, install and update site:

``` shell
task site:update
task site:url
```
