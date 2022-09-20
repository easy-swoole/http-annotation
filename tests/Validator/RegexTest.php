<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Regex;
use EasySwoole\HttpAnnotation\Enum\ValueFrom;
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

        $param = new Param("phone", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Regex(rule: '/^1\d{10}$/');
        $this->assertEquals(true, $rule->execute($param, $request));
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

        $param = new Param("phone", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Regex(rule: '/^1\d{10}$/');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("phone must meet specified rules",$rule->errorMsg());
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

        $param = new Param("phone", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new Regex(rule: '/^1\d{10}$/',errorMsg: '手机号码格式不对');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("手机号码格式不对",$rule->errorMsg());
    }
}
