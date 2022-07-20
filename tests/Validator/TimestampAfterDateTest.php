<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\TimestampAfterDate;
use PHPUnit\Framework\TestCase;

class TimestampAfterDateTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => time()
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampAfterDate(date('YmdHis', time() - 1));
        $this->assertEquals(true, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "date" => time() + 1
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampAfterDate(date('YmdHis', time()));
        $this->assertEquals(true, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "date" => "2022-09-30"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new TimestampAfterDate(date: function () {
            return date('YmdHis', time());
        },errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
