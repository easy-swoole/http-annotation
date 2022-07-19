<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxLength;
use PHPUnit\Framework\TestCase;

class MaxLengthTest extends TestCase
{
    //
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "str" => '12'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxLength(5);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '12345'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxLength(5);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '123456'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxLength(5);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '123456'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxLength(5, errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
