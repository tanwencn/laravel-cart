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
    protected $fillable = ['targetable_id', 'targetable_type', 'qty', ];

    public function newCollection(array $models = [])
    {
        return new Items($models);
    }
}