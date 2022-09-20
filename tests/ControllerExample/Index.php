<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\RequestParam;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Attributes\Validator\Optional;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;

class Index extends Base
{
    #[Api(
        apiName: "home",
        allowMethod:HttpMethod::GET,
        requestPath: "/test/index.html",
        requestParam: new RequestParam(
            params: [
                new Param(
                    name: "page",
                    from: ParamFrom::GET,
                    validate: [
                        new Optional()
                    ],
                    value: 1,
                    description: new Description("翻页参数")
                ),
                new Param(
                    name: "account",
                    from: ParamFrom::GET,
                    validate: [
                        new Optional()
                    ],
                    value: 1,
                    description: new Description("翻页参数")
                )
            ]
        ),
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function index(string $account){
        $this->writeJson(200,null,"account is {$account}");
    }

    #[Api(
        apiName: "hello",
        allowMethod:[HttpMethod::POST,HttpMethod::GET],
        requestPath: "/test/hello.html",
        requestParam: new RequestParam(
            params: [
                new Param(name:"account",from:ParamFrom::GET,validate: [
                    new Required(),
                    new MaxLength(maxLen: 15),
                ],description: new Description("用户登录的账户Id,这个参数一定要有啊"))
            ]
        ),
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function hello(string $account){
        $this->writeJson(200,null,"account is {$account}");
    }
}