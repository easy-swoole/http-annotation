<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Min;
use EasySwoole\HttpAnnotation\Validator\Required;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ValidatorConfigurationTest extends TestCase
{
    #[DataProvider('invalidRules')]
    public function testInvalidRuleHasUsefulConfigurationError(mixed $rule): void
    {
        try {
            new Param('age', validate: [new Required(), 'invalid' => $rule]);
            $this->fail('Expected invalid rule to be rejected');
        } catch (Annotation $error) {
            foreach (['age', 'validate[invalid]', 'AbstractValidator', get_debug_type($rule)] as $text) {
                $this->assertStringContainsString($text, $error->getMessage());
            }
        }
    }

    public static function invalidRules(): array
    {
        return [[null], [false], [1], ['Required'], [Required::class], [[]], [new stdClass()],
            [static fn() => true], [new FakeNamedRule()]];
    }

    public function testValidInstancesKeepNormalizationAndDuplicateBehavior(): void
    {
        $first = new Min(1);
        $last = new Min(2);
        $custom = new CustomConfigurationRule();
        $param = new Param('age', validate: [$first, 'custom' => $custom, $last]);
        $this->assertSame(['Min', 'CustomConfigurationRule'], array_keys($param->validate));
        $this->assertSame($last, $param->validate['Min']);
        $this->assertSame($custom, $param->validate['CustomConfigurationRule']);
        $this->assertSame([], (new Param('empty'))->validate);
    }

    #[DataProvider('invalidControllers')]
    public function testAttributeScanAddsControllerLocation(string $class, string $method): void
    {
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage($class . ' method ' . $method);
        AttributeCache::getInstance()->parseClass($class);
    }

    public static function invalidControllers(): array
    {
        return [[InvalidActionValidatorController::class, 'collect'], [InvalidPublicValidatorController::class, 'onRequest']];
    }
}

class FakeNamedRule
{
    public static function ruleName(): string { return 'fake'; }
}

class CustomConfigurationRule extends AbstractValidator
{
    protected function validate(ValidateRequest $validateRequest): bool { return true; }
    public static function ruleName(): string { return 'CustomConfigurationRule'; }
}

class InvalidActionValidatorController extends AnnotationController
{
    #[Api(requestParam: [new Param('age', validate: [null])])]
    public function collect(array $data): void {}
}

class InvalidPublicValidatorController extends AnnotationController
{
    #[Param('age', validate: [new stdClass()])]
    public function onRequest(?string $action): ?bool { return true; }
}
