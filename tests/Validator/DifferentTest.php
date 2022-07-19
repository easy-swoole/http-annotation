<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Different;
use PHPUnit\Framework\TestCase;

class DifferentTest extends TestCase
{
    // 参数必须与***不同
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Different("easyAccount");

        $rule->allCheckParams([
            "str" => $param,
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => "20",
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Different("20");

        $rule->allCheckParams([
            "str" => $param,
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => 0,
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Different("0", strict: true);

        $rule->allCheckParams([
            "str" => $param
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

    }
}
