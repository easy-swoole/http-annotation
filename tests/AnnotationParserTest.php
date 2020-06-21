<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;
use EasySwoole\HttpAnnotation\Annotation\Parser;
use EasySwoole\HttpAnnotation\AnnotationTag\ApiGroup;
use EasySwoole\HttpAnnotation\Tests\TestController\ControllerA;
use EasySwoole\HttpAnnotation\Tests\TestController\ControllerB;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;

class AnnotationParserTest extends TestCase
{
    /**
     * @var ObjectAnnotation
     */
    protected $resultA;
    /** @var ObjectAnnotation */
    protected $resultB;

    function run(TestResult $result = null): TestResult
    {
        $this->resultA = (new Parser())->getObjectAnnotation(ControllerA::class);
        $this->resultB = (new Parser())->getObjectAnnotation(ControllerB::class);
        return parent::run($result);
    }

    function testApiGroup()
    {

    }
}