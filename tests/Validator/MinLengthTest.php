<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\MinLength;
use PHPUnit\Framework\TestCase;

class MinLengthTest extends TestCase
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

        $rule = new MinLength(minLen: 5);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '12345'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new MinLength(minLen: 5);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new MinLength(minLen: 3);
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
            "str" => 1234
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new MinLength(minLen: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str min length is 5",$rule->errorMsg());

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '1234'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new MinLength(minLen: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str min length is 5",$rule->errorMsg());
        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new MinLength(minLen: 4);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str min length is 4",$rule->errorMsg());

        // 对象
        $request = new Request();
        $request->withQueryParams([
            "str" => (object)['apple', 'grape', 'orange', 'orange', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new MinLength(minLen: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str min length is 5",$rule->errorMsg());
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

        $rule = new MinLength(minLen: 6,errorMsg: '名字长度最少6位');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("名字长度最少6位",$rule->errorMsg());
    }
}
