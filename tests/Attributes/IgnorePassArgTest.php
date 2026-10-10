<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\HttpAnnotation\AnnotationController;
use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use PHPUnit\Framework\TestCase;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\Min;

class IgnorePassArgTest extends TestCase
{
    public function testOptionalIntegerIsOmittedWhenMissingAndConvertedWhenSet(): void
    {
        foreach ([[], ['queryLimitPeriod' => null], ['queryLimitPeriod' => '5']] as $input) {
            $request = new Request();
            $request->withMethod('GET');
            $request->withQueryParams($input);
            $controller = new OptionalIntegerController($request, new Response(), 'collect');
            $controller->__hook();
            $this->assertSame(isset($input['queryLimitPeriod']) ? ['queryLimitPeriod' => 5] : [], $controller->received);
            $this->assertTrue($controller->called);
        }
        foreach (['', '0', '-1'] as $value) {
            $request = new Request();
            $request->withMethod('GET');
            $request->withQueryParams(['queryLimitPeriod' => $value]);
            $controller = new OptionalIntegerController($request, new Response(), 'collect');
            try {
                $controller->__hook();
                $this->fail('Expected Min validation failure');
            } catch (ParamValidateFail $error) {
                $this->assertInstanceOf(Min::class, $error->getFailRule());
                $this->assertFalse($controller->called);
            }
        }
    }

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

class OptionalIntegerController extends AnnotationController
{
    public array $received = [];
    public bool $called = false;

    #[Api(requestParam: [new Param(
        name: 'queryLimitPeriod',
        from: [ParamFrom::GET],
        validate: [new Optional(), new Integer(), new Min(1)],
        type: ParamType::INT,
        ignorePassArgWhenNotSet: true,
    )])]
    public function collect(array $data): void
    {
        $this->called = true;
        $this->received = $data;
    }
}
