echo "Clearing code dump & swagger..."
z:\xampp\php\php composer.phar dump-autoload
z:\xampp\php\php artisan l5-swagger:generate

echo "Cache cleaning..."
z:\xampp\php\php artisan cache:clear
z:\xampp\php\php artisan config:clear
z:\xampp\php\php artisan route:clear
echo "Cache cleared."

echo "Generating Laravel App Key..."
z:\xampp\php\php artisan key:generate --force --quiet
echo "Generated Laravel App Key"

echo "Generating JWT Secret..."
z:\xampp\php\php artisan jwt:secret --force --quiet
echo "Generated JWT Secret Key"

echo "Creating DB..."
z:\xampp\php\php artisan migrate:refresh
echo "DB created."

echo "Creating meta and dummy data ..."
z:\xampp\php\php artisan module:seed Core
z:\xampp\php\php artisan module:seed Contact
z:\xampp\php\php artisan module:seed User
z:\xampp\php\php artisan module:seed Preference
z:\xampp\php\php artisan module:seed Subscription
z:\xampp\php\php artisan module:seed Account
z:\xampp\php\php artisan module:seed ServiceRequest
rem z:\xampp\php\php artisan module:seed Wallet
echo "Data created..."