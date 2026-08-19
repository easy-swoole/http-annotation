<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Tests\Funcs\EqualFunc;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Func;
use PHPUnit\Framework\TestCase;

class FuncTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "fun" => "123456789",
        ]);

        $param = new Param(name:"fun");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Func(func: function (ValidateRequest $validateRequest) {
            return $validateRequest->validateParam->parsedValue() == "123456789";
        });

        $this->assertEquals(true, $rule->execute($request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "fun" => "111",
        ]);

        $param = new Param(name:"fun");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Func(func: function (ValidateRequest $validateRequest) {
            return $validateRequest->validateParam->parsedValue() == "222";
        });

        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("fun validate fail in custom function",$rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "fun" => "111",
        ]);

        $param = new Param(name:"fun");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Func(func: function (ValidateRequest $validateRequest) {
            return $validateRequest->validateParam->parsedValue() == "222";
        }, errorMsg: '测试提示');

        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("测试提示",$rule->errorMsg($request));
    }

    function testEqual1()
    {
        $request = new Request();
        $request->withQueryParams([
            "fun" => '1',
        ]);

        $param = new Param(name:"fun");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Func(new EqualFunc(1));
        $this->assertEquals(true, $rule->execute( $request));
        $this->assertEquals("fun validate fail in Equal function",$rule->errorMsg($request));

        $rule = new Func(new EqualFunc(2));
        $this->assertEquals(false, $rule->execute( $request));
    }
}

