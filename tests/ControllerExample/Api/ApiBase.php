<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Tests\ControllerExample\Base;

#[ApiGroup(
    groupName: "Api",
)]
class ApiBase extends Base
{

}