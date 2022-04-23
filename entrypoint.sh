#!/usr/bin/env bash

echo "running startup set up"
composer install
bin/console cache:clear #no point calling cache:warm as cache:clear also warms up
exec "$@"