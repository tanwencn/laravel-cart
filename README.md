# laravel-cart
laravel的购物车插件。支持作用域、持久化。 

1.安装：

    composer require tanwencn/laravel-cart
 
2.laravel < 5.5的需要修改配置文件comfig/app.php：

     providers 添加："Tanwencn\Cart\ServiceProvider::class"
    
     aliases  添加："Cart": "Tanwencn\Cart\Facades\Cart::class"
    
3.使用方法：
    
    use Tanwencn\Cart\Facades\Cart;  //加载facades
    
    添加购物车：
    
    $product = Product::find(1); //Product模型需要保证$product->price可执行
    
    添加购物车 Cart::put($product, 2);
    
    修改购物车：Cart::put($product, 3, true);
    
    删除购物车商品： Cart::forget($product->id);
    
    清空购物车：Cart::flush();
    
    Cart::save();//持久化，需要每次操作购物车完毕后执行一次，若用户登陆时会同步保存到数据库。不调用该方法时，购物车操作只在session有效时有效。
        
    购物车查询
  
    $items = Cart::get(); 获取购物车商品
    
    foreach($items as $item){
        $item->qty //商品数量
        $item->model //添加时传入的Product模型
        $item->subtotal //用Product->price生成的小计
    }
    
    $items->subtotal(); //商品总价
    
4.作用域

    商品收藏
    Cart::scope('wishlist');
    Cart::add($product);
    Cart::save(); //放入数据库做持久化
    
    购买清单
    Cart::scope('order');
    Cart::add($product);
    