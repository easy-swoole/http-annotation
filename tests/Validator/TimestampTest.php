<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Timestamp;
use PHPUnit\Framework\TestCase;

class TimestampTest extends TestCase
{
    function testNormal()
    {



        $request = new Request();
        $request->withQueryParams([
            "date" => '123456789123456'
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Timestamp();
        $this->assertEquals(false, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-06-30"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Timestamp(errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }

    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time()
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Timestamp();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => 'bajiu'
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Timestamp();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be timestamp",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => 'bajiu'
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Timestamp(errorMsg: '无效时间戳');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("无效时间戳",$rule->errorMsg());
    }
}
