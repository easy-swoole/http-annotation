<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Description;

#[ApiGroup(
    name: "Api.Auth",description: new Description("this is plain text")
)]
class Auth extends ApiBase
{
    function login()
    {

    }
}