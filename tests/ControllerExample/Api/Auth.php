<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;

#[ApiGroup(
    groupName: "Api.Auth", description: new Description("EasySwoole是一款常驻内存型的分布式swoole框架，专为API而生，支持同时混合监听HTTP、WebSocket、自定义TCP、UDP协议，且拥有丰富的组件，例如协程 连接池、TP风格的协程ORM、协程微信SDK、协程支付宝SDK、协程Kafka客户端、协程ElasticSearch客户端、协程Consul客户端、协程Redis客户端、协程Apollo客户端、协程NSQ客户端、协程自定义队列、 协程Memcached客户端、协程视图引擎、JWT、协程RPC、协程SMTP客户端、协程HTTP客户端、协程Actor、Crontab定时器等诸多组件。让开发者以最低的学习成本和精力编写出多进程，可异步，高可用的应用服务。 ")
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
            new Param("code"),
            new Param(
                name: "Result",
                subObject: [
                    new Param("token"),
                    new Param("expire")
                ]
            ),
            new Param("msg")
        ],
        requestExample: [
            new Example(
                [
                    new Param(name: "account", value: "1111", description: "账号"),
                    new Param(name: "password", value: "1111", description: "密码"),
                    new Param(name: "verify", value: "1111", description: new Description('验证码')),
                ],
                new Description('正常请求', Description::PLAIN_TEXT)
            ),
            new Example(
                description: new Description('tests/res/json.json', Description::JSON)
            ),
            new Example(
                description: new Description('tests/res/xml.xml', Description::XML)
            ),
        ],
        responseExample: [
            new Example(
                [
                    new Param(name: "result", description: "结果", subObject: [
                        new Param(name: "id", value: 1, description: "用户Id"),
                        new Param(name: "name", value: "八九", description: "昵称")
                    ]),
                    new Param(name: "code", value: "200", description: "状态码"),
                ],
                new Description('成功返回值', Description::PLAIN_TEXT)
            ),
            new Example(
                [
                    new Param(name: "result", value: "fail", description: "结果"),
                    new Param(name: "code", value: "500", description: "状态码"),
                ],
                new Description('失败返回值', Description::PLAIN_TEXT)
            ),
            new Example(
                description: new Description('tests/res/json.json', Description::JSON)
            ),
            new Example(
                description: new Description('tests/res/xml.xml', Description::XML)
            ),
        ],
        description: new Description("这是一个接口说明啊啊啊啊")
    )]
    function login()
    {

    }
}