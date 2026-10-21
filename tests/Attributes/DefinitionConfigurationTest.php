<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ExtendParam;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\Example\Array2Json;
use EasySwoole\HttpAnnotation\Bean\Example\ArrayForm;
use EasySwoole\HttpAnnotation\Bean\Example\Raw;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use PHPUnit\Framework\TestCase;
use stdClass;

class DefinitionConfigurationTest extends TestCase
{
    public function testInvalidExamplesAreRejected(): void
    {
        foreach (['requestExamples', 'responseExamples'] as $property) {
            foreach ([null, 'example', [], new stdClass()] as $value) {
                try {
                    new Api(...[$property => ['invalid' => $value]]);
                    self::fail('Invalid example must be rejected');
                } catch (Annotation $exception) {
                    self::assertStringContainsString($property . '[invalid]', $exception->getMessage());
                    self::assertStringContainsString('ExampleInterface', $exception->getMessage());
                    self::assertStringContainsString(get_debug_type($value), $exception->getMessage());
                }
            }
        }
    }

    public function testValidExamplesRemainSupported(): void
    {
        $examples = [new Array2Json(['code' => 200]), new ArrayForm(['page' => 1]), new Raw('text')];
        $api = new Api(requestExamples: $examples, responseExamples: $examples);
        self::assertSame($examples, $api->requestExamples);
        self::assertSame($examples, $api->responseExamples);
        self::assertSame([], (new Api())->requestExamples);
        self::assertSame([], (new Api())->responseExamples);
    }

    public function testDuplicatePublicParamsAreRejectedOnEveryAttempt(): void
    {
        $cache = new AttributeCache();
        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $cache->parseClass(DuplicateDefinitionController::class);
                self::fail('Duplicate parameter must be rejected');
            } catch (Annotation $exception) {
                self::assertStringContainsString('id', $exception->getMessage());
                self::assertStringContainsString('duplicate', $exception->getMessage());
                self::assertStringContainsString(DuplicateDefinitionController::class, $exception->getMessage());
                self::assertStringContainsString('onRequest', $exception->getMessage());
            }
        }
    }

    public function testInheritedAndActionOverridesRemainAllowed(): void
    {
        $info = (new AttributeCache())->parseClass(ChildDefinitionController::class);
        self::assertSame('child', $info->onRequest->onRequestParams['id']->value);
        self::assertSame('parent-only', $info->onRequest->onRequestParams['shared']->value);
        self::assertSame('action', $info->apiTag('collect')->requestParam['id']->value);
    }
}

class DuplicateDefinitionController extends AnnotationController
{
    #[Param('id')]
    #[Param('id')]
    public function onRequest(?string $action): ?bool
    {
        return true;
    }
}

class ParentDefinitionController extends AnnotationController
{
    #[Param('id', value: 'parent')]
    #[Param('shared', value: 'parent-only')]
    public function onRequest(?string $action): ?bool
    {
        return true;
    }
}

class ChildDefinitionController extends ParentDefinitionController
{
    #[ExtendParam]
    #[Param('id', value: 'child')]
    public function onRequest(?string $action): ?bool
    {
        return true;
    }

    #[Api(requestParam: [new Param('id', value: 'action')])]
    public function collect(array $data): void
    {
    }
}
