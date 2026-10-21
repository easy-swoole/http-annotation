<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Func;
use EasySwoole\HttpAnnotation\Validator\Min;
use PHPUnit\Framework\TestCase;

class ParameterOverrideValidationTest extends TestCase
{
    public function testOverriddenAndUniqueParamsValidateOncePerRequest(): void
    {
        foreach ([OverrideValidationController::class, IgnoredOverrideValidationController::class] as $class) {
            for ($i = 0; $i < 2; $i++) {
                OverrideCountingValidator::$calls = [];
                $request = new Request();
                $request->withMethod('GET');
                $request->withQueryParams(['id' => '12', 'shared' => '3', 'extra' => '4']);
                $controller = new $class($request, new Response(), 'collect');
                $controller->__hook();
                $this->assertSame(['action-id', 'shared', 'extra'], OverrideCountingValidator::$calls);
                $this->assertSame(['id' => 12, 'shared' => 3], $controller->publicParams);
                $this->assertSame(['id' => 12, 'extra' => 4], $controller->actionParams);
            }
        }
    }

    public function testIgnoredPublicParamWithoutOverrideIsNotValidated(): void
    {
        OverrideCountingValidator::$calls = [];
        $request = new Request();
        $request->withMethod('GET');
        $request->withQueryParams(['id' => '12', 'shared' => '3']);
        $controller = new IgnoredOverrideValidationController($request, new Response(), 'ignored');
        $controller->__hook();
        $this->assertSame(['shared'], OverrideCountingValidator::$calls);
        $this->assertSame(['shared' => 3], $controller->publicParams);
        $this->assertSame([], $controller->actionParams);
    }

    public function testOverriddenRuleFailureStillStopsController(): void
    {
        OverrideCountingValidator::$calls = [];
        $request = new Request();
        $request->withMethod('GET');
        $request->withQueryParams(['id' => '0', 'shared' => '3', 'extra' => '4']);
        $controller = new OverrideValidationController($request, new Response(), 'collect');
        try {
            $controller->__hook();
            $this->fail('Expected overridden Min rule to reject zero');
        } catch (ParamValidateFail $error) {
            $this->assertInstanceOf(Min::class, $error->getFailRule());
            $this->assertSame('id', $error->getParamName());
            $this->assertSame(['action-id'], OverrideCountingValidator::$calls);
            $this->assertNull($controller->publicParams);
            $this->assertNull($controller->actionParams);
        }
    }
}

class OverrideCountingValidator implements ValidateFuncInterface
{
    public static array $calls = [];
    private int $count = 0;

    public function __construct(private string $label) {}

    public function execute(ValidateRequest $validateRequest): bool
    {
        self::$calls[] = $this->label;
        return ++$this->count === 1;
    }

    public function functionName(): string { return $this->label; }
}

class OverrideValidationController extends AnnotationController
{
    public ?array $publicParams = null;
    public ?array $actionParams = null;

    #[Param('id', type: ParamType::STRING, validate: [new Func(new OverrideCountingValidator('public-id'))])]
    #[Param('shared', type: ParamType::INT, validate: [new Func(new OverrideCountingValidator('shared'))])]
    public function onRequest(?string $action, ?array $params = null): ?bool
    {
        $this->publicParams = $params;
        return true;
    }

    #[Api(requestParam: [
        new Param('id', type: ParamType::INT, validate: [new Func(new OverrideCountingValidator('action-id')), new Min(1)]),
        new Param('extra', type: ParamType::INT, validate: [new Func(new OverrideCountingValidator('extra'))]),
    ])]
    public function collect(array $params): void { $this->actionParams = $params; }

    #[Api]
    public function ignored(array $params): void { $this->actionParams = $params; }
}

class IgnoredOverrideValidationController extends OverrideValidationController
{
    #[Param('id', type: ParamType::STRING, ignoreAction: ['collect', 'ignored'], validate: [new Func(new OverrideCountingValidator('public-id'))])]
    #[Param('shared', type: ParamType::INT, validate: [new Func(new OverrideCountingValidator('shared'))])]
    public function onRequest(?string $action, ?array $params = null): ?bool
    {
        return parent::onRequest($action, $params);
    }
}
