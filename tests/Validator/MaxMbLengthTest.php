<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MaxMbLength;
use PHPUnit\Framework\TestCase;

class MaxMbLengthTest extends TestCase
{
    //
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "str" => '测试测试测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxMbLength(6);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '123..'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxMbLength(5);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '1234测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxMbLength(5);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '1234测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MaxMbLength(5, errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
