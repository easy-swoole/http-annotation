<?php

namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Validator\Each;
use EasySwoole\HttpAnnotation\Validator\ObjectShape;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\TestCase;

class NestedParamsTest extends TestCase
{
    public function testDocumentPreservesRootParametersAndIncludesNestedMetadata(): void
    {
        $document = new class(__FILE__, __NAMESPACE__) extends Document {
            public function scanAllApiGroup(): array
            {
                $info = new ClassAttribute();
                $api = new Api(allowMethod: HttpMethod::POST, acceptContentType: ContentType::JSON, requestParam: [
                    new Param('boxes', from: [ParamFrom::JSON], type: null, validate: [new Each([new ObjectShape([
                        new Param('id', type: ParamType::INT, deprecated: true, description: '箱号', value: 0, validate: [new Required()]),
                        new Param('detail', type: null, validate: [new ObjectShape([new Param('name')])]),
                    ])])]),
                    new Param('flat', from: [ParamFrom::JSON]),
                ]);
                $api->relateClass = __NAMESPACE__ . '\\Nested';
                $api->apiName = 'create';
                $info->apis['create'] = $api;
                return ['Nested' => ['apiGroup' => new ApiGroup('Nested'), 'classAttribute' => $info]];
            }
        };
        $document->getConfig()->setValidateMsgMap(ChineseMap::class);
        $params = $document->scan2ArrayMap()['Nested']['apiList']['create']['requestParams'];
        self::assertSame(['boxes', 'flat'], array_keys($params));
        self::assertSame('array', $params['boxes']['type']);
        self::assertSame('object', $params['boxes']['items']['type']);
        $id = $params['boxes']['items']['children']['id'];
        self::assertSame(['JSON'], $id['from']);
        self::assertSame(0, $id['defaultValue']);
        self::assertTrue($id['deprecated']);
        self::assertSame('箱号', $id['description']);
        self::assertSame('INT', $id['type']);
        self::assertStringContainsString('必须传入', $id['validateRules']['Required']['msg']);
        self::assertSame([], $params['boxes']['validateRules']);
        self::assertArrayHasKey('name', $params['boxes']['items']['children']['detail']['children']);
        self::assertStringContainsString('箱号', $document->scan2html());
        self::assertArrayNotHasKey('children', $params['flat']);
    }
}
