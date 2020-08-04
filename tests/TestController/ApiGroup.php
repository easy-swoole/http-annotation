<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup as ApiGroupTag;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;

/**
 * Class ControllerA
 * @package EasySwoole\HttpAnnotation\Tests\TestController
 * @ApiGroupTag(groupName="GroupA")
 * @ApiGroupDescription("GroupA desc")
 * @ApiGroupAuth(name="groupParamA")
 * @ApiGroupAuth(name="groupParamB")
 */
class ApiGroup extends AnnotationController
{
    /**
     * @Api(path="/apiGroup/func",name="func")
     * @ApiAuth(name="apiAuth1")
     * @ApiAuth(name="apiAuth2")
     */
    function func()
    {

    }
}