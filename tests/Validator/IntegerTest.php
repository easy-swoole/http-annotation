<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Integer;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    public function testOnlyIntegersAndIntegerStringsAreAccepted(): void
    {
        $rule = new Integer();
        foreach ([true, false, 1.0, 0.0, 1.5, [], null, '1.0', '1e2',
            '999999999999999999999'] as $value) {
            $param = new Param(name: 'num', type: null, value: $value);
            self::assertFalse($rule->execute(new ValidateRequest($param)), get_debug_type($value));
        }
        foreach ([0, 1, -1, PHP_INT_MAX, '0', '1', '-1', (string)PHP_INT_MAX] as $value) {
            $param = new Param(name: 'num', type: null, value: $value);
            self::assertTrue($rule->execute(new ValidateRequest($param)));
        }
    }

    /*
    * 合法
    */
    public function testValidCase()
    {
        // 正常的int类型
        $request = new Request();
        $request->withQueryParams([
            "num" => 2
        ]);

        $param = new Param(type: null, name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Integer();
        $this->assertEquals(true, $rule->execute( $request));
        // 文本型int
        $request = new Request();
        $request->withQueryParams([
            "num" => "2"
        ]);

        $param = new Param(type: null, name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Integer();
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 不是一个数字
        $request = new Request();
        $request->withQueryParams([
            "num" => 'bajiu'
        ]);

        $param = new Param(type: null, name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Integer();
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num must be integer",$rule->errorMsg($request->validateParam->name));
        // 不是一个整数
        $request = new Request();
        $request->withQueryParams([
            "num" => 0.001
        ]);

        $param = new Param(type: null, name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Integer();
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num must be integer",$rule->errorMsg($request->validateParam->name));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 0.001
        ]);

        $param = new Param(type: null, name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new Integer(errorMsg: '请输入正确的数量');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("请输入正确的数量",$rule->errorMsg($request->validateParam->name));
    }
}
