<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\AlphaNum;
use EasySwoole\HttpAnnotation\Enum\ValueFrom;
use PHPUnit\Framework\TestCase;

class AlphaNumTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 字段值仅允许大小写字母、数字
        $request = new Request();
        $request->withQueryParams([
            "no" => "Answer123"
        ]);

        $param = new Param("no", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new AlphaNum();
        $this->assertEquals(true, $rule->execute($param, $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "no" => "0bA111..@"
        ]);

        $param = new Param("no", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new AlphaNum();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("no must be all AlphaNum", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "no" => "0bA111..@"
        ]);

        $param = new Param("no", [ValueFrom::GET]);
        $param->parsedValue($request);

        $rule = new AlphaNum(errorMsg: '只能由字母和数字构成');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("只能由字母和数字构成", $rule->errorMsg());
    }
}
