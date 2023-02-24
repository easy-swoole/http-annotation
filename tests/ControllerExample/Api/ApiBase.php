<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Tests\ControllerExample\Base;

#[ApiGroup(
    groupName: "Api",
    description: new Description(
        desc: "tests/res/description.md"
    ),
)]
class ApiBase extends Base
{

}