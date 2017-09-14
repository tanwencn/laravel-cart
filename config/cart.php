<?php

return [

    //登陆后的存放表
    'table' => 'goods_cart',

    'item' => [

        'model' => \App\Model\Goods::class, //商品Eloquent,false为关闭

        'fieldPrice' => 'price', //商品价格字段

        'fieldStock' => 'stock', //商品库存字段
    ]

];
