<?php
/**
 * 作者: Tanwen
 * 邮箱: 361657055@qq.com
 * 所在地: 广东广州
 * 时间: 2017/10/12 11:02
 */
namespace Tanwencn\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Tanwencn\Cart\Items;

class Cart extends Model
{
    protected $fillable = ['cartable_id', 'cartable_type', 'qty', 'user_id'];

    public function newCollection(array $models = [])
    {
        return new Items($models);
    }

    public function cartable(){
        return $this->morphTo();
    }

    public function getPriceAttribute(){
        return $this->cartable->price;
    }

    public function getSubtotalAttribute(){
        return $this->qty * $this->price;
    }

    public function getCartKey(){
        return sha1($this->cartable_type.'_'.$this->cartable_id, true);
    }
}