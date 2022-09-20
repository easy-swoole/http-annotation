<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Different;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;

class DifferentTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 值不相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Different(compare: "easySwoole");

        $this->assertEquals(true, $rule->execute($param, $request));

        // 值相等,但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "age" => "12",
        ]);

        $param = new Param("age", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Different(compare: 12,strict: true);

        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 值相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Different(compare: "easyswoole",strict: true);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must different with easyswoole",$rule->errorMsg());

        // 值相等,但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => 12,
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Different(compare: "12");

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must different with 12",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => 0,
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Different(compare: "0",errorMsg: '参数必须不等于0');

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("参数必须不等于0",$rule->errorMsg());
    }
}
