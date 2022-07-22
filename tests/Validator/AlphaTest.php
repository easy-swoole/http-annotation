<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AllDigital;
use EasySwoole\HttpAnnotation\Attributes\Validator\Alpha;
use PHPUnit\Framework\TestCase;

class AlphaTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 只能是字母
        $request = new Request();
        $request->withQueryParams([
            "str" => "abcheezsss"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "0bA111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must be all alpha", $rule->errorMsg());

        $request = new Request();
        $request->withQueryParams([
            "str" => "111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str must be all alpha", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "0bA111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha(errorMsg: '您输入的参数不合法');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("您输入的参数不合法", $rule->errorMsg());
    }
}