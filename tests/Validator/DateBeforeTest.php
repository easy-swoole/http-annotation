<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\DateBefore;
use PHPUnit\Framework\TestCase;

class DateBeforeTest extends TestCase
{
    // 日期在此之前
    function testNormal()
    {
        $request = new Request();
        $request->withQueryParams([
            "date"=>"20220429"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateBefore("20220430");
        $this->assertEquals(true,$rule->execute($param,$request));



        $request = new Request();
        $request->withQueryParams([
            "date"=>"20220701"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateBefore("20220630");
        $this->assertEquals(false,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "date"=>"2022-06-06"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateBefore("20220630");
        $this->assertEquals(true,$rule->execute($param,$request));

        //字段必须是日期格式。因此传时间戳，失败
        $request = new Request();
        $request->withQueryParams([
            "date"=>"1654765394"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateBefore("20220530");
        $this->assertEquals(false,$rule->execute($param,$request));


        $request = new Request();
        $request->withQueryParams([
            "date"=>"20220630"
        ]);

        $param = new Param("date");
        $param->parsedValue($request);

        $rule = new DateBefore(date:function (){
            return "20220705";
        });
        $this->assertEquals(true,$rule->execute($param,$request));

    }
}
