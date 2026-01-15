#!/bin/bash

# Function to run the DB Migrations and Data Seeding
migrateFunction() {
    ARG_DB_MIGRATE="${1:-false}"    # Default value is false
    ARG_DB_SEED="${2:-false}"       # Default value is false

    echo $ARG_DB_MIGRATE
    echo $ARG_DB_SEED

    echo "Creating DB..."
    php artisan migrate:refresh
    echo "DB created."

    echo "Creating meta and dummy data ..."
    php artisan module:seed Core
    php artisan module:seed Contact
    php artisan module:seed User
    php artisan module:seed Preference
    php artisan module:seed Subscription
    php artisan module:seed Account
    php artisan module:seed ServiceRequest
    echo "Data created..."
}

echo "Clearing code dump & swagger..."
php composer.phar dump-autoload
php artisan l5-swagger:generate

echo "Cache cleaning..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo "Cache cleared."

echo "Settng system access..."
sudo chown -R www-data:www-data .
sudo chmod -R 775 .
sudo chmod -R 775 bootstrap/cache
sudo chmod -R 775 scripts
sudo chmod -R 775 storage
sudo chmod -R 775 public
echo "System access granted."

echo "Caching API routes..."
php artisan api:cache
echo "API routes cached."

echo "Clearing logs..."
sudo cat /dev/null > storage/logs/laravel.log
echo "Logs cleared."

echo "Generating Laravel App Key..."
php artisan key:generate --force --quiet
echo "Generated Laravel App Key"

echo "Generating JWT Secret..."
php artisan jwt:secret --force --quiet
echo "Generated JWT Secret Key"

if [ $1 == true ]; then
    migrateFunction $1 $2
fi
