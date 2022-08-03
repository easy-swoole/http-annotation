<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api;

use EasySwoole\HttpAnnotation\Attributes\ApiGroup;

#[ApiGroup(
    name: "Api.Auth"
)]
class Auth extends ApiBase
{
    function login()
    {

    }
}