#!/bin/bash

cd /ellaisys/solutions/crmomni/$DEPLOYMENT_GROUP_NAME

echo "Clearing code dump & swagger..."
php composer.phar dump-autoload
php artisan l5-swagger:generate

echo "Cache cleaning..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
echo "Cache cleared."

echo "Generating Laravel App Key..."
php artisan key:generate --force --quiet
echo "Generated Laravel App Key"

echo "Generating JWT Secret..."
php artisan jwt:secret --force --quiet
echo "Generated JWT Secret Key"

echo "Creating DB..."
php artisan migrate:refresh
echo "DB created."

echo "Creating meta and dummy data ..."
php artisan module:seed Core
php artisan module:seed Contact
php artisan module:seed User
php artisan module:seed Preference
echo "Data created..."