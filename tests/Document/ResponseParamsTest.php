<?php

namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Bean\Description\Text;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Bean\Example\ResParamType;
use EasySwoole\HttpAnnotation\Document\Document;
use PHPUnit\Framework\TestCase;

class ResponseParamsTest extends TestCase
{
    private function document(array $params): Document
    {
        return new class(__FILE__, $params) extends Document {
            public function __construct(string $path, private array $params)
            {
                parent::__construct($path, __NAMESPACE__);
            }

            public function scanAllApiGroup(): array
            {
                $info = new ClassAttribute();
                $api = new Api(responseParam: $this->params);
                $api->relateClass = __NAMESPACE__ . '\\Example';
                $api->relateMethod = 'detail';
                $api->apiName = 'detail';
                $info->apis['detail'] = $api;
                return ['Example' => ['apiGroup' => new ApiGroup('Example'), 'classAttribute' => $info]];
            }
        };
    }

    public function testResponseFieldsPreserveOrderValuesTypesAndDescriptions(): void
    {
        $document = $this->document([
            new ResParam('code', 200, ResParamType::int, '响应码'),
            new ResParam('result', null, ResParamType::object),
            new ResParam('result.user.id', 0, ResParamType::int, new Text('用户 ID')),
            new ResParam('enabled', false, ResParamType::bool),
            new ResParam('msg', '', ResParamType::string),
            new ResParam('score', 1.25, ResParamType::float),
            new ResParam('result.items', [['id' => 1]], ResParamType::array, '列表'),
            new ResParam('result.items[].id', 1, ResParamType::int, '列表元素 ID'),
        ]);
        $params = $document->scan2ArrayMap()['Example']['apiList']['detail']['responseParams'];
        $this->assertSame(['code', 'result', 'result.user.id', 'enabled', 'msg', 'score', 'result.items', 'result.items[].id'], array_column($params, 'name'));
        $this->assertSame([200, null, 0, false, '', 1.25, [['id' => 1]], 1], array_column($params, 'exampleValue'));
        $this->assertSame(['int', 'object', 'int', 'bool', 'string', 'float', 'array', 'int'], array_column($params, 'type'));
        $this->assertSame('用户 ID', $params[2]['description']);
        $this->assertNull($params[1]['description']);
        $this->assertStringContainsString('result.user.id', $document->scan2html());
    }

    public function testEmptyDefinitions(): void
    {
        $this->assertSame([], $this->document([])->scan2ArrayMap()['Example']['apiList']['detail']['responseParams']);
    }
}
