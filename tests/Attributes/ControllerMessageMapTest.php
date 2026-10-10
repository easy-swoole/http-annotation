<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ControllerMessageMapTest extends TestCase
{
    public function testUtilitySingleArgumentUsesDefaultMap(): void
    {
        $param = new Param('ticket', type: null, validate: [new Required()]);
        $param->parsedValue(new Request());
        try {
            Utility::validateParam(new ValidateRequest($param));
            $this->fail('Expected required validation failure');
        } catch (ParamValidateFail $error) {
            $this->assertStringStartsWith('ticket is required', $error->getMessage());
            $this->assertSame('ticket', $error->getParamName());
        }
    }

    #[DataProvider('controllerCases')]
    public function testControllerMapSelectionAndCustomPriority(string $class, string $message): void
    {
        $request = new Request();
        $request->withMethod('GET');
        $controller = new $class($request, new Response(), 'collect');
        try {
            $controller->__hook();
            $this->fail('Expected required validation failure');
        } catch (ParamValidateFail $error) {
            $this->assertStringStartsWith($message, $error->getMessage());
            $this->assertStringContainsString($class, $error->getMessage());
            $this->assertSame('ticket', $error->getParamName());
            $this->assertInstanceOf(Required::class, $error->getFailRule());
            $this->assertFalse($controller->called);
        }
    }

    public static function controllerCases(): array
    {
        return [
            [EnglishMessageController::class, 'ticket is required'],
            [ChineseActionMessageController::class, 'ticket必须传入'],
            [ChinesePublicMessageController::class, 'ticket必须传入'],
            [CustomChineseMessageController::class, '自定义 ticket'],
            // 中文调用后默认英文仍保持独立。
            [EnglishMessageController::class, 'ticket is required'],
        ];
    }
}

class EnglishMessageController extends AnnotationController
{
    public bool $called = false;
    #[Api(requestParam: [new Param('ticket', type: null, validate: [new Required()])])]
    public function collect(array $data): void { $this->called = true; }
}

class ChineseActionMessageController extends EnglishMessageController
{
    protected function __getValidateRuleMapClass(): string { return ChineseMap::class; }
}

class ChinesePublicMessageController extends AnnotationController
{
    public bool $called = false;
    protected function __getValidateRuleMapClass(): string { return ChineseMap::class; }
    #[Param('ticket', type: null, validate: [new Required()])]
    public function onRequest(?string $action): ?bool { return true; }
    #[Api]
    public function collect(array $data): void { $this->called = true; }
}

class CustomChineseMessageController extends ChineseActionMessageController
{
    #[Api(requestParam: [new Param('ticket', type: null, validate: [new Required(errorMsg: '自定义 {#validateParam}')])])]
    public function collect(array $data): void { $this->called = true; }
}
