<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Tests\TestController\ApiGroup;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    protected $controller;

    function setUp()
    {
        parent::setUp();
        $this->controller = new ApiGroup();
        ContextManager::getInstance()->set("context",'context data');
        Di::getInstance()->set('di','di data');
    }

    function testDi()
    {

        $this->controller->__hook('index',$this->fakeRequest(),$this->fakeResponse());
        $this->assertEquals('di data',$this->controller->di);
        $this->controller->di = null;
    }

    function testContext()
    {
        $this->controller->__hook('index',$this->fakeRequest(),$this->fakeResponse());
        $this->assertEquals('context data',$this->controller->context);
        $this->controller->context = null;
    }


    protected function fakeRequest(string $requestPath = '/'):Request
    {
        $request = new Request();
        $request->getUri()->withPath($requestPath);
        return $request;
    }

    protected function fakeResponse():Response
    {
        return new Response();
    }

}