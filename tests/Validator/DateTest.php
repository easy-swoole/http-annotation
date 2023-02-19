<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Between;
use EasySwoole\HttpAnnotation\Validator\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    function testEqual()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => \date("Y-m-d")
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Date('today');
        $this->assertEquals(true, $rule->execute($param, $request));


        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Date('-1 day');
        $this->assertEquals(false, $rule->execute($param, $request));

        var_dump($rule->errorMsg());
    }
}