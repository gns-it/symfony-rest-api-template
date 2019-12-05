# Docker Symfony 4 (PHP7-FPM - NGINX - MySQL - RABBIT_MQ)


## Installation

1. Create a `.env` from the `.env.dist` file. Adapt it according to your symfony application

    ```bash
    cp .env.dist .env
    ```

2. Build/run containers with (with and without detached mode)

    ```bash
    $ docker-compose build
    $ docker-compose up -d
    ```
3. Update your system host file (add honey_moon.local)

4. Set $SYMFONY_APP_PATH (inside .env full path to symfony dir)

5. Prepare Symfony app
    
    5.1. Composer install & create database

        ```bash
        $ docker-compose exec php bash
        $ composer install
        $ sf doctrine:database:create
        $ sf doctrine:schema:update --force
        # Only if you have `doctrine/doctrine-fixtures-bundle` installed
        $ sf doctrine:fixtures:load --no-interaction
        ```

6. Enjoy :-)

## Useful commands

* Build assembly `docker-compose build`

* Run assembly `docker-compose up -d`

* Run assembly except node container `docker-compose run --rm start_dependencies`

* Stop assembly `docker-compose stop`

* To enter php container `$docker-compose exec php bash` `sf` alias configured for `php bin/console`

* Run supervisor: `supervisord -c /etc/supervisor/conf.d/private_island.conf `

* Stop supervisor: `kill -s SIGTERM "$(< /tmp/supervisord.pid)"`
