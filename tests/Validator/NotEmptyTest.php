<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\NotEmpty;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10,
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(false, $rule->execute($param, $request));

    }
}
