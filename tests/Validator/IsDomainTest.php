<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\IsDomain;
use PHPUnit\Framework\TestCase;

class IsDomainTest extends TestCase
{
    /*
    * 合法
    */
    public function testValidCase()
    {
        // 合法的 domain
        $domains = [
            'easyswoole.com',
            'easy--swoole-swoole.ch',
            'easy.xn--swoole-swoole.ch',
            'easy-swoole.com',
            'example--valid.com',
            'easy--swoole.com',
            'r--w.com'
        ];

        foreach ($domains as $domain) {
            $request = new Request();
            $request->withQueryParams([
                "domain" => $domain
            ]);

            $param = new Param(name: "domain");
            $param->parsedValue($request);

            $rule = new IsDomain();
            $this->assertEquals(true, $rule->execute($param, $request));
        }
    }

    /*
     * 默认错误信息
     */
    public function testDefaultErrorMsgCase()
    {
        // 不合法的 domain
        $domains = [
            null,
            new \stdClass(),
            '',
            'no dots',
            '2222222domain.local',
            '-example-invalid.com',
            'example.invalid.-com',
            'xn--bcher--kva.ch',
            'example.invalid-.com',
            '1.2.3.256',
            '1.2.3.4',
            'www,easyswoole,com',
            'easyswoole,com'
        ];
        foreach ($domains as $domain) {
            $request = new Request();
            $request->withQueryParams([
                "domain" => $domain
            ]);

            $param = new Param(name: "domain");
            $param->parsedValue($request);

            $rule = new IsDomain();
            $this->assertEquals(false, $rule->execute($param, $request));
            $this->assertEquals("domain must be a valid domain name format", $rule->errorMsg());
        }
    }

    /*
     * 自定义错误信息
     */
    public function testCustomErrorMsgCase()
    {
        $request = new Request();
        $request->withQueryParams([
            "domain" => 'this is str'
        ]);

        $param = new Param(name: "domain");
        $param->parsedValue($request);

        $rule = new IsDomain(errorMsg: '请输入合法的域名');
        $this->assertEquals(false, $rule->execute($param, $request));
        $this->assertEquals("请输入合法的域名", $rule->errorMsg());
    }
}
