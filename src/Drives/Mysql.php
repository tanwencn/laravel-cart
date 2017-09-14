<?php

/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */

namespace Tanwen\Cart\Drives;

use Tanwen\Cart\IDrive;
use Illuminate\Support\Facades\DB;

class Mysql extends Base implements IDrive
{
    private $_user_id;
    private $_table;

    public function __construct($_user_id)
    {
        $this->_user_id = $_user_id;
        $this->_table = config('cart.table', 'goods_cart');
    }

    public function _all()
    {
        $data = $this->getQuery()->select('data')->value('data');

        return $data?json_decode($data, true):[];
    }

    public function _save($data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE ^ JSON_UNESCAPED_SLASHES);
        DB::update("REPLACE INTO {$this->_table}(`user_id`, `data`) values('{$this->_user_id}', '{$data}')");
    }

    public function _flush()
    {
        $this->getQuery()->delete();
    }

    private function getQuery()
    {
        return DB::table($this->_table)->where('user_id', $this->_user_id);
    }
}