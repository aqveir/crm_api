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
php composer.phar dump-autoload --optimize
```

## Swagger Documentation Regenarate
```sh
php artisan l5-swagger:generate
```

Make sure that you have the latest version of the AWS CLI and Docker installed. For more information, see Getting Started with Amazon ECR .
Use the following steps to authenticate and push an image to your repository. For additional registry authentication methods, including the Amazon ECR credential helper, see Registry Authentication .
Retrieve an authentication token and authenticate your Docker client to your registry.
Use the AWS CLI:

aws ecr-public get-login-password --region us-east-1 | docker login --username AWS --password-stdin public.ecr.aws/v2i8k2o7
Note: If you receive an error using the AWS CLI, make sure that you have the latest version of the AWS CLI and Docker installed.
Build your Docker image using the following command. For information on building a Docker file from scratch see the instructions here . You can skip this step if your image is already built:

docker build -t aqveir .
After the build completes, tag your image so you can push the image to this repository:

docker tag aqveir:latest public.ecr.aws/v2i8k2o7/aqveir:latest
Run the following command to push this image to your newly created AWS repository:

docker push public.ecr.aws/v2i8k2o7/aqveir:latest


CREATE USER 'user_name'@'%' IDENTIFIED BY 'password';

GRANT ALL PRIVILEGES ON db_name.* TO 'user_name'@'%';