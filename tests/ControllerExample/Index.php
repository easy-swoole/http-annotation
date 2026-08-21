<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Di;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Validator\BigThanColumn;
use EasySwoole\HttpAnnotation\Validator\IgnoreValidatorWhenEmpty;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\IsUrl;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Min;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamMiss;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\SmallThanColumn;

#[ApiGroup(
    groupName: 'index'
)]
#[PreCall([Utility::class,'preCall'])]
class Index extends Base
{

    #[Di()]
    protected $test;

    #[Api(
        apiName: "home",
        allowMethod:HttpMethod::GET,
        requestPath: "/test/index.html",
        requestParam: [
            new Param(
                name:'userInfo',
                from: ParamFrom::JSON,
                subObject: [
                    new Param(
                        name: 'name',
                        validate: [
                            new NotEmpty()
                        ]
                    ),
                    new Param(
                        name: 'age',
                        validate: [
                            new Optional()
                        ]
                    )
                ]
            ),
        ],
        description: new Description(__DIR__.'/../res/description.md',Description::MARKDOWN_FILE)
    )]
    function index(array $userInfo){
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