<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AllDigital;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use PHPUnit\Framework\TestCase;

class AllDigitalTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "no" => 5001
        ]);

        $param = new Param(name:"no");
        $param->parsedValue($request);

        $rule = new AllDigital();

        $request = new ValidateRequest($param);

        $this->assertEquals(true, $rule->execute($request));


        $request = new Request();
        $request->withQueryParams([
            "no" => 005001
        ]);

        $param = new Param(name:"no");
        $param->parsedValue($request);

        $request = new ValidateRequest($param);

        $rule = new AllDigital();
        $this->assertEquals(true, $rule->execute($request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 含有英文
        $request = new Request();
        $request->withQueryParams([
            "no" => "0bA111"
        ]);

        $param = new Param(name:"no");
        $param->parsedValue($request);

        $rule = new AllDigital();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute($request));
        $this->assertEquals("no must be all digital", $rule->errorMsg($request));

        // 含有小数点
        $request = new Request();
        $request->withQueryParams([
            "no" => "111.11"
        ]);

        $param = new Param(name:"no");
        $param->parsedValue($request);

        $rule = new AllDigital();
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute($request));
        $this->assertEquals("no must be all digital", $rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "no" => "111.11"
        ]);

        $param = new Param(name:"no");
        $param->parsedValue($request);

        $rule = new AllDigital(errorMsg: '学号只能由数字构成');
        $request = new ValidateRequest($param);
        $this->assertEquals(false, $rule->execute($request));
        $this->assertEquals("学号只能由数字构成", $rule->errorMsg($request));
    }
}