<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\TimestampBefore;
use PHPUnit\Framework\TestCase;

class TimestampBeforeTest extends TestCase
{
    /*
        * 合法
        */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time() - 1
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new TimestampBefore(compare: time());
        $this->assertEquals(true, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => time() - 1
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new TimestampBefore(time());
        $this->assertEquals(false, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-06-30"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new TimestampBefore(compare: time(), errorMsg: '无效时间戳');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("无效时间戳", $rule->errorMsg());
    }
}
