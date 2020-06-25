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
     * @Api(path="/A/test")
     */
    function test()
    {

    }

    /**
     * @Api(path="")
     */
    function test2()
    {

    }
}