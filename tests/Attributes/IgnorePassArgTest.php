<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use PHPUnit\Framework\TestCase;

class IgnorePassArgTest extends TestCase
{
    public function testMissingAndNullAreOmittedButEmptyStringZeroAndFalseRemain(): void
    {
        foreach ([[], ['value' => null], ['value' => ''], ['value' => 0], ['value' => false], ['value' => 'text']] as $input) {
            $request = new Request();
            $request->withMethod('GET');
            $request->withQueryParams($input);
            $controller = new IgnorePassArgController($request, new Response(), 'collect');
            $controller->__hook();
            $expected = isset($input['value']) ? $input : [];
            $this->assertSame($expected, $controller->received);
        }
    }
}

class IgnorePassArgController extends AnnotationController
{
    public array $received = [];

    #[Api(requestParam: [new Param('value', type: null, ignorePassArgWhenNotSet: true)])]
    public function collect(array $data): void
    {
        $this->received = $data;
    }
}
