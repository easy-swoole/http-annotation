<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Required;
use PHPUnit\Framework\TestCase;

class RequiredTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Required();
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => null,
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Required();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str is required",$rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 10,
        ]);

        $param = new Param(name:"phone");
        $param->parsedValue($request);

        $rule = new Required(errorMsg: '手机号码必填');
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("手机号码必填",$rule->errorMsg($request));
    }
}
