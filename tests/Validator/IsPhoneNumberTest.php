<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\IsPhoneNumber;
use EasySwoole\HttpAnnotation\Validator\Length;
use PHPUnit\Framework\TestCase;

class IsPhoneNumberTest extends TestCase
{
    function testPhone()
    {
        $request = new Request();
        $request->withQueryParams([
            "phone" => 12345
        ]);

        $param = new Param(name:"phone");
        $param->parsedValue($request);

        $rule = new IsPhoneNumber();
        $this->assertEquals(false, $rule->execute($param, $request));


        $request = new Request();
        $request->withQueryParams([
            "phone" => 15505920001
        ]);

        $param = new Param(name:"phone");
        $param->parsedValue($request);

        $rule = new IsPhoneNumber();
        $this->assertEquals(true, $rule->execute($param, $request));
    }
}