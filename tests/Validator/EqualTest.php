<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Equal;
use PHPUnit\Framework\TestCase;

class EqualTest extends TestCase
{
    // 参数必须与***相同
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Equal("easyswoole");

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

        $rule = new Equal("20");

        $rule->allCheckParams([
            "str" => $param,
        ]);

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => 0,
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Equal("0", strict: true);

        $rule->allCheckParams([
            "str" => $param
        ]);

        $this->assertEquals(false, $rule->execute($param, $request));

    }
}
