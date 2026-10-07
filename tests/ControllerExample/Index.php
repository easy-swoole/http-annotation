<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Bean\Example\ArrayForm;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;

#[ApiGroup(
    groupName: 'index',
    description: 'index description',
)]
#[PreCall([Utility::class,'preCallGlobal'])]
class Index extends Base
{
    #[Di(key: 'di')]
    protected $test;

    #[Api(
        allowMethod:HttpMethod::GET,
        requestParam: [
            new Param(
                name:'account',
                validate: [
                    new MinLength(3)
                ]
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
        requestParam: [
            new Param(
                name:'age',
            ),
            new Param(
                name: 'userName',
                validate: [
                    new NotEmpty()
                ]
            )
        ],
        requestExamples: [
            new ArrayForm([
                'age'=>12,
                'userName'=>'easyswoole'
            ])
        ]
    )]
    #[PreCall([Utility::class,'preCall'])]
    function test(int|null $age,string|null $userName)
    {
        var_dump($userName,$age);
    }

    #[Api(
        requestParam: [
            new Param(
                name:'age',

            ),
            new Param(
                name: 'userName',
                validate: [
                    new NotEmpty()
                ]
            )
        ]
    )]
    #[PreCall([Utility::class,'preCall'])]
    function test2(array $data)
    {
        var_dump($data);
    }
}