<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLength;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;

#[ApiGroup(
    groupName: "Api.Auth",description: new Description("EasySwoole是一款常驻内存型的分布式swoole框架，专为API而生，支持同时混合监听HTTP、WebSocket、自定义TCP、UDP协议，且拥有丰富的组件，例如协程 连接池、TP风格的协程ORM、协程微信SDK、协程支付宝SDK、协程Kafka客户端、协程ElasticSearch客户端、协程Consul客户端、协程Redis客户端、协程Apollo客户端、协程NSQ客户端、协程自定义队列、 协程Memcached客户端、协程视图引擎、JWT、协程RPC、协程SMTP客户端、协程HTTP客户端、协程Actor、Crontab定时器等诸多组件。让开发者以最低的学习成本和精力编写出多进程，可异步，高可用的应用服务。 ")
)]
class Auth extends ApiBase
{

    function login()
    {

    }
}