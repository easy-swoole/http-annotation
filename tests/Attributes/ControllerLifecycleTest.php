<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Component\Di;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Attributes\PreCall;
use EasySwoole\HttpAnnotation\Attributes\Property\Di as InjectDi;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Func;
use PHPUnit\Framework\TestCase;
use stdClass;

class ControllerLifecycleTest extends TestCase
{
    public function testCallbacksCanStopAtTheirOriginalStages(): void
    {
        $dependency = new stdClass();
        Di::getInstance()->set('annotation-lifecycle-test', $dependency);
        try {
            foreach ([
                ['global', ['global']],
                ['method', ['global', 'validate', 'method']],
                ['', ['global', 'validate', 'method', 'onRequest', 'action']],
            ] as [$stop, $expected]) {
                LifecycleHooks::$events = [];
                $request = new Request();
                $request->withMethod('GET');
                $request->withQueryParams(['id' => '42', 'stop' => $stop]);
                $controller = new LifecycleController($request, new Response(), 'collect');
                $controller->__hook();
                $this->assertSame($expected, LifecycleHooks::$events);
                $this->assertSame($stop === '' ? $dependency : null, $controller->dependency);
                $this->assertSame($stop === '' ? ['id' => '42'] : null, $controller->received);
            }
        } finally {
            Di::getInstance()->delete('annotation-lifecycle-test');
        }
    }
}

class LifecycleHooks
{
    public static array $events = [];

    public static function global(string $action, Request $request, Response $response): bool
    {
        self::$events[] = 'global';
        return $request->getQueryParams()['stop'] !== 'global';
    }

    public static function validate(ValidateRequest $context): bool
    {
        self::$events[] = 'validate';
        return $context->validateParam->parsedValue() === '42';
    }

    public static function method(Request $request, Response $response): bool
    {
        self::$events[] = 'method';
        return $request->getQueryParams()['stop'] !== 'method';
    }
}

#[PreCall([LifecycleHooks::class, 'global'])]
class LifecycleController extends AnnotationController
{
    #[InjectDi('annotation-lifecycle-test')]
    public ?stdClass $dependency = null;

    public ?array $received = null;

    public function onRequest(?string $action): ?bool
    {
        LifecycleHooks::$events[] = 'onRequest';
        return $this->dependency !== null;
    }

    #[PreCall([LifecycleHooks::class, 'method'])]
    #[Api(requestParam: [new Param('id', validate: [new Func([LifecycleHooks::class, 'validate'])])])]
    public function collect(array $data): void
    {
        LifecycleHooks::$events[] = 'action';
        $this->received = $data;
    }
}
