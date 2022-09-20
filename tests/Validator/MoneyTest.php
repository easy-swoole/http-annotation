<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Money;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{

    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 10
        ]);

        $param = new Param("num", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Money();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10.1
        ]);

        $param = new Param("num", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Money(precision: 1);
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "num" => 10.20
        ]);

        $param = new Param("num", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Money(precision: 2);
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 10.123
        ]);

        $param = new Param("num", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Money(precision: 2);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must be legal amount with 2 precision",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 10.123
        ]);

        $param = new Param("num", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new Money(precision: 2,errorMsg: '金额必须两位小数');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("金额必须两位小数",$rule->errorMsg());
    }
}
