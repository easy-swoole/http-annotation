<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\DateAfterColumn;
use EasySwoole\HttpAnnotation\Validator\DateBeforeColumn;
use PHPUnit\Framework\TestCase;
use stdClass;

class DateColumnInputTest extends TestCase
{
    public function testInvalidComparisonValuesFailValidation(): void
    {
        foreach ([DateAfterColumn::class, DateBeforeColumn::class] as $ruleClass) {
            foreach (['invalid-date', '', '   ', null, [], ['date'], true, false, 1.5, new stdClass()] as $value) {
                $rule = new $ruleClass('other');
                $param = new Param('date', type: null, value: '2026-01-01', validate: [$rule]);
                $context = new ValidateRequest($param);
                $context->allDefineParams = [
                    'date' => $param,
                    'other' => new Param('other', type: null, value: $value),
                ];
                self::assertFalse($rule->execute($context));
                try {
                    Utility::validateParam($context);
                    self::fail('Invalid input must fail through ParamValidateFail');
                } catch (ParamValidateFail $exception) {
                    self::assertSame('date', $exception->getParamName());
                    self::assertSame($rule, $exception->getFailRule());
                }
            }
        }
    }

    public function testUndefinedComparisonParameterRemainsConfigurationError(): void
    {
        foreach ([DateAfterColumn::class, DateBeforeColumn::class] as $ruleClass) {
            $context = new ValidateRequest(new Param('date', value: '2026-01-01'));
            try {
                (new $ruleClass('missing'))->execute($context);
                self::fail('Undefined parameter must be rejected');
            } catch (Annotation $exception) {
                self::assertStringContainsString('missing', $exception->getMessage());
                self::assertStringContainsString('not define', $exception->getMessage());
            }
        }
    }

    public function testDateStringsAndTenDigitTimestampsRemainSupported(): void
    {
        foreach ([DateAfterColumn::class => '2026-01-02', DateBeforeColumn::class => '2025-12-31'] as $ruleClass => $date) {
            foreach (['2026-01-01', '20260101', strtotime('2026-01-01'), (string)strtotime('2026-01-01')] as $other) {
                $param = new Param('date', type: null, value: $date);
                $context = new ValidateRequest($param);
                $context->allDefineParams = ['other' => new Param('other', type: null, value: $other)];
                self::assertTrue((new $ruleClass('other'))->execute($context));
            }
        }
    }
}
