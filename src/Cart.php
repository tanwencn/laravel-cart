<?php
/**
 * Created by PhpStorm.
 * User: tanwen-d
 * Date: 2017/9/8
 * Time: 15:35
 */
namespace Tanwen\Cart;

class Cart
{
    private static $_drive = [];
    private static $_scene = [];

    public static function drive($key, $concrete)
    {
        if ($concrete instanceof \Closure) {
            self::$_drive[$key] = $concrete;
        } else {
            self::$_scene[$key] = $concrete;
        }
    }

    public static function scene($key, $parameters=[])
    {
        if (!isset(self::$_scene[$key])) {
            self::$_scene[$key] = call_user_func_array(self::$_drive[$key], $parameters);
        }

        return self::$_scene[$key];
    }
}