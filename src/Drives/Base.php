<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */

namespace Tanwen\Cart\Drives;

use Tanwen\Cart\Exceptions\CartMessageException;
use Tanwen\Cart\Item;


abstract class Base
{
    public function formatItem(int $goods_id, int $quantity)
    {
        $item = new Item($goods_id, $quantity);
        $item->hasModel() && $item->model();
        return $item;
    }

    public function add(int $goods_id, int $quantity, int $shop_id = 0)
    {
        $item = $this->formatItem($goods_id, $quantity);

        if (!$item->hasValidity()) {
            throw new CartMessageException($item->getStatus());
        }

        $data = $this->_all();
        $item = &$data[$shop_id][$goods_id];
        if (!empty($item)) { //商品存在时
            $item['quantity'] += $quantity;
            if (intval($item['quantity']) < 1) {
                throw new CartMessageException('商品数量不能小于1');
            }
        } else {
            $item = [
                'goods_id' => $goods_id,
                'quantity' => $quantity
            ];
        }

        $this->_save($data);
    }

    public function items(\Closure $closure = null)
    {
        $data = $this->_all();
        $results = [];
        if(!empty($data)) {
            foreach ($data as $shop_id => $cart) {
                foreach ($cart as $goods_id => $val) {
                    $item = $item = $this->formatItem($goods_id, $val['quantity']);
                    if (empty($closure) || $closure($item)) {
                        $results[$shop_id][$goods_id] = $item;
                    }
                }
            }
        }

        return $results;
    }

    public function delete(int $goods_id, $shop_id = 0)
    {
        $data = $this->_all();
        if (isset($data[$shop_id][$goods_id])) {
            unset($data[$shop_id][$goods_id]);
            if (empty($data[$shop_id])) {
                unset($data[$shop_id]);
                if (empty($data)) {
                    $this->flush();
                }
            }
        }

        if (!empty($data)) {
            $this->_save($data);
        }
    }
}