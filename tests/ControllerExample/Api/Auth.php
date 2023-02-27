<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Required;

#[ApiGroup(
    groupName: "Api.Auth", description: "Api.Auth 的文本描述 "
)]
class Auth extends ApiBase
{
    #[Api(
        apiName: "login",
        allowMethod: HttpMethod::GET,
        requestPath: "/auth/login.html",
        requestParam: [
            new Param(name: "account", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ], description: new Description("用户登录的账户Id")),
            new Param(name: "password", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ], description: new Description("密码")),
            new Param(name: "verify", from: ParamFrom::JSON,
                description: new Description("验证码"),
                type: ParamType::OBJECT,
                subObject: [
                    new Param(name: "code", from: ParamFrom::JSON, validate:[
                        new Required(),
                        new MaxLength(maxLen: 15),
                    ],description: "防伪编号"),
                    new Param(name: "phone", from: ParamFrom::JSON, description: "手机号")
                ])
        ],
        responseParam: [
            new Param(
                name: "code",type: ParamType::STRING
            ),
            new Param(
                name: "Result",
                type: ParamType::LIST,
                subObject: [
                    new Param("token"),
                    new Param("expire")
                ]
            ),
            new Param("msg")
        ],
        requestExamples: [
            new Example(
                [
                    new Param(name: "account", value: "1111", description: "账号"),
                    new Param(name: "password", value: "1111", description: "密码"),
                    new Param(name: "verify", value: "1111", description: new Description('验证码')),
                ]
            ),
            new Example(
                new Description('tests/res/json.json', Description::JSON)
            ),
            new Example(
                 new Description('tests/res/xml.xml', Description::XML)
            ),
        ],
        responseExamples: [
            new Example(
                [
                    new Param(name: "result", description: "结果", subObject: [
                        new Param(name: "id", value: 1, description: "用户Id"),
                        new Param(name: "name", value: "八九", description: "昵称")
                    ]),
                    new Param(name: "code", value: "200", description: "状态码"),
                ]
            ),
            new Example(
                [
                    new Param(name: "result", value: "fail", description: "结果"),
                    new Param(name: "code", value: "500", description: "状态码"),
                ]
            ),
            new Example(
                 new Description('tests/res/json.json', Description::JSON)
            ),
            new Example(
               new Description('tests/res/xml.xml', Description::XML)
            ),
        ],
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function login()
    {

    }
}