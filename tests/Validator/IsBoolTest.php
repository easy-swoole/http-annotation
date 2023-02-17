<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\IsBool;
use PHPUnit\Framework\TestCase;

class IsBoolTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // "1",1,"0",0,true,false
        // 值为true
        $request = new Request();
        $request->withQueryParams([
            "bool" => true
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        // 值为 1 等同于 true
        $request = new Request();
        $request->withQueryParams([
            "bool" => 1
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        // 值为 1 等同于 true
        $request = new Request();
        $request->withQueryParams([
            "bool" => "1"
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        // 值为false
        $request = new Request();
        $request->withQueryParams([
            "bool" => false
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        // 值为 0 等同于 false
        $request = new Request();
        $request->withQueryParams([
            "bool" => 0
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 值为文本值无法通过
        $request = new Request();
        $request->withQueryParams([
            "bool" => 'true'
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("bool must be bool",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "bool" => null
        ]);

        $param = new Param(name:"bool");
        $param->parsedValue($request);

        $rule = new IsBool('状态只能是开启或关闭');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("状态只能是开启或关闭",$rule->errorMsg());
    }
}
