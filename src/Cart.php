<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */

namespace Tanwencn\Cart;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Session\SessionManager;
use Tanwencn\Cart\Models\Cart as CartModel;

class Cart
{
    static $scope = 'default';

    protected $session;

    protected $events;

    protected $auth;

    public function __construct(SessionManager $sessionManager, Dispatcher $events, AuthManager $auth)
    {
        $this->session = $sessionManager;
        $this->events = $events;
        $this->auth = $auth;
    }

    public static function scope($scope = 'default')
    {
        self::$scope = $scope;
    }

    protected function cacheKey()
    {
        return "cart." . self::$scope;
    }

    public function put(Model $model, $qty = 1, $cover=false)
    {
        if ($model instanceof CartModel) {
            $item = $model;
            self::scope($item->scope);
        } else {
            $item = new CartModel([
                'cartable_type' => get_class($model),
                'cartable_id' => $model->getKey(),
                'qty' => $qty
            ]);
        }

        $items = $this->get();
        if (!$cover && $items->has($item->getCartKey())) {
            $item->qty += $items->get($item->getCartKey())->qty;
        }

        $item->qty = $item->qty > 0 ? $item->qty : 1;

        $items->put($item->getCartKey(), $item);

        $this->session->put($this->cacheKey(), $items);

        return $item;
    }

    public function get()
    {
        if (is_null($this->session->get($this->cacheKey()))) {
            return new Items([]);
        }
        return $this->session->get($this->cacheKey());
    }

    public function save()
    {
        if (!$this->auth->check()) return false;

        $user_id = $this->auth->id();

        CartModel::where('user_id', $user_id)->delete();

        foreach ($this->get() as $item) {
            $item->user_id = $user_id;
            CartModel::create($item->only(['user_id', 'qty', 'cartable_type', 'cartable_id']));
        }

    }

    public function sync()
    {
        if (!$this->auth->check()) return false;

        $user_id = $this->auth->id();

        $models = CartModel::where('user_id', $user_id)->get();


        foreach ($models as $model) {
            self::put($model);
        }

        $this->save();
    }

    public function forget($key)
    {
        $items = $this->get();
        $items->pull($key);
        $this->session->put($this->cacheKey(), $items);
    }

    public function flush()
    {
        $this->session->forget($this->cacheKey());
    }
}