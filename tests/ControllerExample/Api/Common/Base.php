<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\ApiBase;
use EasySwoole\HttpAnnotation\Validator\Required;

class Base extends ApiBase
{
    #[Param(
        name: "token",
        validate: [
            new Required()
        ], ignoreAction: [
            "list"
        ]
    )]
    function onRequest(?string $action): ?bool
    {
        return parent::onRequest($action);
    }
}