<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Func;
use EasySwoole\HttpAnnotation\Validator\Funcs\Equal1;
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

        $rule = new Func(func: function (AbstractValidator $validator) {
            return $validator->getRequest()->getQueryParams()['fun'] == "123456789";
        });

        $this->assertEquals(true, $rule->execute($param, $request));
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

        $rule = new Func(func: function (AbstractValidator $validator) {
            return $validator->getRequest()->getQueryParams()['fun'] == "222";
        });

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("fun validate fail in custom function",$rule->errorMsg());
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

        $rule = new Func(func: function (AbstractValidator $validator) {
            return $validator->getRequest()->getQueryParams()['fun'] == "222";
        }, errorMsg: '测试提示');

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("测试提示",$rule->errorMsg());
    }

    function testEqual1()
    {
        $request = new Request();
        $request->withQueryParams([
            "fun" => '1',
        ]);

        $param = new Param(name:"fun");
        $param->parsedValue($request);

        $rule = new Func(new Equal1());
        $this->assertEquals(true, $rule->execute($param, $request));
//        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}

