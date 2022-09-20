<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\ApiBase;

class Base extends ApiBase
{
    function onRequest(?string $action): ?bool
    {
        return parent::onRequest($action);
    }
}