<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
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

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Required();

        $this->assertEquals(true, $rule->execute($param, $request));
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

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Required();

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str is required",$rule->errorMsg());
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

        $param = new Param("phone", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Required(errorMsg: '手机号码必填');

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("手机号码必填",$rule->errorMsg());
    }
}
