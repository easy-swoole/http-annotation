<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\IsFloat;
use PHPUnit\Framework\TestCase;

class IsFloatTest extends TestCase
{
    //
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "num" => 2
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new IsFloat();
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 2.0
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new IsFloat();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 2
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new IsFloat(errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
