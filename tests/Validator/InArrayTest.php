<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\InArray;
use PHPUnit\Framework\TestCase;

class InArrayTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => 2
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new InArray(array: [1, 2, 3], strict: false);
        $request = new ValidateRequest($param);

        $this->assertEquals(true, $rule->execute( $request));

        $request = new Request();
        $request->withQueryParams([
            "str" => '2'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new InArray(array: [1, 2, 3], strict: false);
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "num" => "3"
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new InArray(array: [1, 2, 3], strict: true);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("num must in array of [1,2,3]",$rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => '测试'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new InArray(array: [1, 2, 3], strict: false, errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("测试提示",$rule->errorMsg($request));
    }
}
