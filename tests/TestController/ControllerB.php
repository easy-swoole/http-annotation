<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;

/**
 * Class ControllerB
 * @package EasySwoole\HttpAnnotation\Tests\TestController
 * @ApiGroup(groupName="B")
 * @ApiGroupAuth(name="groupBAuth")
 */
class ControllerB extends AnnotationController
{

}