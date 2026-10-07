<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;

#[ApiGroup(groupName: 'Common.Message')]
class Message extends Base
{

    #[Api]
    function list(){
        $this->writeJson(Status::CODE_OK,[1,2,3]);
    }

    #[Api(
        requestParam: [
            new Param(
                name: 'testHeader',
                from: ParamFrom::HEADER
            )
        ]
    )]
    function unRead()
    {

    }

    #[Api(
        allowMethod: HttpMethod::POST,
        requestParam: [
            new Param(
                name: 'msgId',
                from: ParamFrom::JSON
            )
        ],
        acceptContentType: ContentType::JSON
    )]
    function detail()
    {

    }
}