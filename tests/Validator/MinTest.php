<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Min;
use PHPUnit\Framework\TestCase;

class MinTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // int 整数 (超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => 101
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
        // int 整数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => 100
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
        // float 浮点数 (超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => 100.1
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
        // float 浮点数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => 100.1
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100.1);
        $this->assertEquals(true, $rule->execute( $request));
        // 字符串整数 (超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => '101'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
        // 字符串整数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => '100'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
        // 字符串浮点数 (超过)
        $request = new Request();
        $request->withQueryParams([
            "num" => '100.1'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
        // 字符串浮点数 (相等)
        $request = new Request();
        $request->withQueryParams([
            "num" => '100.0'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // int
        $request = new Request();
        $request->withQueryParams([
            "num" => 99
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num min value is 100",$rule->errorMsg($request->validateParam->name));
        // float
        $request = new Request();
        $request->withQueryParams([
            "num" => 99.9
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num min value is 100",$rule->errorMsg($request->validateParam->name));
        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "num" => '99'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num min value is 100",$rule->errorMsg($request->validateParam->name));
        // 字符串浮点数
        $request = new Request();
        $request->withQueryParams([
            "num" => '99.9'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num min value is 100",$rule->errorMsg($request->validateParam->name));
        // 非数字字符串
        $request = new Request();
        $request->withQueryParams([
            "num" => '99.0.1'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num min value is 100",$rule->errorMsg($request->validateParam->name));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "price" => 99
        ]);

        $param = new Param(name:"price");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Min(min: 100,errorMsg: '价钱最低100');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("价钱最低100",$rule->errorMsg($request->validateParam->name));
    }
}
