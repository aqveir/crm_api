echo "Clearing code dump & swagger..."
sudo php composer.phar dump-autoload
sudo php artisan l5-swagger:generate

echo "Cache cleaning..."
sudo php artisan cache:clear
sudo php artisan config:clear
sudo php artisan route:clear
echo "Cache cleared."

echo "Generating JWT Secret..."
sudo php artisan jwt:secret
echo "Generated JWT Secret Key"

echo "Creating DB..."
sudo php artisan migrate:refresh
echo "DB created."

echo "Creating meta and dummy data ..."
sudo php artisan module:seed Core
sudo php artisan module:seed Contact
sudo php artisan module:seed User
sudo php artisan module:seed Preference
echo "Data created..."