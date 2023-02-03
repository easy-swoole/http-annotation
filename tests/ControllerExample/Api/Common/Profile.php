<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;

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

    }

    #[Api(
        apiName: "update"
    )]
    function update()
    {

    }
}