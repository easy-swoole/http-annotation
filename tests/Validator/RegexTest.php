<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "phone" => 15880809999
        ]);

        $param = new Param(name:"phone");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Regex(rule: '/^1\d{10}$/');
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "phone" => 158808099
        ]);

        $param = new Param(name:"phone");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $regex = '/^1\d{10}$/';
        $rule = new Regex(rule: $regex);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("phone must meet specified rule: {$regex}",$rule->errorMsg($request->validateParam->name));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "phone" => 158808099
        ]);

        $param = new Param(name:"phone");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Regex(rule: '/^1\d{10}$/',errorMsg: '手机号码格式不对');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("手机号码格式不对",$rule->errorMsg($request->validateParam->name));
    }
}
