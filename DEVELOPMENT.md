# Development Details

## Environment

## Packages
- Modules: Refer this https://nwidart.com/laravel-modules/v6/introduction (Github: https://github.com/nWidart/laravel-modules)

### Modules CLI
The CLI commands for some of the core activities are given below. For more details refer the documentation provided by the author of the laravel-modules.

```sh
php artisan module:make <module_name>
php artisan module:make-migration create_wallet_user_table <module_name>
php artisan module:make-model Post <module_name>
php artisan module:make-notification NotifyAdminOfNewComment <module_name>
php artisan module:make-event BlogPostWasUpdated <module_name>
php artisan module:make-listener NotifyUsersOfANewPost <module_name> --event=PostWasCreated
php artisan module:make-listener NotifyUsersOfANewPost <module_name> --event=PostWasCreated --queued
php artisan module:make-mail SendWeeklyPostsEmail <module_name>
php artisan module:make-controller PostController <module_name>
php artisan module:make-request Frontend/GetBlogPostsRequest <module_name>
php artisan module:make-policy PolicyName <module_name>
php artisan module:make-resource PostResource <module_name>
```

## Migrations
```sh
php artisan migrate:reset
php artisan migrate:refresh --seed
```

## Seed
```sh
php artisan make:seeder Backend\CountriesTableSeeder

php artisan migrate:fresh --seed
php composer.phar dump-autoload
```

## Swagger Documentation Regenarate
```sh
php artisan l5-swagger:generate
```