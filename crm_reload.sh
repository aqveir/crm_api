local expres="${1:-false}"    # Default value is false

echo "Clearing code dump & swagger..."
php composer.phar dump-autoload
php artisan l5-swagger:generate

echo "Cache cleaning..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo "Cache cleared."

echo "Settng system access..."
chown -R apache:apache .
chmod -R 777 storage/.
chmod -R 777 public/.
echo "System access granted."

echo "Clearing logs..."
sudo cat /dev/null > storage/logs/laravel.log
echo "Logs cleared."

echo "Generating Laravel App Key..."
php artisan key:generate --force --quiet
echo "Generated Laravel App Key"

echo "Generating JWT Secret..."
php artisan jwt:secret --force --quiet
echo "Generated JWT Secret Key"

if "$expres"; then
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
fi