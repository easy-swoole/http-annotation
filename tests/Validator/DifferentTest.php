<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Different;
use PHPUnit\Framework\TestCase;

class DifferentTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 值不相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Different(compare: "easySwoole");
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));

        // 值相等,但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "age" => "12",
        ]);

        $param = new Param(name:"age");
        $param->parsedValue($request);

        $rule = new Different(compare: 12,strict: true);
        $request = new ValidateRequest($param);
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 值相等
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Different(compare: "easyswoole",strict: true);
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str must different with easyswoole",$rule->errorMsg($request->validateParam->name));

        // 值相等,但类型不一样
        $request = new Request();
        $request->withQueryParams([
            "str" => 12,
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Different(compare: "12");
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str must different with 12",$rule->errorMsg($request->validateParam->name));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => 0,
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new Different(compare: "0",errorMsg: '参数必须不等于0');
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("参数必须不等于0",$rule->errorMsg($request->validateParam->name));
    }
}
