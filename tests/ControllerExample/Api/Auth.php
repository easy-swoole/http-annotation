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
            ], )
        ],
        responseParam: [
            new Param(
                name: "code",type: ParamType::STRING
            ),
            new Param(
                name: "Result",
                type: ParamType::STRING
            ),
            new Param("msg")
        ]
    )]
    function login()
    {

    }
}