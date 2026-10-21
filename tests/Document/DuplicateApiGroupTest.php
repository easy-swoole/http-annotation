<?php

namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Document\Document;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Tests\Document\Fixtures\DuplicateGroup\FirstController;
use EasySwoole\HttpAnnotation\Tests\Document\Fixtures\DuplicateGroup\SecondController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DuplicateApiGroupTest extends TestCase
{
    #[DataProvider('scanMethods')]
    public function testDuplicateGroupErrorIdentifiesBothControllers(string $method): void
    {
        $document = new Document(__DIR__ . '/Fixtures/DuplicateGroup');
        try {
            $document->$method();
            $this->fail('Expected duplicate ApiGroup to be rejected');
        } catch (Annotation $error) {
            $this->assertStringContainsString('apiGroupName Duplicate.Example is already defined in ', $error->getMessage());
            $this->assertStringContainsString(FirstController::class, $error->getMessage());
            $this->assertStringContainsString(SecondController::class, $error->getMessage());
            $this->assertStringContainsString(',redefine in ', $error->getMessage());
        }
    }

    public static function scanMethods(): array
    {
        return [
            'group scan' => ['scanAllApiGroup'],
            'array generation' => ['scan2ArrayMap'],
            'HTML generation' => ['scan2html'],
        ];
    }
}
