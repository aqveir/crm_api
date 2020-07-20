echo "Clearing code dump & swagger..."
sudo php composer.phar dump-autoload
sudo php artisan l5-swagger:generate

echo "Cache cleaning..."
sudo php artisan cache:clear
sudo php artisan config:clear
sudo php artisan route:clear
echo "Cache cleared."

echo "Settng system access..."
sudo chown -R www-data:www-data .
sudo chmod -R 777 storage/.
sudo chmod -R 777 public/.
echo "System access granted."

echo "Clearing logs..."
sudo cat /dev/null > storage/logs/laravel.log
echo "Logs cleared."

echo "Creating DB..."
sudo php artisan migrate:refresh
echo "DB created."

echo "Creating meta and dummy data ..."
sudo php artisan module:seed Core
sudo php artisan module:seed Customer
sudo php artisan module:seed Ecommerce
sudo php artisan module:seed Wallet
echo "Data created..."