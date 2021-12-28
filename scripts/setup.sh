#!/bin/bash
echo -e "# Set up dev environment"
php bin/console doctrine:database:drop --if-exists --force --env=dev
php bin/console doctrine:database:create --env=dev
php bin/console doctrine:migrations:migrate --no-interaction --env=dev
# php bin/console app:load-csv
echo -e " --> DONE\n"
