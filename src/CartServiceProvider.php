<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */
namespace Tanwencn\Cart;

class CartServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    //protected $defer = true;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../migrations' => database_path('migrations'),
            ], 'tanwencms');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {

        $this->app->singleton('cart', function ($app) {
            return new Cart($app['session'], $app['events'], $app['auth']);
        });
    }
}
