<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\BetweenMbLen;
use PHPUnit\Framework\TestCase;

class BetweenMbLenTest extends TestCase
{

    /*
    * 合法
    */
    public function testValidCase()
    {
        // 字符长度范围必须在min ~ max之间 汉字、字母 = 1
        $request = new Request();
        $request->withQueryParams([
            "name" => '城南花已开'
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(minLen: 5, maxLen: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // 英文
        $request = new Request();
        $request->withQueryParams([
            "name" => 'bajiu'
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(minLen: 5, maxLen: 10);
        $this->assertEquals(true, $rule->execute($param, $request));

        // func
        $request = new Request();
        $request->withQueryParams([
            "num" => 5.56
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);

    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => '城南花已开'
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(minLen: 2, maxLen: 4);
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("name length must between 2 to 4", $rule->errorMsg());
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => '八九'
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(minLen: 5, maxLen: 10,errorMsg: "testCustomErrorMsgCase");
        $rule->execute($param, $request);

        $this->assertEquals("testCustomErrorMsgCase", $rule->errorMsg());

    }
}
