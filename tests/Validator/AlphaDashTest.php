<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AlphaDash;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use PHPUnit\Framework\TestCase;

class AlphaDashTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 字段值仅允许大小写字母、数字、破折号（-）以及下划线（_）
        $request = new Request();
        $request->withQueryParams([
            "str" => "qweqwe-123_"
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new AlphaDash();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute($request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "0bA1-11_..@"
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new AlphaDash();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute($request));
        $this->assertEquals("str must be all AlphaDash", $rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "0bA1-11_..@"
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new AlphaDash(errorMsg: '只能由字母数字下划线和破折号构成');
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute($request));
        $this->assertEquals("只能由字母数字下划线和破折号构成", $rule->errorMsg($request));
    }
}
