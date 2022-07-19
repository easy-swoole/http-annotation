<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\IsBool;
use PHPUnit\Framework\TestCase;

class IsBoolTest extends TestCase
{
//
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "bool" => true
        ]);

        $param = new Param("bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "bool" => false
        ]);

        $param = new Param("bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));
        
        $request = new Request();
        $request->withQueryParams([
            "bool" => 2
        ]);

        $param = new Param("bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "bool" => 1
        ]);

        $param = new Param("bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "bool" => "0"
        ]);

        $param = new Param("bool");
        $param->parsedValue($request);

        $rule = new IsBool();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "bool" => 2
        ]);

        $param = new Param("bool");
        $param->parsedValue($request);

        $rule = new IsBool(errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
