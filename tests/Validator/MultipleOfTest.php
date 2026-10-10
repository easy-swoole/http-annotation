<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\MultipleOf;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MultipleOfTest extends TestCase
{
    #[DataProvider('values')]
    public function testMultiples(int|float $multiple, mixed $value, bool $expected): void
    {
        $param = new Param('amount', type: null, value: $value);
        $this->assertSame($expected, (new MultipleOf($multiple))->execute(new ValidateRequest($param)));
        if (is_float($value) && is_nan($value)) {
            $this->assertNan($param->parsedValue());
        } else {
            $this->assertSame($value, $param->parsedValue());
        }
    }

    public static function values(): array
    {
        return [
            [3, 9, true], [3, '9', true], [3, 10, false], [3, 0, true],
            [3, -9, true], [-3, 9, true], [0.1, 0.3, true], [0.1, '0.3', true],
            [0.1, 0.31, false], [0.25, '1.5', true], [2, '1e2', true],
            [2, 9007199254740993, false], [2, '9007199254740993', false],
            [3, PHP_INT_MIN, false], [-1, PHP_INT_MIN, true],
            [2, null, false], [2, true, false], [2, [], false], [2, '', false],
            [2, 'abc', false], [2, INF, false], [2, NAN, false], [2, '1e999', false],
            [1e-300, 1e300, false], [1, 0.000000000001, false],
            [1, 1e-16, false], [1, -1e-16, false],
            [1, '1e-16', false], [1, '-1e-16', false],
            [2, '1e-999', false], [2, '-1e-999', false],
            [2, '0.0001e-999', false], [1e300, 1e-300, false],
            [1e300, -1e-300, false], [0.1, 0.0, true],
            [0.1, -0.0, true], [0.1, '0.0', true],
            [0.1, '0e-999', true], [0.1, ' -0.00e-999 ', true],
            [1e-16, 3e-16, true], [1e-16, -3e-16, true],
        ];
    }

    public function testInvalidMultiples(): void
    {
        foreach ([0, 0.0, INF, -INF, NAN] as $multiple) {
            try {
                new MultipleOf($multiple);
                $this->fail('Expected invalid multiple to be rejected');
            } catch (Annotation $error) {
                $this->assertStringContainsString('non-zero', $error->getMessage());
            }
        }
    }

    public function testMessages(): void
    {
        $rule = new MultipleOf(0.25);
        $this->assertSame('amount must be an integer multiple of 0.25', $rule->errorMsg('amount'));
        $this->assertSame('amount 必须为 0.25 的整数倍', $rule->errorMsg('amount', ChineseMap::class));
        $this->assertSame('custom', (new MultipleOf(3, 'custom'))->errorMsg('amount'));
    }
}
