<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use EasySwoole\HttpAnnotation\Tests\ControllerExample\Base;

#[ApiGroup(
    groupName: "Api",
    description: new Description(
        desc: "tests/res/description.md",
        type: Description::MARKDOWN
    ),
)]
class ApiBase extends Base
{

}