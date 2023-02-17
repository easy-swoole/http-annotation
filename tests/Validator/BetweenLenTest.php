<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\BetweenLen;
use EasySwoole\Validate\tests\UploadFile;
use PHPUnit\Framework\TestCase;

class BetweenLenTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 字符长度范围必须在min ~ max之间  汉字=3  字母=1
        $request = new Request();
        $request->withQueryParams([
            "name" => 5.56789
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new BetweenLen(minLen: 5, maxLen: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 数字 英文 符号
        $request = new Request();
        $request->withQueryParams([
            "str" => 'asc-~+...9'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new BetweenLen(minLen: 5, maxLen: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => 4.9876
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new BetweenLen(minLen: 2, maxLen: 5);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("name length must between 2 to 5", $rule->errorMsg());

        // 一个汉字 3
        $request = new Request();
        $request->withQueryParams([
            "str" => '测试测试'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);

        $rule = new BetweenLen(minLen: 5, maxLen: 10);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("str length must between 5 to 10", $rule->errorMsg());

        // testFuncCall
        $request = new Request();
        $request->withQueryParams([
            "name" => 5.56
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "str" => '测试' // 6
        ]);

        $param = new Param(
            name:"str"
        );
        $param->parsedValue($request);

        $rule = new BetweenLen(minLen: 5, maxLen: 10,errorMsg: "testCustomErrorMsgCase");
        $rule->execute($param, $request);

        $this->assertEquals("testCustomErrorMsgCase", $rule->errorMsg());


    }
}
