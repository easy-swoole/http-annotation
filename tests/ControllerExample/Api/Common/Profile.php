<?php

namespace EasySwoole\HttpAnnotation\Tests\ControllerExample\Api\Common;

use EasySwoole\Http\Message\Status;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\IsFile;
use EasySwoole\HttpAnnotation\Validator\Required;

#[ApiGroup(groupName: 'Common.Profile')]
class Profile extends Base
{
    #[Api(

    )]
    function info()
    {
        $this->writeJson(Status::CODE_OK,null,"info");
    }

    #[Api(
        allowMethod: HttpMethod::POST,
        requestParam: [
            new Param(
                name: 'userThumb',
                from: ParamFrom::FILE,
                validate: [
                    new IsFile()
                ],
                type: ParamType::FILE
            ),
        ]
    )]
    function update()
    {
        $this->writeJson(Status::CODE_OK,null,"update");
    }
}