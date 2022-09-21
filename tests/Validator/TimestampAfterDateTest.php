<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\TimestampAfterDate;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;

class TimestampAfterDateTest extends TestCase
{

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

        $rule = new TimestampAfterDate(date: date('YmdHis', time() - 1));
        $this->assertEquals(true, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new TimestampAfterDate(date: function () {
            return date('YmdHis', time());
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

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $time = date('YmdHis', time() + 1);
        $rule = new TimestampAfterDate(date: $time);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("date must be timestamp after {$time}", $rule->errorMsg());

        // func
        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-09-30"
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $time = date('YmdHis', time());
        $rule = new TimestampAfterDate(date: function () use ($time) {
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

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $time = date('YmdHis', time() + 1);
        $rule = new TimestampAfterDate(date: $time, errorMsg: '无效时间戳');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("无效时间戳", $rule->errorMsg());
    }
}
