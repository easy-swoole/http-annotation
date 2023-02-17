<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\IsNumeric;
use PHPUnit\Framework\TestCase;

class IsNumericTest extends TestCase
{
    /*
        * 合法
        */
    public function testValidCase()
    {
        // int
        $request = new Request();
        $request->withQueryParams([
            "age" => 18
        ]);

        $param = new Param(name:"age");
        $param->parsedValue($request);

        $rule = new IsNumeric();
        $this->assertEquals(true, $rule->execute($param, $request));
        // float
        $request = new Request();
        $request->withQueryParams([
            "price" => 18.1
        ]);

        $param = new Param(name:"price");
        $param->parsedValue($request);

        $rule = new IsNumeric();
        $this->assertEquals(true, $rule->execute($param, $request));
        // 字符整数
        $request = new Request();
        $request->withQueryParams([
            "age" => '18'
        ]);

        $param = new Param(name:"age");
        $param->parsedValue($request);

        $rule = new IsNumeric();
        $this->assertEquals(true, $rule->execute($param, $request));
        // 字符小数
        $request = new Request();
        $request->withQueryParams([
            "price" => '18.1'
        ]);

        $param = new Param(name:"price");
        $param->parsedValue($request);

        $rule = new IsNumeric();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 非数字
        $request = new Request();
        $request->withQueryParams([
            "price" => 'bajiu'
        ]);

        $param = new Param(name:"price");
        $param->parsedValue($request);

        $rule = new IsNumeric();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("price must be numeric",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "price" => 'bajiu'
        ]);

        $param = new Param(name:"price");
        $param->parsedValue($request);

        $rule = new IsNumeric(errorMsg: '价格必须是数字');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("价格必须是数字",$rule->errorMsg());
    }
}
