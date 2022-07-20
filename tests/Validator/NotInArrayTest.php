<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\NotInArray;
use PHPUnit\Framework\TestCase;

class NotInArrayTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 4
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new NotInArray(array: [1, 2, 3], strict: false);
        $this->assertEquals(true, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "str" => '2'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new NotInArray(array: [1, 2, 3], strict: true);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => "4"
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new NotInArray(array: [1, 2, 3], strict: true);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '测试'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new NotInArray(array: [1, 2, 3], strict: false, errorMsg: '测试提示');
        $this->assertEquals(true, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
