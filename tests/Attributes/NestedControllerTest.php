<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Tests\Support\CoroutineRunner;
use EasySwoole\HttpAnnotation\Validator\Each;
use EasySwoole\HttpAnnotation\Validator\ObjectShape;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\Min;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\TestCase;

class NestedControllerTest extends TestCase
{
    public function testJsonNestedValuesReachActionAndRequestsRemainIsolated(): void
    {
        foreach ([1, 2] as $id) {
            CoroutineRunner::run(function () use ($id): void {
                $request = new Request();
                $request->withMethod('POST');
                $request->getBody()->write(json_encode(['boxes' => [['id' => (string)$id]], 'flat' => '3']));
                $controller = new NestedJsonController($request, new Response(), 'collect');
                $controller->__hook();
                self::assertSame(['boxes' => [['id' => $id]], 'flat' => 3], $controller->received);
            });
        }
    }

    public function testNestedFailurePreventsActionAndKeepsControllerContext(): void
    {
        CoroutineRunner::run(function (): void {
            $request = new Request();
            $request->withMethod('POST');
            $request->getBody()->write('{"boxes":[{"id":"bad"}],"flat":"3"}');
            $controller = new NestedJsonController($request, new Response(), 'collect');
            try {
                $controller->__hook();
                self::fail('Expected nested input failure');
            } catch (ParamValidateFail $exception) {
                self::assertNull($controller->received);
                self::assertSame('boxes[0].id', $exception->getParamName());
                self::assertStringStartsWith('boxes[0].id 必须为整数', $exception->getMessage());
                self::assertStringContainsString(NestedJsonController::class, $exception->getMessage());
                self::assertStringContainsString('collect', $exception->getMessage());
            }
        });
    }
}

class NestedJsonController extends AnnotationController
{
    public ?array $received = null;

    #[Api(allowMethod: HttpMethod::POST, acceptContentType: ContentType::JSON, requestParam: [
        new Param('boxes', from: [ParamFrom::JSON], type: null, validate: [new Each([new ObjectShape([
            new Param('id', type: ParamType::INT, validate: [new Required(), new Integer(), new Min(1)]),
        ])])]),
        new Param('flat', from: [ParamFrom::JSON], type: ParamType::INT, validate: [new Integer()]),
    ])]
    public function collect(array $params): void { $this->received = $params; }

    protected function __getValidateRuleMapClass(): string { return ChineseMap::class; }
}
