<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Alpha;
use EasySwoole\HttpAnnotation\Validator\AlphaDash;
use EasySwoole\HttpAnnotation\Validator\AlphaNum;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Decimal;
use EasySwoole\HttpAnnotation\Validator\IsFloat;
use EasySwoole\HttpAnnotation\Validator\Timestamp;
use PHPUnit\Framework\TestCase;
use stdClass;

class ValidatorInputBoundaryTest extends TestCase
{
    public function testAlphabetRulesRejectUnsupportedTypes(): void
    {
        foreach ([new Alpha(), new AlphaNum(), new AlphaDash()] as $rule) {
            foreach ([[], ['abc'], new stdClass(), null, true, false, INF, -INF, NAN] as $value) {
                $this->assertRejected($rule, $value);
            }
        }
        foreach ([[new Alpha(), 'abc'], [new AlphaNum(), 'abc123'], [new AlphaNum(), 123],
            [new AlphaNum(), 0], [new AlphaDash(), 'abc-def_ghi']] as [$rule, $value]) {
            self::assertTrue($rule->execute(new ValidateRequest(new Param('value', type: null, value: $value))));
        }
    }

    public function testTimestampRejectsFractionalAndOverflowValues(): void
    {
        $rule = new Timestamp();
        foreach (['1e999', '999999999999999999999', '1.5', '1e2', 1.5, 1.0,
            true, false, [], new stdClass(), null, INF, NAN] as $value) {
            $this->assertRejected($rule, $value);
        }
        foreach ([0, '0', -1, '-1', 1700000000, '1700000000'] as $value) {
            self::assertTrue($rule->execute(new ValidateRequest(new Param('value', type: null, value: $value))));
        }
    }

    public function testFloatRejectsBooleansAndNonFiniteValues(): void
    {
        $rule = new IsFloat();
        foreach ([true, false, INF, -INF, NAN, '1e999', '', [], new stdClass(), null] as $value) {
            $this->assertRejected($rule, $value);
        }
        foreach ([0, 0.0, '0', 1, 1.5, '-1.5', '1e2'] as $value) {
            self::assertTrue($rule->execute(new ValidateRequest(new Param('value', type: null, value: $value))));
        }
    }

    public function testDecimalRejectsUnsupportedTypesForEveryAccuracyMode(): void
    {
        foreach ([null, 0, 2] as $accuracy) {
            foreach ([[], ['1.5'], new stdClass(), true, false, null, INF, -INF, NAN] as $value) {
                $this->assertRejected(new Decimal($accuracy), $value);
            }
        }
        foreach ([[null, 1.5], [0, 2.0], [2, 1.25], [2, '1.25']] as [$accuracy, $value]) {
            self::assertTrue((new Decimal($accuracy))->execute(new ValidateRequest(new Param('value', type: null, value: $value))));
        }
    }

    private function assertRejected(AbstractValidator $rule, mixed $value): void
    {
        $param = new Param('value', type: null, value: $value, validate: [$rule]);
        try {
            Utility::validateParam(new ValidateRequest($param));
            self::fail($rule::ruleName() . ' must reject ' . get_debug_type($value));
        } catch (ParamValidateFail $exception) {
            self::assertSame('value', $exception->getParamName());
            self::assertSame($rule, $exception->getFailRule());
        }
    }
}
