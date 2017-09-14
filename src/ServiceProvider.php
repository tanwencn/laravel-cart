<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */
namespace Tanwen\Cart;

use Illuminate\Support\Facades\Auth;
use Tanwen\Cart\Drives\Mysql;
use Tanwen\Cart\Drives\Session;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
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
        //
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/cart.php';
        $this->mergeConfigFrom($configPath, 'cart');
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        $this->app->singleton('cart', function () {
            Cart::drive('guest', function () {
                return new Session();
            });
            Cart::drive('user', function ($user_id) {
                return new Mysql($user_id);
            });

            if (Auth::check()) {
                return Cart::scene('user', ['user_id' => Auth::id()]);
            } else {
                return Cart::scene('guest');
            }
        });
    }
}
