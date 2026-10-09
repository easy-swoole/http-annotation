<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\EqualWithColumn;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\Required;
use PHPUnit\Framework\Attributes\DataProvider;
use EasySwoole\HttpAnnotation\Validator\BigThanColumn;
use EasySwoole\HttpAnnotation\Validator\DateAfterColumn;
use EasySwoole\HttpAnnotation\Validator\DateBeforeColumn;
use EasySwoole\HttpAnnotation\Validator\DifferentWithColumn;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamMiss;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValInArray;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValNoInArray;
use EasySwoole\HttpAnnotation\Validator\SmallThanColumn;
use PHPUnit\Framework\TestCase;

class ControllerValidationTest extends TestCase
{
    #[DataProvider('optionalControllerCases')]
    public function testConditionalRulesThroughBothControllerStages(string $class, array $input, bool $valid): void
    {
        $request = new Request();
        $request->withMethod('GET');
        $request->withQueryParams($input);
        $controller = new $class($request, new Response(), 'collect');
        if ($valid) {
            $controller->__hook();
            $this->assertTrue($controller->called);
        } else {
            try {
                $controller->__hook();
                $this->fail('Expected conditional validation failure');
            } catch (ParamValidateFail $error) {
                $this->assertSame('value', $error->getParamName());
                $this->assertInstanceOf(Required::class, $error->getFailRule());
                $this->assertFalse($controller->called);
            }
        }
    }

    public static function optionalControllerCases(): array
    {
        return [
            [ActionOptionalIfParamMissFixture::class, [], true],
            [ActionOptionalIfParamMissFixture::class, ['target' => 'guest'], false],
            [ActionOptionalIfParamMissFixture::class, ['target' => 'guest', 'value' => 'provided'], true],
            [PublicOptionalIfParamMissFixture::class, [], true],
            [PublicOptionalIfParamMissFixture::class, ['target' => 'guest'], false],
            [PublicOptionalIfParamMissFixture::class, ['target' => 'guest', 'value' => 'provided'], true],
            [ActionOptionalIfParamSetFixture::class, [], false],
            [ActionOptionalIfParamSetFixture::class, ['target' => 'guest'], true],
            [ActionOptionalIfParamSetFixture::class, ['value' => 'provided'], true],
            [PublicOptionalIfParamSetFixture::class, [], false],
            [PublicOptionalIfParamSetFixture::class, ['target' => 'guest'], true],
            [PublicOptionalIfParamSetFixture::class, ['value' => 'provided'], true],
            [ActionOptionalIfParamValInArrayFixture::class, ['target' => 'guest'], true],
            [ActionOptionalIfParamValInArrayFixture::class, ['target' => 'member'], false],
            [ActionOptionalIfParamValInArrayFixture::class, [], false],
            [ActionOptionalIfParamValInArrayFixture::class, ['value' => 'provided'], true],
            [PublicOptionalIfParamValInArrayFixture::class, ['target' => 'guest'], true],
            [PublicOptionalIfParamValInArrayFixture::class, ['target' => 'member'], false],
            [PublicOptionalIfParamValInArrayFixture::class, [], false],
            [PublicOptionalIfParamValInArrayFixture::class, ['value' => 'provided'], true],
            [ActionOptionalIfParamValNoInArrayFixture::class, ['target' => 'guest'], false],
            [ActionOptionalIfParamValNoInArrayFixture::class, ['target' => 'member'], true],
            [ActionOptionalIfParamValNoInArrayFixture::class, [], true],
            [ActionOptionalIfParamValNoInArrayFixture::class, ['target' => 'guest', 'value' => 'provided'], true],
            [PublicOptionalIfParamValNoInArrayFixture::class, ['target' => 'guest'], false],
            [PublicOptionalIfParamValNoInArrayFixture::class, ['target' => 'member'], true],
            [PublicOptionalIfParamValNoInArrayFixture::class, [], true],
            [PublicOptionalIfParamValNoInArrayFixture::class, ['target' => 'guest', 'value' => 'provided'], true],
        ];
    }

    #[DataProvider('columnCases')]
    public function testColumnRulesThroughController(string $class, string $rule, mixed $target, mixed $value, bool $valid): void
    {
        $request = new Request();
        $request->withMethod('GET');
        $request->withQueryParams(['target' => $target, 'value' => $value]);
        $controller = new $class($request, new Response(), 'collect');
        if ($valid) {
            $controller->__hook();
            $this->assertTrue($controller->called);
        } else {
            try {
                $controller->__hook();
                $this->fail('Expected column rule failure');
            } catch (ParamValidateFail $error) {
                $this->assertSame('value', $error->getParamName());
                $this->assertSame($rule, $error->getFailRule()->ruleName());
                $this->assertFalse($controller->called);
            }
        }
    }

    public function testUndefinedComparisonTargetsReportDefinitionErrors(): void
    {
        foreach ([MissingBigThanColumnController::class, MissingDateAfterColumnController::class,
            MissingDateBeforeColumnController::class, MissingDifferentWithColumnController::class,
            MissingEqualWithColumnController::class] as $class) {
            $request = new Request();
            $request->withMethod('GET');
            $request->withQueryParams(['value' => '2026-01-01']);
            $controller = new $class($request, new Response(), 'collect');
            try {
                $controller->__hook();
                $this->fail('Expected undefined target error');
            } catch (\EasySwoole\HttpAnnotation\Exception\Annotation $error) {
                $this->assertStringContainsString('target', $error->getMessage());
                $this->assertStringContainsString('not define', $error->getMessage());
                $this->assertFalse($controller->called);
            }
        }
    }

    public static function columnCases(): array
    {
        return [
            [ActionSmallThanColumnController::class, 'SmallThanColumn', 10, 9, true],
            [ActionSmallThanColumnController::class, 'SmallThanColumn', 10, 10, false],
            [ActionSmallThanColumnController::class, 'SmallThanColumn', 10, 11, false],
            [PublicSmallThanColumnController::class, 'SmallThanColumn', 10, 9, true],
            [PublicSmallThanColumnController::class, 'SmallThanColumn', 10, 10, false],
            [PublicSmallThanColumnController::class, 'SmallThanColumn', 10, 11, false],
            [ActionBigThanColumnController::class, 'BigThanColumn', 10, 11, true],
            [ActionBigThanColumnController::class, 'BigThanColumn', 10, 10, false],
            [ActionBigThanColumnController::class, 'BigThanColumn', 10, 9, false],
            [PublicBigThanColumnController::class, 'BigThanColumn', 10, 11, true],
            [PublicBigThanColumnController::class, 'BigThanColumn', 10, 10, false],
            [PublicBigThanColumnController::class, 'BigThanColumn', 10, 9, false],
            [ActionDateAfterColumnController::class, 'DateAfterColumn', '2026-01-01', '2026-01-02', true],
            [ActionDateAfterColumnController::class, 'DateAfterColumn', '2026-01-01', '2026-01-01', false],
            [ActionDateAfterColumnController::class, 'DateAfterColumn', '2026-01-01', 'invalid', false],
            [PublicDateAfterColumnController::class, 'DateAfterColumn', '2026-01-01', '2026-01-02', true],
            [PublicDateAfterColumnController::class, 'DateAfterColumn', '2026-01-01', '2026-01-01', false],
            [PublicDateAfterColumnController::class, 'DateAfterColumn', '2026-01-01', 'invalid', false],
            [ActionDateBeforeColumnController::class, 'DateBeforeColumn', '2026-01-02', '2026-01-01', true],
            [ActionDateBeforeColumnController::class, 'DateBeforeColumn', '2026-01-02', '2026-01-02', false],
            [ActionDateBeforeColumnController::class, 'DateBeforeColumn', '2026-01-02', 'invalid', false],
            [PublicDateBeforeColumnController::class, 'DateBeforeColumn', '2026-01-02', '2026-01-01', true],
            [PublicDateBeforeColumnController::class, 'DateBeforeColumn', '2026-01-02', '2026-01-02', false],
            [PublicDateBeforeColumnController::class, 'DateBeforeColumn', '2026-01-02', 'invalid', false],
            [ActionDifferentWithColumnController::class, 'DifferentWithColumn', 'a', 'b', true],
            [ActionDifferentWithColumnController::class, 'DifferentWithColumn', 'a', 'a', false],
            [ActionDifferentWithColumnController::class, 'DifferentWithColumn', 0, '0', true],
            [PublicDifferentWithColumnController::class, 'DifferentWithColumn', 'a', 'b', true],
            [PublicDifferentWithColumnController::class, 'DifferentWithColumn', 'a', 'a', false],
            [PublicDifferentWithColumnController::class, 'DifferentWithColumn', 0, '0', true],
            [ActionEqualWithColumnController::class, 'EqualWithColumn', 'a', 'a', true],
            [ActionEqualWithColumnController::class, 'EqualWithColumn', 'a', 'b', false],
            [ActionEqualWithColumnController::class, 'EqualWithColumn', 0, '0', false],
            [PublicEqualWithColumnController::class, 'EqualWithColumn', 'a', 'a', true],
            [PublicEqualWithColumnController::class, 'EqualWithColumn', 'a', 'b', false],
            [PublicEqualWithColumnController::class, 'EqualWithColumn', 0, '0', false],
        ];
    }

    #[DataProvider('comparisonCases')]
    public function testBothValidationStagesReceiveParsedParameters(string $class, bool $valid): void
    {
        $request = new Request();
        $request->withMethod('GET');
        $request->withQueryParams(['password' => 'secret', 'confirmation' => $valid ? 'secret' : 'wrong']);
        $controller = new $class($request, new Response(), 'collect');
        if (!$valid) {
            try {
                $controller->__hook();
                $this->fail('Expected cross-field validation to fail');
            } catch (ParamValidateFail $error) {
                $this->assertSame('confirmation', $error->getParamName());
                $this->assertInstanceOf(EqualWithColumn::class, $error->getFailRule());
                $this->assertStringContainsString($class, $error->getMessage());
                $this->assertFalse($controller->called);
            }
        } else {
            $controller->__hook();
            $this->assertTrue($controller->called);
        }
    }

    public static function comparisonCases(): array
    {
        return [
            [ActionComparisonController::class, true],
            [ActionComparisonController::class, false],
            [OnRequestComparisonController::class, true],
            [OnRequestComparisonController::class, false],
        ];
    }

    public function testConditionalOptionalReadsOnRequestParameter(): void
    {
        foreach ([true, false] as $hasAccount) {
            $request = new Request();
            $request->withMethod('GET');
            $request->withQueryParams($hasAccount ? ['account' => 'guest'] : []);
            $controller = new ConditionalOptionalController($request, new Response(), 'collect');
            if ($hasAccount) {
                $controller->__hook();
                $this->assertTrue($controller->called);
            } else {
                try {
                    $controller->__hook();
                    $this->fail('Required must execute when the target is missing');
                } catch (ParamValidateFail $error) {
                    $this->assertSame('ticket', $error->getParamName());
                    $this->assertInstanceOf(Required::class, $error->getFailRule());
                    $this->assertFalse($controller->called);
                }
            }
        }
    }
}

class ActionComparisonController extends AnnotationController
{
    public bool $called = false;

    #[Param('password', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('confirmation', type: null, validate: [new EqualWithColumn(compare: 'password', strict: true)])])]
    public function collect(array $data): void { $this->called = true; }
}

class OnRequestComparisonController extends AnnotationController
{
    public bool $called = false;

    #[Param('confirmation', type: null, validate: [new EqualWithColumn(compare: 'password', strict: true)])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('password', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ConditionalOptionalController extends AnnotationController
{
    public bool $called = false;

    #[Param('account', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('ticket', type: null, validate: [new OptionalIfParamSet(paramName: 'account'), new Required()])])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionBigThanColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new BigThanColumn(paramName: 'target')])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicBigThanColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new BigThanColumn(paramName: 'target')])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionDateAfterColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new DateAfterColumn(compare: 'target')])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicDateAfterColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new DateAfterColumn(compare: 'target')])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionDateBeforeColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new DateBeforeColumn(compare: 'target')])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicDateBeforeColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new DateBeforeColumn(compare: 'target')])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionDifferentWithColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new DifferentWithColumn(compare: 'target', strict: true)])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicDifferentWithColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new DifferentWithColumn(compare: 'target', strict: true)])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionEqualWithColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new EqualWithColumn(compare: 'target', strict: true)])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicEqualWithColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new EqualWithColumn(compare: 'target', strict: true)])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class MissingBigThanColumnController extends ActionBigThanColumnController
{
    public function onRequest(?string $action): ?bool { return true; }
}

class MissingDateAfterColumnController extends ActionDateAfterColumnController
{
    public function onRequest(?string $action): ?bool { return true; }
}

class MissingDateBeforeColumnController extends ActionDateBeforeColumnController
{
    public function onRequest(?string $action): ?bool { return true; }
}

class MissingDifferentWithColumnController extends ActionDifferentWithColumnController
{
    public function onRequest(?string $action): ?bool { return true; }
}

class MissingEqualWithColumnController extends ActionEqualWithColumnController
{
    public function onRequest(?string $action): ?bool { return true; }
}

class ActionOptionalIfParamMissFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new OptionalIfParamMiss(paramName: 'target'), new Required()])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicOptionalIfParamMissFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new OptionalIfParamMiss(paramName: 'target'), new Required()])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionOptionalIfParamSetFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new OptionalIfParamSet(paramName: 'target'), new Required()])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicOptionalIfParamSetFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new OptionalIfParamSet(paramName: 'target'), new Required()])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionOptionalIfParamValInArrayFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new OptionalIfParamValInArray(paramName: 'target', inVal: ['guest']), new Required()])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicOptionalIfParamValInArrayFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new OptionalIfParamValInArray(paramName: 'target', inVal: ['guest']), new Required()])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionOptionalIfParamValNoInArrayFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new OptionalIfParamValNoInArray(paramName: 'target', inVal: ['guest']), new Required()])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicOptionalIfParamValNoInArrayFixture extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new OptionalIfParamValNoInArray(paramName: 'target', inVal: ['guest']), new Required()])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionSmallThanColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('target', type: null)]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('value', type: null, validate: [new SmallThanColumn(paramName: 'target')])])]
    public function collect(array $data): void { $this->called = true; }
}

class PublicSmallThanColumnController extends AnnotationController
{
    public bool $called = false;

    #[Param('value', type: null, validate: [new SmallThanColumn(paramName: 'target')])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target', type: null)])]
    public function collect(array $data): void { $this->called = true; }
}
