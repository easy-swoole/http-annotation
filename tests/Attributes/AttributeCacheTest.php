<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use PHPUnit\Framework\TestCase;

class AttributeCacheTest extends TestCase
{
    public function testConflictReportsContextAndNeverCachesPartialResult(): void
    {
        $cache = new AttributeCache();
        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $cache->parseClass(ConflictingController::class);
                $this->fail('Expected an incompatible onRequest parameter to be rejected');
            } catch (Annotation $error) {
                foreach ([ConflictingController::class, 'index', 'ticket', 'GET', 'POST'] as $text) {
                    $this->assertStringContainsString($text, $error->getMessage());
                }
            }
        }
    }

    public function testGetSourceAndActionOverridesAreAllowed(): void
    {
        $this->assertArrayHasKey('index', (new AttributeCache())->parseClass(CompatibleController::class)->apis);
        $this->assertArrayHasKey('index', (new AttributeCache())->parseClass(OverrideController::class)->apis);
        $this->assertArrayHasKey('index', (new AttributeCache())->parseClass(IgnoredController::class)->apis);
    }
}

class ConflictingController extends AnnotationController
{
    #[Param('ticket', from: [ParamFrom::POST])]
    public function onRequest(?string $action): ?bool { return true; }
    #[Api]
    public function index() {}
}
class CompatibleController extends AnnotationController
{
    #[Param('ticket', from: [ParamFrom::GET, ParamFrom::POST])]
    public function onRequest(?string $action): ?bool { return true; }
    #[Api]
    public function index() {}
}
class OverrideController extends ConflictingController
{
    #[Api(requestParam: [new Param('ticket', from: ParamFrom::GET)])]
    public function index() {}
}
class IgnoredController extends AnnotationController
{
    #[Param('ticket', from: ParamFrom::POST, ignoreAction: ['index'])]
    public function onRequest(?string $action): ?bool { return true; }
    #[Api]
    public function index() {}
}
