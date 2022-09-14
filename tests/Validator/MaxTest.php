<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Max;
use PHPUnit\Framework\TestCase;

class MaxTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // int 整数 (不超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => 99
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
        // int 整数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => 100
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
        // float 浮点数 (不超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => 99.9
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
        // float 浮点数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => 100.1
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100.1);
        $this->assertEquals(true, $rule->execute($param, $request));
        // 字符串整数 (不超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => '99'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
        // 字符串整数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => '100'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
        // 字符串浮点数 (不超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => '99.9'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
        // 字符串浮点数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => '100.0'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // int
        $request = new Request();
        $request->withQueryParams([
            "num" => 101
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num max value is 100",$rule->errorMsg());
        // float
        $request = new Request();
        $request->withQueryParams([
            "num" => 100.1
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num max value is 100",$rule->errorMsg());
        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "num" => '101'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num max value is 100",$rule->errorMsg());
        // 字符串浮点数
        $request = new Request();
        $request->withQueryParams([
            "num" => '100.1'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num max value is 100",$rule->errorMsg());
        // 非数字字符串
        $request = new Request();
        $request->withQueryParams([
            "num" => '101.0.1'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(max: 100);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num max value is 100",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "price" => 101
        ]);

        $param = new Param("price");
        $param->parsedValue($request);

        $rule = new Max(max: 100,errorMsg: '价钱不超过100');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("价钱不超过100",$rule->errorMsg());
    }
}
