<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\IsBool;
use EasySwoole\HttpAnnotation\Validator\IsFloat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class ParamConversionTest extends TestCase
{
    #[DataProvider('conversionCases')]
    public function testSafeConversion(ParamType $type, mixed $input, mixed $expected): void
    {
        $request = new Request();
        $request->withQueryParams(['value' => $input]);
        $param = new Param('value', type: $type, value: 'default');
        $this->assertSame($expected, $param->parsedValue($request));
        $this->assertSame('default', $param->value);
        $this->assertTrue($param->hasSet());
        $this->assertSame($expected, $param->parsedValue());
    }

    public static function conversionCases(): array
    {
        $cases = [];
        foreach ([['12', 12], ['0', 0], ['-12', -12], [PHP_INT_MAX, PHP_INT_MAX],
            ['abc', 'abc'], ['', ''], ['1.5', '1.5'], ['12abc', '12abc'],
            ['1e2', '1e2'], ['999999999999999999999', '999999999999999999999'],
            [1.5, 1.5], [true, true], [[], []]] as [$input, $expected]) {
            $cases[] = [ParamType::INT, $input, $expected];
        }
        foreach ([ParamType::FLOAT, ParamType::DOUBLE, ParamType::REAL] as $type) {
            foreach ([['1.5', 1.5], ['0', 0.0], [2, 2.0], ['1e2', 100.0],
                ['abc', 'abc'], ['12abc', '12abc'], ['', ''], ['1e999', '1e999'],
                [true, true], [[], []], [INF, INF]] as [$input, $expected]) {
                $cases[] = [$type, $input, $expected];
            }
        }
        foreach ([['true', true], ['TRUE', true], ['false', false], ['FALSE', false],
            ['1', true], [1, true], [true, true], ['0', false], [0, false], [false, false],
            ['abc', 'abc'], ['', ''], ['yes', 'yes'], [2, 2], [1.0, 1.0], [[], []]] as [$input, $expected]) {
            $cases[] = [ParamType::BOOLEAN, $input, $expected];
        }
        foreach ([[12, '12'], [1.5, '1.5'], [false, ''], [true, '1'], ['text', 'text'],
            [[], []], [INF, INF]] as [$input, $expected]) {
            $cases[] = [ParamType::STRING, $input, $expected];
        }
        $object = new stdClass();
        $cases[] = [ParamType::STRING, $object, $object];
        return $cases;
    }

    public function testInvalidValuesFailThroughValidators(): void
    {
        foreach ([[ParamType::INT, 'abc', new Integer()],
            [ParamType::INT, '1.5', new Integer()],
            [ParamType::INT, true, new Integer()],
            [ParamType::INT, false, new Integer()],
            [ParamType::INT, 1.0, new Integer()],
            [ParamType::INT, 0.0, new Integer()],
            [ParamType::BOOLEAN, 'invalid', new IsBool()],
            [ParamType::FLOAT, '12abc', new IsFloat()]] as [$type, $input, $rule]) {
            $request = new Request();
            $request->withQueryParams(['value' => $input]);
            $param = new Param('value', type: $type, validate: [$rule]);
            $this->assertSame($input, $param->parsedValue($request));
            try {
                Utility::validateParam(new ValidateRequest($param));
                $this->fail('Expected validator failure');
            } catch (ParamValidateFail $error) {
                $this->assertSame('value', $error->getParamName());
                $this->assertSame($rule, $error->getFailRule());
            }
        }
    }
}
