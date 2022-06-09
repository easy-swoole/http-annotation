<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AllDigital;
use PHPUnit\Framework\TestCase;

class AllDigitalTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "str"=>5001
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AllDigital();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>005001
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AllDigital();
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>"0bA111"
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new AllDigital();
        $this->assertEquals(false,$rule->execute($param,$request));
    }
}