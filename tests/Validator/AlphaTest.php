<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Alpha;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
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

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));
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

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str must be all alpha", $rule->errorMsg($request));

        $request = new Request();
        $request->withQueryParams([
            "str" => "111"
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str must be all alpha", $rule->errorMsg($request));
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

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Alpha(errorMsg: '您输入的参数不合法');
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("您输入的参数不合法", $rule->errorMsg($request));
    }
}