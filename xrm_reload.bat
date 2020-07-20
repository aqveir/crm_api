echo "Clearing code dump & swagger..."
z:\xampp\php\php composer.phar dump-autoload
z:\xampp\php\php artisan l5-swagger:generate

echo "Cache cleaning..."
z:\xampp\php\php artisan cache:clear
z:\xampp\php\php artisan config:clear
z:\xampp\php\php artisan route:clear
echo "Cache cleared."

echo "Creating DB..."
z:\xampp\php\php artisan migrate:refresh
echo "DB created."

echo "Creating meta and dummy data ..."
z:\xampp\php\php artisan module:seed Core
z:\xampp\php\php artisan module:seed Customer
z:\xampp\php\php artisan module:seed Ecommerce
z:\xampp\php\php artisan module:seed Wallet
echo "Data created..."