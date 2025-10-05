<?php

namespace Modules\Boilerplate\Providers;

use RuntimeException;
use Modules\Boilerplate\Auth\Auth;
use Modules\Boilerplate\Dispatcher;
use Modules\Boilerplate\Http\Request;
use Modules\Boilerplate\Http\Response;
use Modules\Boilerplate\Console\Command;
use Modules\Boilerplate\Exception\Handler as ExceptionHandler;
use Modules\Boilerplate\Transformer\Factory as TransformerFactory;

class BoilerplateServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setResponseStaticInstances();

        Request::setAcceptParser($this->app[\Modules\Boilerplate\Http\Parser\Accept::class]);

        $this->app->rebinding('api.routes', function ($app, $routes) {
            $app['api.url']->setRouteCollections($routes);
        });
    }

    protected function setResponseStaticInstances()
    {
        Response::setFormatters($this->config('formats'));
        Response::setFormatsOptions($this->config('formatsOptions'));
        Response::setTransformer($this->app['api.transformer']);
        Response::setEventDispatcher($this->app['events']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $this->registerClassAliases();

        $this->app->register(RoutingServiceProvider::class);

        $this->app->register(HttpServiceProvider::class);

        $this->registerExceptionHandler();

        $this->registerDispatcher();

        $this->registerAuth();

        $this->registerTransformer();

        //$this->registerDocsCommand();

        if (class_exists('Illuminate\Foundation\Application', false)) {
            $this->commands([
                \Modules\Boilerplate\Console\Command\Cache::class,
                \Modules\Boilerplate\Console\Command\Routes::class,
            ]);
        }
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(base_path('config/api.php'), 'api');

        if (! $this->app->runningInConsole() && empty($this->config('prefix')) && empty($this->config('domain'))) {
            throw new RuntimeException('Unable to boot ApiServiceProvider, configure an API domain or prefix.');
        }
    }

    /**
     * Register the class aliases.
     *
     * @return void
     */
    protected function registerClassAliases()
    {
        $serviceAliases = [
            \Modules\Boilerplate\Http\Request::class => \Modules\Boilerplate\Contract\Http\Request::class,
            'api.dispatcher' => \Modules\Boilerplate\Dispatcher::class,
            'api.http.validator' => \Modules\Boilerplate\Http\RequestValidator::class,
            'api.http.response' => \Modules\Boilerplate\Http\Response\Factory::class,
            'api.router' => \Modules\Boilerplate\Routing\Router::class,
            'api.router.adapter' => \Modules\Boilerplate\Contract\Routing\Adapter::class,
            'api.auth' => \Modules\Boilerplate\Auth\Auth::class,
            'api.limiting' => \Modules\Boilerplate\Http\RateLimit\Handler::class,
            'api.transformer' => \Modules\Boilerplate\Transformer\Factory::class,
            'api.url' => \Modules\Boilerplate\Routing\UrlGenerator::class,
            'api.exception' => [
                \Modules\Boilerplate\Exception\Handler::class, 
                \Modules\Boilerplate\Contract\Debug\ExceptionHandler::class
            ],
        ];

        foreach ($serviceAliases as $key => $aliases) {
            foreach ((array) $aliases as $alias) {
                $this->app->alias($key, $alias);
            }
        }
    }

    /**
     * Register the exception handler.
     *
     * @return void
     */
    protected function registerExceptionHandler()
    {
        $this->app->singleton('api.exception', function ($app) {
            return new ExceptionHandler($app['Illuminate\Contracts\Debug\ExceptionHandler'], $this->config('errorFormat'), $this->config('debug'));
        });
    }

    /**
     * Register the internal dispatcher.
     *
     * @return void
     */
    public function registerDispatcher()
    {
        $this->app->singleton('api.dispatcher', function ($app) {
            $dispatcher = new Dispatcher($app, $app['files'], $app[\Modules\Boilerplate\Routing\Router::class], $app[\Modules\Boilerplate\Auth\Auth::class]);

            $dispatcher->setSubtype($this->config('subtype'));
            $dispatcher->setStandardsTree($this->config('standardsTree'));
            $dispatcher->setPrefix($this->config('prefix'));
            $dispatcher->setDefaultVersion($this->config('version'));
            $dispatcher->setDefaultDomain($this->config('domain'));
            $dispatcher->setDefaultFormat($this->config('defaultFormat'));

            return $dispatcher;
        });
    }

    /**
     * Register the auth.
     *
     * @return void
     */
    protected function registerAuth()
    {
        $this->app->singleton('api.auth', function ($app) {
            return new Auth($app[\Modules\Boilerplate\Routing\Router::class], $app, $this->config('auth'));
        });
    }

    /**
     * Register the transformer factory.
     *
     * @return void
     */
    protected function registerTransformer()
    {
        $this->app->singleton('api.transformer', function ($app) {
            return new TransformerFactory($app, $this->config('transformer'));
        });
    }

    /**
     * Register the documentation command.
     *
     * @return void
     */
    protected function registerDocsCommand()
    {
        $this->app->singleton(\Modules\Boilerplate\Console\Command\Docs::class, function ($app) {
            return new Command\Docs(
                $app[\Modules\Boilerplate\Routing\Router::class],
                $app[\Dingo\Blueprint\Blueprint::class],
                $app[\Dingo\Blueprint\Writer::class],
                $this->config('name'),
                $this->config('version')
            );
        });

        $this->commands([\Modules\Boilerplate\Console\Command\Docs::class]);
    }
}
