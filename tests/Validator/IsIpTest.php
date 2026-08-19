<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\IsIp;
use PHPUnit\Framework\TestCase;

class IsIpTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 合法的IPv4
        $request = new Request();
        $request->withQueryParams([
            "ip" => '192.0.0.1'
        ]);

        $param = new Param(name:"ip");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new IsIp();
        $this->assertEquals(true, $rule->execute( $request));

        // 合法的IPv6
        $request = new Request();
        $request->withQueryParams([
            "ip" => '2001:0db8:85a3:08d3:1319:8a2e:0370:7334'
        ]);

        $param = new Param(name:"ip");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new IsIp();
        $this->assertEquals(true, $rule->execute( $request));
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 不是IP
        $request = new Request();
        $request->withQueryParams([
            "ip" => 'https://wwww.easyswoole.com'
        ]);

        $param = new Param(name:"ip");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new IsIp();
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("ip must be a ip format",$rule->errorMsg($request->validateParam->name));

        // 范围不合法
        $request = new Request();
        $request->withQueryParams([
            "ip" => '256.256.256.256'
        ]);

        $param = new Param(name:"ip");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new IsIp();
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("ip must be a ip format",$rule->errorMsg($request->validateParam->name));
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "ip" => 'this is str'
        ]);

        $param = new Param(name:"ip");
        $param->parsedValue($request);
        $request = new ValidateRequest($param);
        $rule = new IsIp(errorMsg: '请输入合法的IP地址');
        $this->assertEquals(false, $rule->execute( $request));
        $this->assertEquals("请输入合法的IP地址",$rule->errorMsg($request->validateParam->name));
    }
}
