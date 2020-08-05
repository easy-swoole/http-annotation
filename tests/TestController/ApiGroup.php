<?php


namespace EasySwoole\HttpAnnotation\Tests\TestController;


use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFailParam;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup as ApiGroupTag;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiRequestExample;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccess;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiSuccessParam;
use EasySwoole\HttpAnnotation\AnnotationTag\CircuitBreaker;
use EasySwoole\HttpAnnotation\AnnotationTag\Context;
use EasySwoole\HttpAnnotation\AnnotationTag\Di;
use EasySwoole\HttpAnnotation\AnnotationTag\InjectParamsContext;
use EasySwoole\HttpAnnotation\AnnotationTag\Method;
use EasySwoole\HttpAnnotation\AnnotationTag\Param;

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
     * @Di(key="di")
     */
    protected $di;
    /**
     * @Context(key="context")
     */
    protected $context;

    /**
     * @Api(path="/apiGroup/func",name="func")
     * @ApiAuth(name="apiAuth1")
     * @ApiAuth(name="apiAuth2")
     * @ApiDescription("func desc")
     * @ApiFail("func fail example1")
     * @ApiFail("func fail example2")
     * @ApiFailParam(name="failParam1")
     * @ApiFailParam(name="failParam2")
     * @ApiRequestExample("func request example1")
     * @ApiRequestExample("func request example2")
     * @ApiSuccess("func success example1")
     * @ApiSuccess("func success example2")
     * @ApiSuccessParam(name="successParam1")
     * @ApiSuccessParam(name="successParam2")
     * @CircuitBreaker(timeout=5.0)
     * @InjectParamsContext(key="requestData")
     * @Method(allow={POST,GET})
     * @Param(name="param1")
     * @Param(name="param2")
     */
    function func()
    {

    }
}