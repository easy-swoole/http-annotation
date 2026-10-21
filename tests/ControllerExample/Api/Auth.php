<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\Example\Array2Json;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Bean\Example\ResParamType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Validator\Required;


#[ApiGroup(
    groupName: 'Admin.Auth',
    description:'Admin Auth desc',
)]
class Auth extends ApiBase
{
    #[Api(
        allowMethod: HttpMethod::GET,
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
            new ResParam('code', 200, ResParamType::int, '响应码，200 表示成功'),
            new ResParam('result', null, ResParamType::object, '返回数据'),
            new ResParam('result.access_token', 'token-example', ResParamType::string, '授权 Token'),
            new ResParam('result.user_info', null, ResParamType::object, '用户信息'),
            new ResParam('result.user_info.id', 19, ResParamType::int, '用户 ID'),
            new ResParam('msg', 'Success', ResParamType::string, '响应消息'),
        ],
        responseExamples: [
            new Array2Json([
                'code' => 200,
                'result' => [
                    'access_token' => 'token-example',
                    'user_info' => [
                        'id' => 19,
                    ],
                ],
                'msg' => 'Success',
            ]),
            new Array2Json([
                'code' => 401,
                'result' => null,
                'msg' => '账号或密码错误',
            ], isSuccessResponse: false),
        ]
    )]
    function login()
    {

    }

    #[Api(
        requestParam: [
            new Param(
                name: "userId",
                validate: []
            ),
            new Param(
                name: "ticket",
                from: ParamFrom::COOKIE,
                validate: []
            )
        ]
    )]
    function logout()
    {

    }
}
