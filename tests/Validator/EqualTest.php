<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Equal;
use EasySwoole\HttpAnnotation\Enum\ValueFrom;
use PHPUnit\Framework\TestCase;

class EqualTest extends TestCase
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

        $param = new Param("str", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Equal(compare: "easyswoole");

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
        ]);

        $param = new Param("str", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Equal(compare: 89);

        $this->assertEquals(true, $rule->execute($param, $request));

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 值不相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Equal(compare: "easySwoole");

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must equal with easySwoole",$rule->errorMsg());

        // 值相等,类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => "89",
        ]);

        $param = new Param("str", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Equal(compare: 89,strict: true);

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must equal with 89",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Equal(compare: "easySwoole",errorMsg: '参数必须为easyswoole');

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("参数必须为easyswoole",$rule->errorMsg());
    }
}
