<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\TimestampAfter;
use PHPUnit\Framework\TestCase;

class TimestampAfterTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampAfter(date:time());
        $this->assertEquals(true, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampAfter(date: function () {
            return time();
        });
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time()
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $time = time() + 1;
        $rule = new TimestampAfter(date:$time);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be timestamp after {$time}", $rule->errorMsg());

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-09-30"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $time = time() + 1;
        $rule = new TimestampAfter(date: function () use ($time) {
            return $time;
        });
        $this->assertEquals(false, $rule->execute($param, $request));

        $this->assertEquals("date must be timestamp after {$time}", $rule->errorMsg());
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

        $param = new Param("date");
        $param->parsedValue($request);

        $time = time() + 1;
        $rule = new TimestampAfter(date: $time, errorMsg: '无效时间戳');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("无效时间戳", $rule->errorMsg());
    }
}