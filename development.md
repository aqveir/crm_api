# Development Details

## Environment

## Packages
- Modules: Refer this https://nwidart.com/laravel-modules/v6/introduction (Github: https://github.com/nWidart/laravel-modules)

### Modules CLI
```sh
z:\xampp\php\php artisan module:make <module_name>
z:\xampp\php\php artisan module:make-migration create_wallet_user_table <module_name>
z:\xampp\php\php artisan module:make-model Post <module_name>
z:\xampp\php\php artisan module:make-notification NotifyAdminOfNewComment <module_name>
z:\xampp\php\php artisan module:make-event BlogPostWasUpdated <module_name>
z:\xampp\php\php artisan module:make-listener NotifyUsersOfANewPost <module_name> --event=PostWasCreated
z:\xampp\php\php artisan module:make-listener NotifyUsersOfANewPost <module_name> --event=PostWasCreated --queued
z:\xampp\php\php artisan module:make-mail SendWeeklyPostsEmail <module_name>
z:\xampp\php\php artisan module:make-controller PostController <module_name>
z:\xampp\php\php artisan module:make-request Frontend/GetBlogPostsRequest <module_name>
z:\xampp\php\php artisan module:make-policy PolicyName Blog
```

## Migrations
php artisan migrate:reset
php artisan migrate:refresh --seed

## Seed
php artisan make:seeder Backend\CountriesTableSeeder

php artisan migrate:fresh --seed
z:\xampp\php\php composer.phar dump-autoload

## Swagger Documentation Regenarate
php artisan l5-swagger:generate