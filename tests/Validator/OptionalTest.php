<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Optional;
use PHPUnit\Framework\TestCase;

class OptionalTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Optional();

        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10,
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new Optional();

        $this->assertEquals(true, $rule->execute($param, $request));
    }
}
