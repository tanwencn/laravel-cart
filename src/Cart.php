<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */

namespace Tanwencn\Cart;

class Cart
{
    static $scope;

    protected $session;

    protected $events;

    public function __construct(SessionManager $sessionManager, Dispatcher $events)
    {
        $this->session = $sessionManager;
        $this->events = $events;
    }

    public static function scope($scope = null)
    {
        self::$scope = $scope;
    }

    public function add(Model $model, $qty = 1)
    {
        if ($model instanceof Cart) {
            $item = $model;
        } else {
            $item = new Cart([
                'targetable_class' => get_class($model),
                '$targetable_id' => $model->getKey(),
                'qty' => $qty
            ]);
        }

        $items = $this->all();
        if ($items->has($item->getKey())) {
            $item->qty += $items->get($item->getKey())->qty;
        }
        $item->qty = $item->qty>0?:1;

        $this->events->fire('cart.added', $item);

        $items->put($item->getKey(), $item);
var_dump($items->toJson());exit;
        $this->session->put(\Tanwencn\Cart\Cart::$scope."_cart", $items);

        return $item;
    }
}