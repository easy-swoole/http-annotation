<?php

//use EasySwoole\HttpAnnotation\Attributes\Api;
//use EasySwoole\HttpAnnotation\Attributes\Description;
//use EasySwoole\HttpAnnotation\Attributes\Param;
//use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLength;
//use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
//
//#[Attribute]
//class Route
//{
//    public function __construct(
//        public string $path = '/',
//        public array $methods = []
//    ) {}
//}
//
//#[Attribute]
//class RouteGroup
//{
//    public function __construct(
//        public string $basePath = "/"
//    ) {}
//}
//
//#[RouteGroup("/index")]
//class IndexController
//{
//    #[Api(
//        apiName: "index",
//        requestPath: "/test/index.html",
//        params: [
//            new Param(name:"account",from: [Param::GET],validate: [
//                new Required(),
//                new MaxLength(maxLen: 15),
//            ],description: new Description("这个参数一定要有啊"))
//        ],
//        exampleParams: [
//            new Param(name:"test",value:3 ),
//            new Param(name:"testB",value:222)
//        ],
//        exampleSuccess: [
//
//        ],
//        description: new Description("这是一个接口说明啊啊啊啊")
//    )]
//    public function index(): void
//    {
//        echo "hello!world" . PHP_EOL;
//    }
//
//    #[Route("/test", methods: ["post"])]
//    public function test(): void
//    {
//        echo "test" . PHP_EOL;
//    }
//}
//
//class Kernel
//{
//    protected array $routeGroup = [];
//
//    public function handle($argv): void
//    {
//        $this->parseRoute();
//        [,$controller, $method] = explode('/', $argv[1]);
//        [$controller, $method] = $this->routeGroup['/' . $controller]['get']['/'. $method];
//        call_user_func_array([new $controller, $method], []);
//    }
//
//    public function parseRoute(): void
//    {
//        $controller = new ReflectionClass(IndexController::class);
//        $controllerAttributes = $controller->getAttributes(RouteGroup::class);
//
//        foreach ($controllerAttributes as $controllerAttribute) {
//            [$groupName] = $controllerAttribute->getArguments();
//            $methods = $controller->getMethods(ReflectionMethod::IS_PUBLIC);
//
//            foreach ($methods as $method) {
//                $methodAttributes = $method->getAttributes(Route::class);
//
//                foreach ($methodAttributes as $methodAttribute) {
//                    [0 => $path, 'methods' => $routeMethods] = $methodAttribute->getArguments();
//
//                    foreach ($routeMethods as $routeMethod) {
//                        $this->routeGroup[$groupName][$routeMethod][$path] = [IndexController::class, $method->getName()];
//                    }
//                }
//            }
//        }
//    }
//}
//
//$kernel = new Kernel;
//$kernel->handle($argv);
var_dump([
    "num"=>23.0
]);