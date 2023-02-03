<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;

#[Param(
    name: "signature"
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