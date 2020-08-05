<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\AnnotationTag\Api;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiAuth;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiDescription;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFail;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiFailParam;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroupDescription;
use EasySwoole\HttpAnnotation\Tests\TestController\ApiGroup;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup as ApiGroupTag;

class AnnotationParserTest extends TestCase
{
    /**
     * @var ObjectAnnotation
     */
    protected $apiGroup;
    /** @var ObjectAnnotation */
    protected $resultB;

    function run(TestResult $result = null): TestResult
    {
        $parse = new Parser();
        $this->apiGroup = $parse->parseObject(new \ReflectionClass(ApiGroup::class));
        return parent::run($result);
    }

    function testApiGroup()
    {
        $this->assertInstanceOf(ApiGroupTag::class,$this->apiGroup->getApiGroupTag());
        $this->assertEquals('GroupA',$this->apiGroup->getApiGroupTag()->groupName);
    }

    function testApiGroupDescription()
    {
        $this->assertInstanceOf(ApiGroupDescription::class,$this->apiGroup->getApiGroupDescriptionTag());
        $this->assertEquals('GroupA desc',$this->apiGroup->getApiGroupDescriptionTag()->value);
    }

    function testApiGroupAuth()
    {
        $this->assertIsArray($this->apiGroup->getGroupAuthTag());
        $this->assertEquals(2,count($this->apiGroup->getGroupAuthTag()));
        $this->assertEquals('groupParamA',$this->apiGroup->getGroupAuthTag('groupParamA')->name);
        $this->assertEquals('groupParamB',$this->apiGroup->getGroupAuthTag('groupParamB')->name);
    }

    function testMethod()
    {
        $this->assertEquals(null,$this->apiGroup->getMethod('noneFunc'));
        $this->assertInstanceOf(MethodAnnotation::class,$this->apiGroup->getMethod('func'));
        $this->assertEquals('func',$this->apiGroup->getMethod('func')->getMethodName());
    }

    function testApi()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(Api::class,$func->getApiTag());
        $this->assertEquals('func',$func->getApiTag()->name);
        $this->assertEquals('/apiGroup/func',$func->getApiTag()->path);
    }

    function testApiAuth()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2,count($func->getApiAuth()));
        $this->assertInstanceOf(ApiAuth::class,$func->getApiAuth('apiAuth1'));
    }

    function testApiDescription()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertInstanceOf(ApiDescription::class,$func->getApiDescriptionTag());
        $this->assertEquals('func desc',$func->getApiDescriptionTag()->value);
    }

    function testApiFail()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2,count($func->getApiFail()));
        $this->assertInstanceOf(ApiFail::class,$func->getApiFail()[0]);
        $this->assertEquals('func fail example1',$func->getApiFail()[0]->value);
    }

    function testApiFailParam()
    {
        /** @var MethodAnnotation $func */
        $func = $this->apiGroup->getMethod('func');
        $this->assertEquals(2,count($func->getApiFailParam()));
        $this->assertInstanceOf(ApiFailParam::class,$func->getApiFailParam('failParam1'));
        $this->assertEquals('failParam1',$func->getApiFailParam('failParam1')->name);
    }
}