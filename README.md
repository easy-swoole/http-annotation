# HttpAnnotation

## 安装
```bash
composer require easyswoole/http-annotation
```
## 注解规范

### Example
```php
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Http\Dispatcher;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Utility;
use Swoole\Http\Server;

$nameSpace = 'EasySwoole\HttpAnnotation\Tests\ControllerExample';
$dispatcher = new Dispatcher();
$dispatcher->setNamespacePrefix($nameSpace);
$dispatcher->enableFakeRouter();
$dispatcher->setOnRouterCreate(function (AbstractRouter $router)use($nameSpace){
    Utility::mappingRouter($router->getRouteCollector(),"tests/ControllerExample",$nameSpace);
});
$http = new Server("127.0.0.1", 9501);
$http->set([
    "worker_num"=>1
]);

$http->on("request", function ($request, $response) use($dispatcher){
    $request_psr = new Request($request);
    $response_psr = new Response($response);
    $dispatcher->dispatch($request_psr, $response_psr);
    $response_psr->__response();
});

$http->start();
```