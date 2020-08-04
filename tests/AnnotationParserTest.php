<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Annotation\MethodAnnotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
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
    }

    function testApi()
    {

    }
}