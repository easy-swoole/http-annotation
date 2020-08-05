<?php


namespace EasySwoole\HttpAnnotation\Tests;


use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\Exception\Annotation\MethodNotAllow;
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

    function testGroupAuth()
    {
        $response = $this->fakeResponse();
        $this->controller->__hook('index',$this->fakeRequest('/',[]),$response);
        $this->assertEquals('PE-groupParamA',$response->getBody()->__tostring());

        $response = $this->fakeResponse();
        $this->controller->__hook('index',$this->fakeRequest('/',null),$response);
        $this->assertEquals('index',$response->getBody()->__tostring());
    }

    function testAllowPostMethod()
    {
        try {
            $this->controller->__hook('allowPostMethod',$this->fakeRequest(),$this->fakeResponse());
        }catch (\Throwable $throwable){
            $this->assertInstanceOf(MethodNotAllow::class,$throwable);
        }

        $response = $this->fakeResponse();
        $request =  $this->fakeRequest('/allowPostMethod',null,['data'=>1]);
        $this->controller->__hook('allowPostMethod', $request, $response);
        $this->assertEquals('allowPostMethod',$response->getBody()->__tostring());
    }


    protected function fakeRequest(string $requestPath = '/',array $query = null,array $post = []):Request
    {
        if($query === null){
            $query = [
                "groupParamA"=>"groupParamA",
                'groupParamB'=>"groupParamB"
            ];
        }else if(!empty($query)){
            $query = $query + [
                    "groupParamA"=>"groupParamA",
                    'groupParamB'=>"groupParamB"
                ];
        }
        $request = new Request();
        $request->getUri()->withPath($requestPath);
        //全局的参数
        $request->withQueryParams($query);
        if(!empty($post)){
            $request->withMethod('POST')->withParsedBody($post);
        }else{
            $request->withMethod('GET');
        }
        return $request;
    }

    protected function fakeResponse():Response
    {
        return new Response();
    }

}