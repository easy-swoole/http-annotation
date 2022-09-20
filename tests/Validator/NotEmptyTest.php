<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\NotEmpty;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;

class NotEmptyTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 不为空字符串
        $request = new Request();
        $request->withQueryParams([
            "str" => "easyswoole",
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(true, $rule->execute($param, $request));

        // 数值0
        $request = new Request();
        $request->withQueryParams([
            "str" => 0,
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(true, $rule->execute($param, $request));

        // 字符0
        $request = new Request();
        $request->withQueryParams([
            "str" => "0",
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(true, $rule->execute($param, $request));

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 空字符串
        $request = new Request();
        $request->withQueryParams([
            "str" => "",
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str is notEmpty",$rule->errorMsg());

        // null
        $request = new Request();
        $request->withQueryParams([
            "str" => null,
        ]);

        $param = new Param("str", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new NotEmpty();

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str is notEmpty",$rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => "",
        ]);

        $param = new Param("name", [ParamFrom::GET]);
        $param->parsedValue($request);

        $rule = new NotEmpty(errorMsg: '名字必填');

        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("名字必填",$rule->errorMsg());
    }
}
