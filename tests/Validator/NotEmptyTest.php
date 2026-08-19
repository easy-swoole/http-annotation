<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 不为空字符串
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new NotEmpty();
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

        // 数值0
        $request = new Request();
        $request->withQueryParams([
            "str" => 0,
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new NotEmpty();
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

        // 字符0
        $request = new Request();
        $request->withQueryParams([
            "str" => "0",
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new NotEmpty();
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 空字符串
        $request = new Request();
        $request->withQueryParams([
            "str" => "",
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new NotEmpty();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str is notEmpty",$rule->errorMsg($request));

        // null
        $request = new Request();
        $request->withQueryParams([
            "str" => null,
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new NotEmpty();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str is notEmpty",$rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => "",
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new NotEmpty(errorMsg: '名字必填');
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("名字必填",$rule->errorMsg($request));
    }
}
