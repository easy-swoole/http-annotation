<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Context;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Bean\Description\Markdown;
use EasySwoole\HttpAnnotation\Bean\Example\Array2Json;
use EasySwoole\HttpAnnotation\Bean\Example\ArrayForm;
use EasySwoole\HttpAnnotation\Bean\Example\Raw;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;

#[ApiGroup(
    groupName: 'index',
    description: new Markdown(__DIR__.'/../res/descriptionForGroup.md'),
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
                from: ParamFrom::GET,
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
                name: 'age',
                from: ParamFrom::GET,
                value: 12,
                description: '该用户的年龄'
            ),
            new Param(
                name: 'userName',
                from: ParamFrom::GET,
                validate: [
                    new NotEmpty()
                ]
            )
        ],
        requestExamples: [
            new ArrayForm([
                'age'=>12,
                'userName'=>'easyswoole'
            ]),
            new ArrayForm([
                'age'=>19,
                'userName'=>'hello'
            ])
        ],
        responseExamples: [
            new Raw('这是一个raw相应'),
            new Array2Json([
                'result'=>'xxxx'
            ]),
            new Array2Json([
                'result'=>'xxxx'
            ],isSuccessResponse: false)
        ],
        description: new Markdown(__DIR__.'/../res/descriptionForApi.md'),
    )]
    #[PreCall([Utility::class,'preCall'])]
    function test(int|null $age,string|null $userName)
    {
        var_dump($userName,$age);
    }

    #[Api(
        allowMethod: HttpMethod::POST,
        requestParam: [
            new Param(
                name:'age',

            ),
            new Param(
                name: 'userName',
                validate: [
                    new NotEmpty()
                ]
            ),
            new Param(
                name: 'idCode',
                from: ParamFrom::POST,
                deprecated: true
            )
        ],
        deprecated: true,
    )]
    #[PreCall([Utility::class,'preCall'])]
    function test2(array $data)
    {
        var_dump($data);
    }
}