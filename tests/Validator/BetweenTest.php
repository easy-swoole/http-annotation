<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
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
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute( $request));

        // 小数
        $request = new Request();
        $request->withQueryParams([
            "num" => 6.33
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute( $request));

        // 字符串
        $request = new Request();
        $request->withQueryParams([
            "num" => '6.33'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute( $request));

        // 等于最小值
        $request = new Request();
        $request->withQueryParams([
            "num" => 5
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute( $request));

        // 等于最大值
        $request = new Request();
        $request->withQueryParams([
            "num" => 10
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(true, $rule->execute( $request));

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
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num must between 5 to 10", $rule->errorMsg($request->validateParam->name));

        // 不是合法值
        $request = new Request();
        $request->withQueryParams([
            "num" => 'bajiu'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num must between 5 to 10", $rule->errorMsg($request->validateParam->name));
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
        $request = new ValidateRequest($param);
        $rule = new Between(min: 5, max: 10,errorMsg: '您输入的年龄不符');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("您输入的年龄不符", $rule->errorMsg($request->validateParam->name));
    }
}