<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\DateAfter;
use PHPUnit\Framework\TestCase;

class DateAfterTest extends TestCase
{
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "date"=>"20220501"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateAfter("20220430");
        $this->assertEquals(true,$rule->execute($param,$request));



        $request = new Request();
        $request->withQueryParams([
            "date"=>"20220501"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateAfter("20220630");
        $this->assertEquals(false,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "date"=>"2022-05-06"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateAfter("20200630");
        $this->assertEquals(true,$rule->execute($param,$request));

        //字段必须是日期格式。因此传时间戳，失败
        $request = new Request();
        $request->withQueryParams([
            "date"=>"1654765394"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateAfter("20220530");
        $this->assertEquals(false,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "date"=>"20220630"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateAfter(date:function (){
            return "20200605";
        });
        $this->assertEquals(true,$rule->execute($param,$request));



    }
}