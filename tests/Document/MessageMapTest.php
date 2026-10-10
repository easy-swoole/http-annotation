<?php
namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Document\Config;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateMsgMapInterface;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;
use PHPUnit\Framework\TestCase;

class MessageMapTest extends TestCase
{
    public function testDocumentUsesConfiguredMapWithoutOverridingCustomMessages(): void
    {
        $document = new class(__FILE__) extends Document {
            private ?array $groups = null;
            public function scanAllApiGroup(): array
            {
                if ($this->groups !== null) return $this->groups;
                $info = new ClassAttribute();
                $group = new ApiGroup(groupName: 'Example');
                $info->onRequest->onRequestParams['shared'] = new Param('shared', validate: [new Required()]);
                $api = new Api(requestParam: [
                    new Param('ticket', validate: [new Required()]),
                    new Param('custom', validate: [new Required(errorMsg: '自定义 {#validateParam}')]),
                ]);
                $api->relateClass = self::class;
                $api->relateMethod = 'detail';
                $api->apiName = 'detail';
                $api->requestPath = '/detail';
                $info->apis['detail'] = $api;
                return $this->groups = ['Example' => ['apiGroup' => $group, 'classAttribute' => $info]];
            }
        };
        foreach ([[DefaultMap::class, 'ticket is required'], [ChineseMap::class, 'ticket 必须传入'],
            [CustomDocumentMap::class, 'ticket 自定义映射'], [DefaultMap::class, 'ticket is required']] as [$class, $expected]) {
            $document->getConfig()->setValidateMsgMap($class);
            $map = $document->scan2ArrayMap()['Example'];
            $this->assertSame($expected, $map['apiList']['detail']['requestParams']['ticket']['validateRules']['Required']['msg']);
            $this->assertSame('自定义 custom', $map['apiList']['detail']['requestParams']['custom']['validateRules']['Required']['msg']);
            $this->assertSame(str_replace('ticket', 'shared', $expected), $map['onRequestParams']['shared']['validateRules']['Required']['msg']);
            $this->assertStringContainsString($expected, $document->scan2html());
        }
    }

    public function testConfigRejectsClassesWithoutMappingInterface(): void
    {
        $config = new Config();
        $this->assertSame(DefaultMap::class, $config->getValidateMsgMap());
        $this->expectException(Annotation::class);
        $config->setValidateMsgMap(\stdClass::class);
    }
}

class CustomDocumentMap implements ValidateMsgMapInterface
{
    public static function getMsgTpl(string $validateName): ?string { return '{#validateParam} 自定义映射'; }
    public static function getDefaultMsgTpl(string $validateName): string { return '{#validateParam} 校验失败'; }
}
