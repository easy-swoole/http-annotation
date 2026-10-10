<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;
use PHPUnit\Framework\TestCase;

class DefaultMapTest extends TestCase
{
    public function testConstructorsUseMapAndPreserveCustomMessages(): void
    {
        $default = new \EasySwoole\HttpAnnotation\Validator\AllDigital();
        $this->assertNull($default->errorMsgTpl());
        $this->assertNull($default->errorMsgTpl(null));
        $this->assertSame('value must contain only digits', $default->errorMsg('value', DefaultMap::class));
        $this->assertSame('value must contain only digits', $default->errorMsg('value'));
        $this->assertNull($default->errorMsgTpl());
        $custom = new \EasySwoole\HttpAnnotation\Validator\Min(min: 10, errorMsg: '{#validateParam}: minimum {#min}');
        $this->assertSame('{#validateParam}: minimum {#min}', $custom->errorMsgTpl());
        $this->assertSame('age: minimum 10', $custom->errorMsg('age'));
        $this->assertSame('age: minimum 10', $custom->errorMsg('age', DefaultMap::class));
        $file = new \EasySwoole\HttpAnnotation\Validator\IsFile(maxSize: 100, allowExt: ['png'], errorMsg: 'custom');
        $this->assertSame(100, $file->getRuleArgs()['maxSize']);
        $this->assertSame(['png'], $file->getRuleArgs()['allowExt']);
        $this->assertSame('custom', $file->errorMsg('file'));
    }

    public function testMappingMethodsArePublicStatic(): void
    {
        foreach (['getMsgTpl', 'getDefaultMsgTpl'] as $name) {
            $method = new \ReflectionMethod(DefaultMap::class, $name);
            $this->assertTrue($method->isStatic());
            $this->assertTrue($method->isPublic());
        }
    }

    public function testAllBuiltinValidatorsHaveMessageTemplates(): void
    {
        $names = [];
        foreach (glob(dirname(__DIR__, 2) . '/src/Validator/*.php') as $file) {
            $class = 'EasySwoole\\HttpAnnotation\\Validator\\' . basename($file, '.php');
            $name = $class::ruleName();
            $names[] = $name;
            $template = DefaultMap::getMsgTpl($name);
            $this->assertNotNull($template, $name);
            $this->assertStringContainsString('{#validateParam}', $template);
            $this->assertStringNotContainsString('{$', $template);
        }
        $this->assertCount(count($names), DefaultMap::MAP);
    }

    public function testUnknownRuleUsesFallbackAndKnownRulesKeepPlaceholders(): void
    {
        $this->assertNull(DefaultMap::getMsgTpl('UnknownRule'));
        $this->assertSame('{#validateParam} fail in validate UnknownRule rule', DefaultMap::getDefaultMsgTpl('UnknownRule'));
        $this->assertStringContainsString('{#paramName}', DefaultMap::getMsgTpl('BigThanColumn'));
        $this->assertStringContainsString('{#inVal}', DefaultMap::getMsgTpl('OptionalIfParamValInArray'));
        $this->assertStringContainsString('{#rule}', DefaultMap::getMsgTpl('Regex'));
    }
}
