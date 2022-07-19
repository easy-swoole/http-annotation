<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Max;
use PHPUnit\Framework\TestCase;

class MaxTest extends TestCase
{
    //
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "num" => 99
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(100);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 101
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(100);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 100.1
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(100);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 'test'
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(100);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 101
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Max(100, errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
