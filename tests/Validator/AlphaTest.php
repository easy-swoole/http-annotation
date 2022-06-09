<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AllDigital;
use EasySwoole\HttpAnnotation\Attributes\Validator\Alpha;
use PHPUnit\Framework\TestCase;

class AlphaTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str"=>"abcheezsss"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"AbsjkjaKKKK"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"0bA111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Alpha();
        $this->assertEquals(false,$rule->execute($param,$request));
    }
}