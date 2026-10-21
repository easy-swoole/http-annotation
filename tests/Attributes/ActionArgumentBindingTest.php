<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ActionArgumentBindingTest extends TestCase
{
    #[DataProvider('bindings')]
    public function testNamedAndUnionArgumentBinding(string $action, array $input, mixed $expected): void
    {
        $request = new Request();
        $request->withMethod('GET');
        $request->withQueryParams($input);
        $controller = new ArgumentBindingController($request, new Response(), $action);
        $controller->__hook();
        $this->assertTrue($controller->called);
        $this->assertSame($expected, $controller->received);
    }

    public static function bindings(): array
    {
        return [
            'union integer' => ['unionValue', ['id' => 12], 12],
            'union string' => ['unionValue', ['id' => 'token'], 'token'],
            'union array receives named field' => ['arrayOrString', ['id' => [1, 2]], [1, 2]],
            'union array string alternative' => ['arrayOrString', ['id' => 'token'], 'token'],
            'multiple named arguments' => ['multiple', ['id' => 12, 'label' => 'name'], [12, 'name']],
            'nullable named type' => ['nullable', [], null],
            'nullable named value' => ['nullable', ['id' => 12], 12],
            'array collection unchanged' => ['collection', ['id' => 12], ['id' => 12]],
            'nullable array collection unchanged' => ['nullableCollection', ['id' => 12], ['id' => 12]],
            'untyped value unchanged' => ['untyped', ['id' => 'token'], 'token'],
            'named array field' => ['namedArray', ['ids' => [1, 2], 'extra' => 'other'], [1, 2]],
            'named empty array' => ['namedArray', ['ids' => []], []],
            'named array default' => ['namedArray', [], [9]],
            'named nullable array' => ['namedNullableArray', ['ids' => [1, 2]], [1, 2]],
            'named nullable missing stays null' => ['namedNullableArray', [], null],
            'public named array' => ['publicArray', ['sharedIds' => [3, 4]], [3, 4]],
        ];
    }
}

class ArgumentBindingController extends AnnotationController
{
    public bool $called = false;
    public mixed $received = null;

    private function receive(mixed $value): void
    {
        $this->called = true;
        $this->received = $value;
    }

    #[Param('sharedIds', type: null, value: [])]
    public function onRequest(?string $action): ?bool { return true; }

    #[Api(requestParam: [new Param('ids', type: null, value: [9]), new Param('extra', type: null)])]
    public function namedArray(array $ids): void { $this->receive($ids); }

    #[Api(requestParam: [new Param('ids', type: null, ignorePassArgWhenNotSet: true)])]
    public function namedNullableArray(?array $ids): void { $this->receive($ids); }

    #[Api]
    public function publicArray(array $sharedIds): void { $this->receive($sharedIds); }

    #[Api(requestParam: [new Param('id', type: null)])]
    public function unionValue(int|string $id): void { $this->receive($id); }

    #[Api(requestParam: [new Param('id', type: null)])]
    public function arrayOrString(array|string $id): void { $this->receive($id); }

    #[Api(requestParam: [new Param('id', type: null), new Param('label', type: null)])]
    public function multiple(int|string $id, string $label): void { $this->receive([$id, $label]); }

    #[Api(requestParam: [new Param('id', type: null)])]
    public function nullable(?int $id): void { $this->receive($id); }

    #[Api(requestParam: [new Param('id', type: null)])]
    public function collection(array $data): void { $this->receive($data); }

    #[Api(requestParam: [new Param('id', type: null)])]
    public function nullableCollection(?array $data): void { $this->receive($data); }

    #[Api(requestParam: [new Param('id', type: null)])]
    public function untyped($id): void { $this->receive($id); }
}
