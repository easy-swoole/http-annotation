<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\MinMbLength;
use PHPUnit\Framework\TestCase;

class MinMbLengthTest extends TestCase
{
    //
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "str" => '测试测试测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MinMbLength(6);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '123..'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MinMbLength(5);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '12测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MinMbLength(5);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '12测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new MinMbLength(5, errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
