<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\Example\ResParam;
use EasySwoole\HttpAnnotation\Bean\Example\ResParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ResponseParamConfigurationTest extends TestCase
{
    #[DataProvider('invalidDefinitions')]
    public function testInvalidDefinitionIsRejectedByApiConstructor(array $params, string $message): void
    {
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage($message);
        new Api(responseParam: $params);
    }

    public static function invalidDefinitions(): array
    {
        return [
            [[null], 'responseParam[0] must be a ResParam instance'],
            [['invalid'], 'string given'],
            [[new stdClass()], 'stdClass given'],
            [[new Param('legacy')], 'must be a ResParam instance'],
            [[new ResParam('', null)], 'name must not be empty'],
            [[new ResParam(" \t\n", null)], 'name must not be empty'],
            [[new ResParam('code', 200), new ResParam('code', 400)], 'responseParam code define duplicate'],
            [[new ResParam('result.id', 1), new ResParam('result.id', 2)], 'responseParam result.id define duplicate'],
        ];
    }

    public function testNamesAreCaseSensitiveAndDeclarationOrderIsPreserved(): void
    {
        $params = [
            'first' => new ResParam('Code', 200, ResParamType::int),
            'second' => new ResParam('code', 200, ResParamType::int),
            'third' => new ResParam('result.items', [], ResParamType::array),
        ];
        $this->assertSame($params, (new Api(responseParam: $params))->responseParam);
        $this->assertSame([], (new Api())->responseParam);
    }

    public function testScanErrorIdentifiesControllerAndAction(): void
    {
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage(InvalidResponseParamController::class . ' method detail');
        AttributeCache::getInstance()->parseClass(InvalidResponseParamController::class);
    }
}

class InvalidResponseParamController extends AnnotationController
{
    #[Api(responseParam: [new Param('legacy')])]
    public function detail(): void {}
}
