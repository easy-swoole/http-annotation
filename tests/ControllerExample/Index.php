<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
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

class Index extends Base
{
    #[Api(
        apiName: "home",
        allowMethod:HttpMethod::GET,
        requestPath: "/test/index.html",
        requestParam: [
            new Param(
                name: 'account',
                validate: [
                    new Required()
                ]
            )
            ,
            new Param(
                name: "page",
                from: ParamFrom::GET,
                validate: [
                    new Optional()
                ],
                value: 1,
                description: new Description("翻页参数")
            )
        ],
        description: new Description(__DIR__.'/../res/description.md',Description::MARKDOWN_FILE)
    )]
    function index(string $account){
        $this->writeJson(200,null,"account is {$account}");
    }

    #[Api(
        apiName: "hello",
        allowMethod:[HttpMethod::POST,HttpMethod::GET],
        requestPath: "/test/hello.html",
        requestParam: [
            new Param(name:"account",from:ParamFrom::GET,validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ],description: new Description("用户登录的账户Id,这个参数一定要有啊"))
        ],
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function hello(string $account){
        $this->writeJson(200,null,"account is {$account}");
    }

    function doc()
    {
        $path = __DIR__;
        $namespace = 'EasySwoole\HttpAnnotation\Tests\ControllerExample';
        $doc = new Document($path,$namespace);
        $this->response()->write($doc->scanToHtml());
    }

    #[Api(
        apiName: 'url',
        requestParam: [
            new Param(
                name: "url",
                validate: [
                    new IsUrl()
                ]
            )
        ]
    )]
    function url()
    {

    }

    #[Api(
        apiName: 'optionalSet',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new OptionalIfParamSet("b"),
                    new MinLength("5")
                ]
            ),
            new Param(
                name: "b",
                validate: [
                    new OptionalIfParamSet("a"),
                    new Integer(),
                    new Min(1)
                ]
            )
        ]
    )]
    function optionalSet()
    {

    }

    #[Api(
        apiName: 'optionalMiss',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new Optional(),
                    new MinLength("5")
                ]
            ),
            new Param(
                name: "b",
                validate: [
                    new OptionalIfParamMiss("a"),
                    new Integer(),
                    new Min(1)
                ]
            )
        ]
    )]
    function optionalMiss()
    {

    }

    #[Api(
        apiName: 'testArray',
        requestParam: [
            new Param(
                name: 'a',
                validate: [
                    new Required(),
                    new NotEmpty()
                ]
            ),
            new Param(
                name: 'b',
                validate: [
                    new Optional(),
                ]
            ),
            new Param(
                name: 'c',
                validate: [
                    new Optional(),
                ],
                ignorePassArgWhenNull: true
            ),
        ]
    )]
    function testArray(array $data)
    {
        $this->writeJson(Status::CODE_OK,$data);
    }

    #[Api(
        apiName: 'compare',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new Integer(),
                    new BigThanColumn('b')
                ]
            ),
            new Param(
                name: "b",
                validate: [
                    new Integer()
                ]
            ),
            new Param(
                name: 'c',
                validate: [
                    new Integer(),
                    new SmallThanColumn('b')
                ]
            )
        ]
    )]
    function compare(array $data)
    {
        $this->writeJson(Status::CODE_OK,$data);
    }


    #[Api(
        apiName: 'ignoreValidatorWhenEmpty',
        requestParam: [
            new Param(
                name: "a",
                validate: [
                    new Integer(),
                    new IgnoreValidatorWhenEmpty()
                ],
                ignorePassArgWhenNull: true
            ),
        ]
    )]
    function ignoreValidatorWhenEmpty(array $data)
    {
        $this->writeJson(Status::CODE_OK,$data);
    }
}