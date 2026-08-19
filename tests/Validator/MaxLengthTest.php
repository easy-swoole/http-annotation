<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\MaxLength;
use PHPUnit\Framework\TestCase;

class MaxLengthTest extends TestCase
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
        $rule = new MaxLength(maxLen: 5);
        $this->assertEquals(true, $rule->execute( $request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '12345'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MaxLength(maxLen: 5);
        $this->assertEquals(true, $rule->execute( $request));

        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MaxLength(maxLen: 3);
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
            "str" => 123456
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MaxLength(maxLen: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str max length is 5",$rule->errorMsg($request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '123456'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MaxLength(maxLen: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str max length is 5",$rule->errorMsg($request));
        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MaxLength(maxLen: 2);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str max length is 2",$rule->errorMsg($request));

        // 对象
        $request = new Request();
        $request->withQueryParams([
            "str" => (object)['apple', 'grape', 'orange', 'orange', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MaxLength(maxLen: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str max length is 5",$rule->errorMsg($request));
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
        $rule = new MaxLength(maxLen: 4,errorMsg: '名字长度最多4位');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("名字长度最多4位",$rule->errorMsg($request));
    }
}
