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
use Illuminate\Support\Collection;
use Tanwencn\Cart\Models\Cart as CartModel;

class Cart
{
    static $scope = 'default';

    protected $session;

    protected $events;

    protected $auth;

    protected $old;

    public function __construct(SessionManager $sessionManager, Dispatcher $events, AuthManager $auth)
    {
        $this->session = $sessionManager;
        $this->events = $events;
        $this->auth = $auth;
        $this->old = $this->all()->toArray();
    }

    public static function scope($scope = 'default')
    {
        self::$scope = $scope;
    }

    protected function cacheKey()
    {
        return "cart." . self::$scope;
    }

    public function put(Model $model, $qty = 1, $cover = false)
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

        if (!$cover && $items->has($item->getItemKey())) {
            $item->qty += $items->get($item->getItemKey())->qty;
        }

        $item->qty = $item->qty > 0 ? $item->qty : 1;

        $items->put($item->getItemKey(), $item);

        return $item;
    }

    protected function get()
    {
        $items = $this->session->get($this->cacheKey());
        if (is_null($items)) {
            $items = new Items();
            $this->session->put($this->cacheKey(), $items);
        }

        return $items;
    }

    public function all()
    {
        $items = $this->get();
        $results = new Items();
        foreach ($items as $key => $item) {
            $copy = $item->replicate();
            $copy->load('cartable');
            if (is_null($copy->cartable)) {
                $items->forget($key);
            } else {
                $results->put($key, $copy);
            }
        }

        return $results;
    }

    protected function save()
    {
        if (!$this->auth->check()) return false;

        if ($this->isChange()) {

            $user_id = $this->auth->id();

            CartModel::where('user_id', $user_id)->delete();

            foreach ($this->get() as $item) {
                $item->user_id = $user_id;
                CartModel::create($item->only(['user_id', 'qty', 'cartable_type', 'cartable_id']));
            }

            $this->setOld($this->all());
        }
    }

    protected function setOld($items)
    {
        $this->old = $items;
    }

    protected function isChange()
    {
        return json_encode($this->all()->toArray()) !== json_encode($this->old);
    }

    public function sync()
    {
        if (!$this->auth->check()) return false;

        $user_id = $this->auth->id();

        $models = CartModel::where('user_id', $user_id)->get();

        foreach ($models as $model) {
            if (!$this->all()->has($model->getItemKey())) {
                self::put($model);
            }
        }

        $this->setOld($models);
    }

    /**
     * Remove an item from the collection by key.
     *
     * @param  string|array $keys
     */
    public function forget($keys)
    {
        $items = $this->get();
        $items->forget($keys);
    }

    public function flush()
    {
        $this->session->forget($this->cacheKey());
    }

    public function __destruct()
    {
        $this->save();
    }

}