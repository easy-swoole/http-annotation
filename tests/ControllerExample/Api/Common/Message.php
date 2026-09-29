<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;

class Message extends Base
{

    #[Api]
    function list(){
        $this->writeJson(Status::CODE_OK,[1,2,3]);
    }

    #[Api(
    )]
    function unRead(){

    }

    function detail(){

    }
}