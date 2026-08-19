<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Between;
use EasySwoole\HttpAnnotation\Validator\Date;
use EasySwoole\HttpAnnotation\Validator\DateFormat;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    function testEqual()
    {
        $request = new Request();
        $request->withQueryParams([
            "date" => date("Y-m-d")
        ]);

        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Date('today');

        $v = new ValidateRequest($param);

        $this->assertEquals(true, $rule->execute( $v));


        $param = new Param(name:"date");
        $param->parsedValue($request);

        $rule = new Date('-1 day');
        $this->assertEquals(false, $rule->execute( $v));

        $rule = new DateFormat('Y-m-d');
        $this->assertEquals(true, $rule->execute( $v));

        $rule = new DateFormat('Ymd');
        $this->assertEquals(false, $rule->execute( $v));

        $rule = new DateFormat('Y-m-d h:i:s');
        $this->assertEquals(false, $rule->execute( $v));
    }
}