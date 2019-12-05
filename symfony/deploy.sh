#!/bin/bash

if [ $(hostname) == "demo.gns-it.com" ]; then

    # Clear cache
    rm -rf var/cache/dev
    rm -rf var/cache/prod
    rm -rf var/cache/test
    echo -e "Clearing the cache was successfully done."

    # Clear logs
    rm -rf var/log/dev.log
    rm -rf var/log/prod.log
    rm -rf var/log/test.log
    echo -e "Clearing the logs was successfully done."

    # Composer install
    /usr/bin/php73 /usr/bin/composer install #> /dev/null 2>&1
    echo -e "Composer install done."

    # Update DB
    /usr/bin/php73 bin/console d:s:u --force
    echo -e "Database was updated successfully"

    /usr/bin/php73 bin/console d:m:m --no-interaction
    echo -e "Migrations was migrated successfully"

    # Set needed permissions for app folders
    chmod 0777 -R var/cache/ var/log/

else

    composer install
    echo -e "Composer install done."

    php bin/console d:s:u --force
    echo -e "Database was updated successfully"

    php bin/console d:m:m --no-interaction
    echo -e "Migrations was migrated successfully"

fi
