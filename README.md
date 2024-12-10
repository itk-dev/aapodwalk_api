# Aapodwalk api

See [Production](docs/Production.md) for information on how to deploy for production.

## Development

Getting started:

```shell name=install
docker compose pull
docker compose up --detach --remove-orphans
docker compose exec phpfpm composer install
docker compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

## Fixtures

```shell name=fixtures-load
docker compose exec phpfpm composer fixtures-load
```

If you run the fixtures, the following user will be available:

| Username                 | Password | Roles           | API token                       |
|--------------------------|----------|-----------------|---------------------------------|
| <admin@example.com>      | password | ROLE_ADMIN      |                                 |
| <user-admin@example.com> | password | ROLE_USER_ADMIN |                                 |
| <user@example.com>       | password | ROLE_USER       |                                 |
| <api-user@example.com>   | password | ROLE_API        | this-is-not-a-very-secret-token |

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

## API

API documentation is available on `/api/v1/docs`:

``` shell name=api-open-docs
open "http://$(docker compose port nginx 8080)/api/v1/docs"
```

A user must authenticate to actually use the API:

``` shell name=api-request
curl --header 'Authorization: Bearer this-is-not-a-very-secret-token' "http://$(docker compose port nginx 8080)/api/v1/routes"
```

### Update api spec

```shell
docker compose exec phpfpm composer update-api-spec
```

## Translations

Update translations

``` shell name=translations-update
docker compose exec phpfpm composer update-translations
```

Update Danish translations (`translations/*.da.xlf`), e.g. using [Poedit](https://poedit.net/):

``` shell
open translations/admin.da.xlf
```

To help the [translation
extractor](https://symfony.com/doc/current/translation.html#extracting-translation-contents-and-updating-catalogs-automatically)
find some translations, we have some translation expressions in `templates/admin/_messages.html.twig`.

## Assets

We use [Symfony's AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html) for asset management.
Furthermore, [as suggested by
AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html#can-i-lint-and-format-my-code), we use
[BiomeJsBundle](https://github.com/Kocal/BiomeJsBundle) for linting assets.

See [Serving Assets in dev vs
prod](https://symfony.com/doc/current/frontend/asset_mapper.html#serving-assets-in-dev-vs-prod) for details on how to
handle assets for the `prod` environment.

## Commands

We use [Console Commands](https://symfony.com/doc/current/console.html) for command in the console.

List the app commands with

``` shell
task console -- list app
```
