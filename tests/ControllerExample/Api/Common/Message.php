<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Example;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Optional;

class Message extends Base
{

    #[Api(
        apiName: "list"
    )]
    function list(){
        $this->writeJson(Status::CODE_OK,[1,2,3]);
    }

    #[Api(
        apiName: "unRead"
    )]
    function unRead(){

    }

    function detail(){

    }
}