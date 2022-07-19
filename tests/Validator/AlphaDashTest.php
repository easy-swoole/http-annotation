<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AlphaDash;
use PHPUnit\Framework\TestCase;

class AlphaDashTest extends TestCase
{
    function testNormal()
    {
        // 字段值仅允许大小写字母、数字、破折号（-）以及下划线（_）
        $request = new Request();
        $request->withQueryParams([
            "str"=>"qweqwe-123_"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AlphaDash();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"AbsjkjaKKKK-1111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AlphaDash();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"0bA1-11_..@"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AlphaDash();
        $this->assertEquals(false,$rule->execute($param,$request));
    }
}
