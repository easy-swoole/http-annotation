<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Required;
use PHPUnit\Framework\TestCase;

class RequiredTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Required();

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10,
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Required();

        $this->assertEquals(false, $rule->execute($param, $request));


        // errorMsg
        $request = new Request();
        $request->withQueryParams([
            "num" => 10,
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Required("测试提示");

        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());

    }
}
