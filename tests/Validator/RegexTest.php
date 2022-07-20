<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "phone" => 15880809999
        ]);

        $param = new Param("phone");
        $param->parsedValue($request);

        $rule = new Regex('/^1\d{10}$/');
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "phone" => 158808099
        ]);

        $param = new Param("phone");
        $param->parsedValue($request);

        $rule = new Regex('/^1\d{10}$/');
        $this->assertEquals(false, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "phone" => 99.9
        ]);

        $param = new Param("phone");
        $param->parsedValue($request);

        $rule = new Regex('/^1\d{10}$/', errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
