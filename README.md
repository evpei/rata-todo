# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework, with full [HTTP/2](https://symfony.com/doc/current/weblink.html), HTTP/3 and HTTPS support.

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started (Docker-Compose Setup)

##### Note:
 - If the default http (80), https (443), or postgresql (5432) ports are already used on your local machine, you can overwrite ports that are being exposed using the the `HTTP_PORT`, `HTTPS_PORT`, and `DATASBASE_PORT` environment vaiables.

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Run `docker compose build --pull --no-cache` to build fresh images
3. Run `docker compose --env_file=path/to/.env up` (the logs will be displayed in the current shell).
   - Note: The .env file should be loaded by default. If you are using a seperate .env file, dont forget to add the path when running the `up` command
4. Open `https://localhost`  in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)(dont forget to use the correct port if you defined a different HTTPS Port to expose.)
1.. Run `docker-compose down --remove-orphans` to stop the Docker containers.

The Database 

## Credits

Created by [Kévin Dunglas](https://dunglas.fr), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
