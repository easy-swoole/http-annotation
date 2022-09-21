<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\IsFloat;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;

class IsFloatTest extends TestCase
{

    /*
    * 合法
    */
    public function testValidCase()
    {
        // 小数位浮点数
        $request = new Request();
        $request->withQueryParams([
            "num" => 2.0
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new IsFloat();
        $this->assertEquals(true, $rule->execute($param, $request));

        // 字符串表达
        $request = new Request();
        $request->withQueryParams([
            "num" => '12.3'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new IsFloat();
        $this->assertEquals(true, $rule->execute($param, $request));

        // 整数作为浮点数
        $request = new Request();
        $request->withQueryParams([
            "num" => 2
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new IsFloat();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 不是合法的浮点值
        $request = new Request();
        $request->withQueryParams([
            "num" => 'bajiu'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new IsFloat();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must be float",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 'bajiu'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new IsFloat(errorMsg: '请输入一个浮点数');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("请输入一个浮点数",$rule->errorMsg());
    }
}
