<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\TimestampBeforeDate;
use PHPUnit\Framework\TestCase;

class TimestampBeforeDateTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time() - 1
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampBeforeDate(date('YmdHis', time()));
        $this->assertEquals(true, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampBeforeDate(date('YmdHis', time()));
        $this->assertEquals(false, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-06-30"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampBeforeDate(date: function () {
            return date('YmdHis', time());
        },errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
