<?php
namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\IsIp;
use EasySwoole\HttpAnnotation\Validator\IsUrl;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\TestCase;

class UrlIpOptionsTest extends TestCase
{
    public function testUrlProtocolWhitelist(): void
    {
        $rule = new IsUrl(allowProtocols: ['HTTP', 'https', 'http']);
        foreach (['http://example.com' => true, 'https://example.com/a' => true,
            'HTTP://example.com' => true, 'ftp://example.com' => false,
            '/relative' => false, 'https-not-a-url' => false] as $value => $expected) {
            $context = new ValidateRequest(new Param('url', type: null, value: $value));
            $this->assertSame($expected, $rule->execute($context));
        }
        $ftp = new ValidateRequest(new Param('url', type: null, value: 'ftp://example.com'));
        $this->assertTrue((new IsUrl())->execute($ftp));
        $this->assertFalse((new IsUrl(allowProtocols: []))->execute($ftp));
        $this->assertSame(['http', 'https'], $rule->getRuleArgs()['allowProtocols']);
        $this->assertStringContainsString('["http","https"]', $rule->errorMsg('url', ChineseMap::class));
        $this->assertSame('custom', (new IsUrl(['https'], 'custom'))->errorMsg('url'));
    }

    public function testIpModes(): void
    {
        foreach (['ANY' => [true, true], 'ipv4' => [true, false], 'IPv6' => [false, true]] as $mode => $expected) {
            $rule = new IsIp(mode: $mode);
            foreach (['127.0.0.1', '::1'] as $index => $value) {
                $this->assertSame($expected[$index], $rule->execute(new ValidateRequest(new Param('ip', type: null, value: $value))));
            }
            $this->assertFalse($rule->execute(new ValidateRequest(new Param('ip', type: null, value: 'invalid'))));
            $this->assertStringContainsString(strtoupper($mode), $rule->errorMsg('ip', ChineseMap::class));
        }
        $this->assertSame('custom', (new IsIp('IPV4', 'custom'))->errorMsg('ip'));
    }

    public function testInvalidConfigurationIsRejected(): void
    {
        foreach ([fn() => new IsUrl(allowProtocols: ['http://']), fn() => new IsUrl(allowProtocols: [1]),
            fn() => new IsIp(mode: 'IPV5')] as $construct) {
            try {
                $construct();
                $this->fail('Expected invalid configuration error');
            } catch (Annotation $error) {
                $this->assertNotEmpty($error->getMessage());
            }
        }
    }
}
