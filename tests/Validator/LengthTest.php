<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Length;
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

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 5);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '12345'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 5);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 3);
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
            "str" => 123
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg());

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '123'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg());
        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg());

        // 对象
        $request = new Request();
        $request->withQueryParams([
            "str" => (object)['apple', 'grape', 'orange', 'orange', 'orange']
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Length(length: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str length must be 5",$rule->errorMsg());
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

        $param = new Param("name");
        $param->parsedValue($request);

        $rule = new Length(length: 6,errorMsg: '名字长度必须是6位');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("名字长度必须是6位",$rule->errorMsg());
    }
}
