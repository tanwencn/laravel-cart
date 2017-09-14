# laravel-cart
laravel的购物车插件。

1.安装：

    composer require tanwen/cart
 
2.laravel < 5.5的需要修改配置文件comfig/app.php：

     providers 添加："Tanwen\Cart\ServiceProvider::class"
    
     aliases  添加："Cart": "Tanwen\Cart\Facades\Cart::class"
     
3.使用方法：

    修改配置文件文件vendor/tanwen/cart/config/cart.php
    
    table：登陆后购物车永久储存表
    
    item.model 商品数据的Eloquent模型 false为关闭，购物车只做简单数量储存。开启后可判断库存上下架价格小计 及输出model等功能
    
    item.fieldPrice 商品价格在model的输出属性字段，用来计算价格
    
    item.fieldStock 商品库存在model的输出属性字段，用来 判断库存
    
添加商品：
    
    use Tanwen\Cart\Facades\Cart;  //加载facades
    
    Cart::add(商品ID, (+-)数量, 店铺ID（可选 ）);
    
    $items = Cart::items(); 获取购物车商品
    
    $items = Cart::items(function($item){
        $item 等同下面的$item
    }); //获取购物车商品，过滤function返回不为true的数据
    
    foreach($items as $shop_id => $data){
        foreach($data as $goods_id => $item){
            $item->quantity //商品数量
            $item->getPrice() //商品价格
            $item->getTotal() //商品小计
            $item->model(); //获取配置的商品Eloquent
            $item->hasValidity(); //判断商品是否有效
            $item->getStatus(); //string: 正常、库存不足、 商品无效或已下架(model查询不到)
        }
    }
    
    