<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\Between;
use PHPUnit\Framework\TestCase;

class BetweenTest extends TestCase
{
    function testInt(){
        $request = new Request();
        $request->withQueryParams([
            "num"=>5
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "num"=>8
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(true,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>4
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(false,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>11
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(false,$rule->execute($param,$request));
    }


    function testFloat(){
        $request = new Request();
        $request->withQueryParams([
            "num"=>5.5
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(true,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "num"=>8.1
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(true,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>4.9
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(false,$rule->execute($param,$request));

        $request = new Request();
        $request->withQueryParams([
            "num"=>11.2
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:5,max: 10);
        $this->assertEquals(false,$rule->execute($param,$request));
    }

    function testFuncCall(){
        $request = new Request();
        $request->withQueryParams([
            "num"=>5.5
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:function (){
            return 5.1;
        },max: function (){
            return 8.5;
        });
        $this->assertEquals(true,$rule->execute($param,$request));



        $request = new Request();
        $request->withQueryParams([
            "num"=>5
        ]);

        $param = new Param("num");
        $param->parsedValue($request);

        $rule = new Between(min:function (){
            return 5.1;
        },max: function (){
            return 8.5;
        });
        $this->assertEquals(false,$rule->execute($param,$request));

        $rule->currentCheckParam($param);

        $this->assertEquals("num must between 5.1 to 8.5",$rule->errorMsg());



    }
}