<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Decimal;
use PHPUnit\Framework\TestCase;

class DecimalTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // null
        $request = new Request();
        $request->withQueryParams([
            "num" => 23.0
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(accuracy: null);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 0
        $request = new Request();
        $request->withQueryParams([
            "num" => 50.0
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(accuracy: 0);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 5.56789
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(accuracy: 5);
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 555
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(accuracy: 2);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must be decimal with 2 accuracy", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 555
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Decimal(accuracy: 2, errorMsg: 'num只能是小数');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num只能是小数", $rule->errorMsg());
    }
}
