<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Required;

#[ApiGroup(
    groupName: "Api.Auth"
)]
class Auth extends ApiBase
{
    #[Api(
        allowMethod: HttpMethod::GET,
        requestPath: "/auth/login.html",
        requestParam: [
            new Param(name: "account", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ],),
            new Param(name: "password", from: ParamFrom::GET, validate: [
                new Required(),
                new MaxLength(maxLen: 15),
            ], ),
            new Param(name: "verify", from: ParamFrom::JSON,
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
        ]
    )]
    function login()
    {

    }
}