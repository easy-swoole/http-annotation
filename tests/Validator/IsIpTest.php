<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\Validator\IsIp;
use PHPUnit\Framework\TestCase;

class IsIpTest extends TestCase
{
    //
    function testNormal() {
        $request = new Request();
        $request->withQueryParams([
            "ip" => '192.0.0.1'
        ]);

        $param = new Param("ip");
        $param->parsedValue($request);

        $rule = new IsIp();
        $this->assertEquals(true, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "ip" => 'this is str'
        ]);

        $param = new Param("ip");
        $param->parsedValue($request);

        $rule = new IsIp();
        $this->assertEquals(false, $rule->execute($param, $request));

        $request = new Request();
        $request->withQueryParams([
            "ip" => '333.333.333.333'
        ]);

        $param = new Param("ip");
        $param->parsedValue($request);

        $rule = new IsIp(errorMsg: '测试提示');
        $this->assertEquals(false, $rule->execute($param, $request));

        $rule->currentCheckParam($param);

        $this->assertEquals("测试提示",$rule->errorMsg());
    }
}
