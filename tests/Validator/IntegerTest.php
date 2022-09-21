<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Integer;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{

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

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Integer();
        $this->assertEquals(true, $rule->execute($param, $request));
        // 文本型int
        $request = new Request();
        $request->withQueryParams([
            "num" => "2"
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Integer();
        $this->assertEquals(true, $rule->execute($param, $request));
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

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Integer();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must be integer",$rule->errorMsg());
        // 不是一个整数
        $request = new Request();
        $request->withQueryParams([
            "num" => 0.001
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Integer();
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("num must be integer",$rule->errorMsg());
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

        $param = new Param(name:"num");
        $param->parsedValue($request);

        $rule = new Integer(errorMsg: '请输入正确的数量');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("请输入正确的数量",$rule->errorMsg());
    }
}
