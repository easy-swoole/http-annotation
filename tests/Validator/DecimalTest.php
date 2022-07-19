<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Decimal;
use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
    // 参数必须是十进制小数位
    function testNormal()
    {
        // null
        $request = new Request();
        $request->withQueryParams([
            "num"=>23.0
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(null);
        $this->assertEquals(true,$rule->execute($param,$request));

        // 0
        $request = new Request();
        $request->withQueryParams([
            "num"=>50.0
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(0);
        $this->assertEquals(true,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>5.56789
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(5);
        $this->assertEquals(true,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>1.123456
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(5);
        $this->assertEquals(false,$rule->execute($param,$request));

        // errorMsg
        $request = new Request();
        $request->withQueryParams([
            "num"=>1.123456
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $errorMsg = '测试提示';
        $rule = new Decimal(5, $errorMsg);
        $this->assertEquals(false,$rule->execute($param,$request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
