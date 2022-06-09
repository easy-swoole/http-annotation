<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLen;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;

class Index extends Base
{
    #[Api(
        apiName: "index",
        requestPath: "/test/index.html",
        params: [
            new Param(name:"account",from: [Param::GET],validate: [
                new Required(),
                new MaxLen(maxLen: 15),
            ],description: new Description("这个参数一定要有啊"))
        ],
        exampleParams: [
            new Param(name:"test",value:3 ),
            new Param(name:"testB",value:222)
        ],
        exampleSuccess: [

        ],
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function index(string $account){
        $this->writeJson(200,null,"account is {$account}");
    }
}


class C {

}

