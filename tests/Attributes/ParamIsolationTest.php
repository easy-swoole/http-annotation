<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\AttributeCache;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Func;
use EasySwoole\HttpAnnotation\Validator\Required;
use PHPUnit\Framework\TestCase;

class ParamIsolationTest extends TestCase
{
    public function testRequestCopiesLeaveTemplateAndRulesUntouched(): void
    {
        $template = new Param('id', type: null, validate: [new Required()]);
        $first = clone $template;
        $request = new Request();
        $request->withQueryParams(['id' => 'first']);
        $this->assertSame('first', $first->parsedValue($request));
        $second = clone $template;
        $this->assertNull($second->parsedValue(new Request()));
        $this->assertFalse($second->hasSet());
        $this->assertNull($template->value);
        $this->assertFalse($template->hasSet());
        $this->assertNotSame($first->validate['Required'], $template->validate['Required']);
        $this->assertNotSame($first->validate['Required'], $second->validate['Required']);
        $first->validate['Required']->errorMsgTpl('request-specific');
        $this->assertNotSame('request-specific', $template->validate['Required']->errorMsg('id'));
    }

    public function testStatefulInterfaceValidatorIsClonedPerRequest(): void
    {
        $callback = new StatefulIsolationValidator();
        $template = new Param('id', type: null, validate: [new Func($callback)]);
        for ($i = 0; $i < 3; $i++) {
            $copy = clone $template;
            $context = new ValidateRequest($copy);
            $this->assertTrue($copy->validate['Func']->execute($context));
            $this->assertFalse($copy->validate['Func']->execute($context));
        }
        $this->assertSame(0, $callback->calls);
    }

    public function testCurrentShallowCloneBoundariesAreExplicit(): void
    {
        $default = (object)['value' => 'original'];
        $template = new Param('id', type: null, value: $default);
        $copy = clone $template;
        // 对象默认值当前仍共享，不代表请求值隔离保证。
        $this->assertSame($template->value, $copy->value);
        $copy->value->value = 'changed';
        $this->assertSame('changed', $template->value->value);

        $parsed = new Param('id', type: null);
        $request = new Request();
        $request->withQueryParams(['id' => 'first']);
        $parsed->parsedValue($request);
        $copy = clone $parsed;
        $request->withQueryParams(['id' => 'second']);
        // 已解析副本不能作为下次请求的未解析模板。
        $this->assertSame('first', $copy->parsedValue($request));
    }

    public function testRepeatedControllerRequestsKeepCacheStableAndReleaseCopies(): void
    {
        $cache = AttributeCache::getInstance();
        $definition = $cache->parseClass(IsolationController::class);
        $template = $definition->apis['collect']->requestParam['id'];
        $run = static function (int $i): IsolationController {
            $request = new Request();
            $request->withMethod('GET');
            $request->withQueryParams(['id' => (string)$i]);
            $controller = new IsolationController($request, new Response(), 'collect');
            $controller->__hook();
            return $controller;
        };
        $warmup = $run(-1);
        unset($warmup);
        gc_collect_cycles();
        $baseline = memory_get_usage(false);
        for ($i = 0; $i < 1000; $i++) {
            $controller = $run($i);
            $this->assertSame(['id' => (string)$i], $controller->received);
            $weak = \WeakReference::create($controller);
            unset($controller);
        }
        gc_collect_cycles();
        $growth = memory_get_usage(false) - $baseline;
        $this->assertNull($weak->get());
        $this->assertSame($definition, $cache->parseClass(IsolationController::class));
        $this->assertNull($template->value);
        $this->assertFalse($template->hasSet());
        $this->assertLessThan(1024 * 1024, $growth, 'Retained memory should remain bounded after warmup');
    }
}

class StatefulIsolationValidator implements ValidateFuncInterface
{
    public int $calls = 0;
    public function execute(ValidateRequest $validateRequest): bool { return ++$this->calls === 1; }
    public function functionName(): string { return 'stateful-isolation'; }
}

class IsolationController extends AnnotationController
{
    public array $received = [];
    #[Api(requestParam: [new Param('id', type: null, validate: [new Required()])])]
    public function collect(array $data): void { $this->received = $data; }
}
