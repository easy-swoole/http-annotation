<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\NotInArray;
use PHPUnit\Framework\TestCase;

class NotInArrayTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // strict true
        $request = new Request();
        $request->withQueryParams([
            "fruit" => 'Apple'
        ]);

        $param = new Param(name:"num");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new NotInArray(array: ['apple', 'grape', 'orange'], strict: true);
        $this->assertEquals(true, $rule->execute( $request));

        // strict false
        $request = new Request();
        $request->withQueryParams([
            "fruit" => 'banana'
        ]);

        $param = new Param(name:"fruit");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new NotInArray(array: ['apple', 'grape', 'orange'], strict: false);
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "fruit" => 'apple'
        ]);

        $param = new Param(name:"fruit");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new NotInArray(array: ['apple', 'grape', 'orange'], strict: false);
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals('fruit must not in array of ["apple","grape","orange"]',$rule->errorMsg($request));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "fruit" => 'apple'
        ]);

        $param = new Param(name:"fruit");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new NotInArray(array: ['apple', 'grape', 'orange'], errorMsg: '水果不能是苹果、葡萄以及橘子');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("水果不能是苹果、葡萄以及橘子",$rule->errorMsg($request));
    }
}
