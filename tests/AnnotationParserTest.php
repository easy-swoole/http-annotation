<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\Tests\TestController\ApiGroup;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;

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
        $this->assertInstanceOf(\EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup::class,$this->apiGroup->getApiGroupTag());
        $this->assertEquals('GroupA',$this->apiGroup->getApiGroupTag()->groupName);
    }
}