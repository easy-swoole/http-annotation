<?php

namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Document\Document;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DocumentPathTest extends TestCase
{
    #[DataProvider('paths')]
    public function testPathGenerationDoesNotMutateSharedApi(string $class, string $action, string $path): void
    {
        $api = new Api();
        $api->relateClass = $class;
        $api->relateMethod = $action;
        $api->apiName = $action;
        $definition = get_object_vars($api);
        $document = new class(__FILE__, $api) extends Document {
            public function __construct(string $file, private Api $api)
            {
                parent::__construct($file, 'App\\HttpController');
            }

            public function scanAllApiGroup(): array
            {
                $info = new ClassAttribute();
                $info->apis[$this->api->apiName] = $this->api;
                return ['Example' => ['apiGroup' => new ApiGroup('Example'), 'classAttribute' => $info]];
            }
        };
        for ($i = 0; $i < 2; $i++) {
            $map = $document->scan2ArrayMap();
            $this->assertSame($path, $map['Example']['apiList'][$action]['requestPath']);
            $this->assertSame($definition, get_object_vars($api));
            $document->scan2html();
            $this->assertSame($definition, get_object_vars($api));
        }
    }

    public static function paths(): array
    {
        return [
            ['App\\HttpController\\Common\\Message', 'list', '/common/message/list'],
            ['App\\HttpController\\Common\\Index', 'list', '/common/list'],
            ['App\\HttpController\\Common\\Message', 'index', '/common/message/'],
            ['App\\HttpController\\Index', 'index', '/'],
            ['App\\HttpController\\Admin\\UserProfile', 'Detail', '/admin/userProfile/Detail'],
        ];
    }
}
