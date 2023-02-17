<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Required;

#[Param(
    name: "signature",
    validate: [
        new Required()
    ]
    ,ignoreAction: [
        "info"
    ]
)]
class Profile extends Base
{
    #[Api(
        apiName: "info"
    )]
    function info()
    {
        $this->writeJson(Status::CODE_OK,null,"info");
    }

    #[Api(
        apiName: "update"
    )]
    function update()
    {
        $this->writeJson(Status::CODE_OK,null,"update");
    }
}