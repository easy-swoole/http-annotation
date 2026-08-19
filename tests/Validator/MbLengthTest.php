<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\MbLength;
use PHPUnit\Framework\TestCase;

class MbLengthTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        //
        $request = new Request();
        $request->withQueryParams([
            "str" => '八九'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 2);
        $this->assertEquals(true, $rule->execute( $request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '89'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 2);
        $this->assertEquals(true, $rule->execute( $request));

        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 3);
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        //
        $request = new Request();
        $request->withQueryParams([
            "str" => '八九'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str mb length must be 5",$rule->errorMsg($request));

        // 字符串整数
        $request = new Request();
        $request->withQueryParams([
            "str" => '89'
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str mb length must be 5",$rule->errorMsg($request));
        // 数组
        $request = new Request();
        $request->withQueryParams([
            "str" => ['apple', 'grape', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str mb length must be 5",$rule->errorMsg($request));

        // 对象
        $request = new Request();
        $request->withQueryParams([
            "str" => (object)['apple', 'grape', 'orange', 'orange', 'orange']
        ]);

        $param = new Param(name:"str");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 5);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("str mb length must be 5",$rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "name" => '城南花已开'
        ]);

        $param = new Param(name:"name");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new MbLength(length: 6,errorMsg: '名字长度必须是6位');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("名字长度必须是6位",$rule->errorMsg($request));
    }
}
