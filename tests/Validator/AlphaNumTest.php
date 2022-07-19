<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AlphaNum;
use PHPUnit\Framework\TestCase;

class AlphaNumTest extends TestCase
{
    function testNormal()
    {
        // 字段值仅允许大小写字母、数字
        $request = new Request();
        $request->withQueryParams([
            "str"=>"qweqwe123"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AlphaNum();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"AbsjkjaKKKK1111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AlphaNum();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"0bA111..@"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AlphaNum();
        $this->assertEquals(false,$rule->execute($param,$request));
    }
}
