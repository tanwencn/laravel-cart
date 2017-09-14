<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:36
 */

namespace Tanwen\Cart;


interface IDrive
{
    function _all();

    function _save($data);

    public function _flush();
}