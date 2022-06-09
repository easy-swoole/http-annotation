<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\ActiveUrl;
use PHPUnit\Framework\TestCase;

class ActiveUrlTest extends TestCase
{
    function testUrl(){
        $request = new Request();
        $request->withQueryParams([
            "url"=>"https://www.baidu.com"
        ]);

        $param = new Param("url");
        $param->parsedValue($request);

        $rule = new ActiveUrl();
        $this->assertEquals(true,$rule->execute($param,$request));
    }

    function testErrorUrl(){
        $request = new Request();
        $request->withQueryParams([
            "url"=>"this is a str"
        ]);

        $param = new Param("url");
        $param->parsedValue($request);

        $rule = new ActiveUrl();
        $this->assertEquals(false,$rule->execute($param,$request));
    }


    function testFakeDnsUrl(){
        $request = new Request();
        $request->withQueryParams([
            "url"=>"https://www.noneDnsAnswerDomain.com"
        ]);

        $param = new Param("url");
        $param->parsedValue($request);

        $rule = new ActiveUrl();
        $this->assertEquals(false,$rule->execute($param,$request));
    }


}