<?php

/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */
namespace Tanwen\Cart\Drives;

use Tanwen\Cart\IDrive;

class Session extends Base implements IDrive
{
    public function _all(){
        return session('cart');
    }

    public function _save($data){
        session(['cart' => $data]);
    }

    public function _flush()
    {
        session()->forget('key');
    }
}