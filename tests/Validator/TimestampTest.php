<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
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
        $request = new ValidateRequest($param);
        $rule = new Timestamp();
        $this->assertEquals(false, $rule->execute( $request));


        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-06-30"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Timestamp(errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute( $request));

        $this->assertEquals("测试提示",$rule->errorMsg($request->validateParam->name));
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
        $request = new ValidateRequest($param);
        $rule = new Timestamp();
        $this->assertEquals(true, $rule->execute( $request));
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
        $request = new ValidateRequest($param);
        $rule = new Timestamp();
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("date must be timestamp",$rule->errorMsg($request->validateParam->name));
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
        $request = new ValidateRequest($param);
        $rule = new Timestamp(errorMsg: '无效时间戳');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("无效时间戳",$rule->errorMsg($request->validateParam->name));
    }
}
