<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\BetweenMbLen;
use PHPUnit\Framework\TestCase;

class BetweenMbLenTest extends TestCase
{
    // 字符长度范围必须在min ~ max之间 汉字、字母 = 1
    function testNormal(){
        $request = new Request();
        $request->withQueryParams([
            "num"=>5.56789
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(5,10);
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "str"=>'asc-~+...9'
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(5,10);
        $this->assertEquals(true,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>4.9876
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(2,5);
        $this->assertEquals(false,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "str"=>'测试测试' //4 一个汉字 1
        ]);


        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(5,10);
        $this->assertEquals(false,$rule->execute($param,$request));
    }

    function testFuncCall(){
        $request = new Request();
        $request->withQueryParams([
            "num"=>5.56
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(function (){
            return 2;
        },function (){
            return 5;
        });
        $this->assertEquals(true,$rule->execute($param,$request));



        $request = new Request();
        $request->withQueryParams([
            "str"=>'测试' // 2
        ]);

        $param = new Param("str");
        $param->parsedValue($request);

        $rule = new BetweenMbLen(function (){
            return 7;
        }, function (){
            return 10;
        });
        $this->assertEquals(false,$rule->execute($param,$request));

        $rule->currentCheckParam($param);

        $this->assertEquals("str length must between 7 to 10",$rule->errorMsg());
    }
}
