<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "num" => 10
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Money();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10.1
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Money(precision: 1);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10.2
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Money(precision: 2);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10.1
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Money();
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => "10.03"
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Money(precision: 1);
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 2
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Money(precision: 2,errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
