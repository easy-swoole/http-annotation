<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;

#[ApiGroup(
    groupName: 'index'
)]
#[PreCall([Utility::class,'preCallGlobal'])]
class Index extends Base
{

    #[Di(key: 'di')]
    protected $test;

    #[Api(
        apiName: "home",
        allowMethod:HttpMethod::GET,
        requestPath: "/test/index.html",
        requestParam: [
            new Param(
                name:'account',
            ),
        ]
    )]
    #[PreCall([Utility::class,'preCall'])]
    function index(string $account)
    {
        $account = 1;
        $this->writeJson(200,null,"account is {$account}");
    }


    #[Api(
        apiName: 'test',
        requestParam: [
            new Param(
                name:'age',
            ),
            new Param(
                name: 'userName'
            )
        ]
    )]
    #[PreCall([Utility::class,'preCall'])]
    function test(int|null $age,string|null $userName)
    {
        var_dump($age,$userName);
    }
}