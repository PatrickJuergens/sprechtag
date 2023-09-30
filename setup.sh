#!/bin/sh

php bin/console doctrine:database:drop --if-exists --force
php bin/console doctrine:database:create
php bin/console --no-interaction doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --append
