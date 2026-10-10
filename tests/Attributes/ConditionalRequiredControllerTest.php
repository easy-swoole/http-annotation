<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\RequiredWith;
use EasySwoole\HttpAnnotation\Validator\RequiredIf;
use EasySwoole\HttpAnnotation\Validator\RequiredWithout;
use PHPUnit\Framework\TestCase;

class ConditionalRequiredControllerTest extends TestCase
{
    public function testBothStagesUseFinalParameterDefinitions(): void
    {
        foreach ([PublicConditionalRequiredController::class, ActionConditionalRequiredController::class] as $class) {
            foreach ([
                [['target' => 'no', 'email' => 'provided'], null],
                [['target' => 'yes', 'email' => 'provided'], 'company'],
                [['target' => 'no', 'password' => 'provided', 'email' => 'provided'], 'confirm'],
                [['target' => 'no'], 'phone'],
                [['target' => 'yes', 'company' => 'ok', 'password' => 'ok', 'confirm' => 'ok', 'phone' => 'ok'], null],
            ] as [$input, $failedParam]) {
                $request = new Request();
                $request->withMethod('GET');
                $request->withQueryParams($input);
                $controller = new $class($request, new Response(), 'collect');
                try {
                    $controller->__hook();
                    $this->assertNull($failedParam);
                    $this->assertTrue($controller->called);
                } catch (ParamValidateFail $error) {
                    $this->assertSame($failedParam, $error->getParamName());
                    $this->assertFalse($controller->called);
                }
            }
        }
    }

    public function testConflictingAttributeFailsWhenControllerIsScanned(): void
    {
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage('Optional');
        $controller = new ConflictingRequiredController(new Request(), new Response(), 'collect');
        $controller->__hook();
    }
}

class PublicConditionalRequiredController extends AnnotationController
{
    public bool $called = false;

    #[Param('company', type: null, validate: [new RequiredIf('target', 'yes')])]
    #[Param('confirm', type: null, validate: [new RequiredWith(['password'])])]
    #[Param('phone', type: null, validate: [new RequiredWithout(['email'])])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('target'), new Param('password'), new Param('email')])]
    public function collect(array $data): void { $this->called = true; }
}

class ActionConditionalRequiredController extends AnnotationController
{
    public bool $called = false;

    #[Param('target')]
    #[Param('password')]
    #[Param('email')]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [
        new Param('company', type: null, validate: [new RequiredIf('target', 'yes')]),
        new Param('confirm', type: null, validate: [new RequiredWith(['password'])]),
        new Param('phone', type: null, validate: [new RequiredWithout(['email'])]),
    ])]
    public function collect(array $data): void { $this->called = true; }
}

class ConflictingRequiredController extends AnnotationController
{
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('company', validate: [new Optional(), new RequiredIf('target', 'yes')]), new Param('target')])]
    public function collect(array $data): void {}
}
