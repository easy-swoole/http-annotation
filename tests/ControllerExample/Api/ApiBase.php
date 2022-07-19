<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use EasySwoole\HttpAnnotation\Tests\ControllerExample\Base;

class ApiBase extends Base
{
    #[Param( name: "token",validate: [
        new Required()
    ])]
    function onRequest(?string $action): ?bool
    {
        return parent::onRequest($action);
    }
}