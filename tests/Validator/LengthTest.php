<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Length;
use PHPUnit\Framework\TestCase;

class LengthTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // int
        $request = new Request();
        $request->withQueryParams([
            "str" => 12345
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 5);
        $this->assertEquals(true, $rule->execute( $request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '12345'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 5);
        $this->assertEquals(true, $rule->execute( $request));

        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 3);
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
            "str" => 123
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg($request->validateParam->name));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '123'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg($request->validateParam->name));
        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg($request->validateParam->name));

        // 对象
        $request = new Request();
        $request->withQueryParams([
            "str" => (object)['apple', 'grape', 'orange', 'orange', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg($request->validateParam->name));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => 'bajiu'
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Length(length: 6,errorMsg: '名字长度必须是6位');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("名字长度必须是6位",$rule->errorMsg($request->validateParam->name));
    }
}
