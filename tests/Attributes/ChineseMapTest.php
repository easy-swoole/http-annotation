<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;
use EasySwoole\HttpAnnotation\Validator\Min;
use PHPUnit\Framework\TestCase;

class ChineseMapTest extends TestCase
{
    public function testKeysAndPlaceholdersMatchDefaultMap(): void
    {
        $this->assertSame(array_keys(DefaultMap::MAP), array_keys(ChineseMap::MAP));
        foreach (DefaultMap::MAP as $name => $template) {
            preg_match_all('/\{#[^}]+\}/', $template, $expected);
            preg_match_all('/\{#[^}]+\}/', ChineseMap::getMsgTpl($name), $actual);
            $this->assertSame($expected[0], $actual[0], $name);
        }
    }

    public function testEveryMessageSeparatesParameterNameFromText(): void
    {
        foreach (ChineseMap::MAP as $name => $template) {
            $this->assertStringStartsWith('{#validateParam} ', $template, $name);
        }
        $this->assertStringStartsWith('{#validateParam} ', ChineseMap::getDefaultMsgTpl('Unknown'));
    }

    public function testChineseRenderingCustomPriorityAndFallback(): void
    {
        $this->assertSame('年龄 不能小于 18', (new Min(18))->errorMsg('年龄', ChineseMap::class));
        $this->assertSame('自定义', (new Min(18, '自定义'))->errorMsg('年龄', ChineseMap::class));
        $this->assertNull(ChineseMap::getMsgTpl('Unknown'));
        $this->assertSame('{#validateParam} 未通过 Unknown 规则校验', ChineseMap::getDefaultMsgTpl('Unknown'));
    }
}
