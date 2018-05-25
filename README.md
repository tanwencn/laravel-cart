# laravel-cart
laravel的购物车插件。支持作用域、持久化、关联产品模型。 

1.安装：

    composer require tanwencn/laravel-cart
 
2.laravel < 5.5的需要修改配置文件comfig/app.php：

     providers 添加："Tanwencn\Cart\ServiceProvider::class"
    
     aliases  添加："Cart": "Tanwencn\Cart\Facades\Cart::class"
    
3.使用方法：
    
    use Tanwencn\Cart\Facades\Cart;  //加载facades
    
    $product = Product::find(1); //Product模型需要保证$product->price可执行
    
    添加购物车 Cart::put($product, 2);
    
    修改购物车：Cart::update($item_key, 3);
    
    删除购物车商品： Cart::forget($item_key);
    
    清空购物车：Cart::flush();
        
    购物车查询
  
    $items = Cart::all(); 获取购物车商品
    
    foreach($items as $item){
        $item->getItemKey(); //购物车商品唯一标识
        $item->qty //商品数量
        $item->price //商品数量
        $item->cartable //添加时传入的Product模型
        $item->subtotal //用Product->price生成的小计
    }
    
    $items->subtotal(); //商品总价

4.持久化数据

    默认情况下除了order作用域，其它作用域默认为在登陆的情况下保存数据到数据库，并在下次登陆时合并当前购物车。
    若想取消作用域的持久化，可在config/cart.php配置:
    'order' => [
        'persistent' => false
    ]
    
    
4.作用域
    
    默认域：default
    Cart::add($product);
    Cart::all();
    
    商品收藏
    Cart::scope('wishlist')->add($product);
    Cart::scope('wishlist')->all();
    
    购买清单
    Cart::scope('order')->add($product);
    Cart::scope('order')->all();
    