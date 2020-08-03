<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;

/**
 * Class ControllerA
 * @package EasySwoole\HttpAnnotation\Tests\TestController
 * @ApiGroup(groupName="A")
 * @ApiGroupDescription()
 */
class ControllerA extends AnnotationController
{
    /**
     * @Api(path="/A/test",name="a")
     */
    function test()
    {

    }

    /**
     * @Api(path="/",name="22")
     */
    function test2()
    {

    }
}