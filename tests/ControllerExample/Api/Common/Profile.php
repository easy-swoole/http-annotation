<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Required;

#[ApiGroup(groupName: 'Common.Profile')]
class Profile extends Base
{
    #[Api(

    )]
    function info()
    {
        $this->writeJson(Status::CODE_OK,null,"info");
    }

    #[Api(

    )]
    function update()
    {
        $this->writeJson(Status::CODE_OK,null,"update");
    }
}