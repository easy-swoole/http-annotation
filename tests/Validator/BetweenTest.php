<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Between;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 整数
        $request = new Request();
        $request->withQueryParams([
            "num" => 5
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 小数
        $request = new Request();
        $request->withQueryParams([
            "num" => 6.33
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 字符串
        $request = new Request();
        $request->withQueryParams([
            "num" => '6.33'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 等于最小值
        $request = new Request();
        $request->withQueryParams([
            "num" => 5
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 等于最大值
        $request = new Request();
        $request->withQueryParams([
            "num" => 10
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "num" => 5.5
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 不在值之间
        $request = new Request();
        $request->withQueryParams([
            "num" => 110
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must between 5 to 10", $rule->errorMsg());

        // 不是合法值
        $request = new Request();
        $request->withQueryParams([
            "num" => 'bajiu'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must between 5 to 10", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 'bajiu'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Between(min: 5, max: 10,errorMsg: '您输入的年龄不符');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("您输入的年龄不符", $rule->errorMsg());
    }
}