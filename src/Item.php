<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/12
 * Time: 10:10
 */

namespace Tanwen\Cart;


use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tanwen\Cart\Exceptions\CartMessageException;

class Item
{
    public $id;

    public $quantity;

    private $_price;

    private $_subtotal;

    private $_model;

    private $_modelName;

    private $_status;

    public function __construct($id, $quantity)
    {
        $this->id = $id;
        $this->quantity = $quantity;
        $this->_modelName = config('cart.item.model');
    }

    public function hasModel()
    {
        return !empty($this->_modelName);
    }

    public function model()
    {
        if(empty($this->_model)) {
            $model = $this->_modelName;
            try {
                $this->_model = $model ? $model::findOrFail($this->id) : null;
                $priceField = config('cart.item.fieldPrice');
                $stockField = config('cart.item.fieldStock');
                !empty($priceField) && $this->setPrice($this->model()->$priceField);

                if (!empty($stockField)) {
                    $this->model()->$stockField < $this->quantity && $this->_status = 1;
                }

            } catch (ModelNotFoundException $exception) {
                $this->_status = 2;
            }
        }
        return $this->_model;
    }

    public function setPrice($price)
    {
        $this->_price = number_format($price, 2);
        $this->_subtotal = number_format($this->quantity * $this->_price, 2);
    }

    public function getPrice()
    {
        return $this->_price;
    }

    public function getTotal()
    {
        return $this->_subtotal;
    }

    public function hasValidity()
    {
        return intval($this->_status) == 0;
    }

    public function getStatus()
    {
        $arr = ['正常', '库存不足', '商品无效或已下架'];
        return $arr[intval($this->_status)];
    }
}