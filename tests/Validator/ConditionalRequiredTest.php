<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\IsNumeric;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\NotEmpty;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamMiss;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamSet;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValInArray;
use EasySwoole\HttpAnnotation\Validator\OptionalIfParamValNoInArray;
use EasySwoole\HttpAnnotation\Validator\IgnoreValidatorWhenEmpty;
use EasySwoole\HttpAnnotation\Validator\RequiredIf;
use EasySwoole\HttpAnnotation\Validator\RequiredWith;
use EasySwoole\HttpAnnotation\Validator\RequiredWithout;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\TestCase;

class ConditionalRequiredTest extends TestCase
{
    private function context(AbstractValidator $rule, array $input, array $extraRules = []): ValidateRequest
    {
        $request = new Request();
        $request->withQueryParams($input);
        $params = [
            'value' => new Param('value', type: null, validate: [$rule, ...$extraRules]),
            'target' => new Param('target', type: null),
            'other' => new Param('other', type: null),
        ];
        foreach ($params as $param) {
            $param->parsedValue($request);
        }
        return new ValidateRequest($params['value'], $request, $params, 'Example', 'save');
    }

    public function testTriggeredRequiredValues(): void
    {
        foreach ([new RequiredIf('target', 'yes'), new RequiredWith(['target']), new RequiredWithout(['other'])] as $rule) {
            foreach ([null, '', [], 0, '0', false, ' ', 'ok'] as $value) {
                $expected = $value !== null && $value !== '' && $value !== [];
                $context = $this->context($rule, ['target' => 'yes', 'value' => $value]);
                $this->assertSame($expected, $rule->execute($context), $rule::ruleName());
            }
            $this->assertFalse($rule->execute($this->context($rule, ['target' => 'yes'])));
        }
    }

    public function testIfComparisonUsesSetAndParsedValues(): void
    {
        $this->assertTrue((new RequiredIf('target', 'yes'))->execute($this->context(new RequiredIf('target', 'yes'), [])));
        $loose = new RequiredIf('target', 1);
        $strict = new RequiredIf('target', 1, true);
        $this->assertFalse($loose->execute($this->context($loose, ['target' => '1'])));
        $this->assertTrue($strict->execute($this->context($strict, ['target' => '1'])));
        $context = $this->context($strict, ['target' => '1']);
        $request = new Request();
        $request->withQueryParams(['target' => '1']);
        $context->allDefineParams['target'] = new Param('target', type: ParamType::INT);
        $context->allDefineParams['target']->parsedValue($request);
        $this->assertFalse($strict->execute($context));
        $context = $this->context($loose, []);
        $context->allDefineParams['target'] = new Param('target', value: 1);
        $this->assertTrue($loose->execute($context));
    }

    public function testAnyFieldConditions(): void
    {
        $with = new RequiredWith(['target', 'other']);
        $without = new RequiredWithout(['target', 'other']);
        foreach ([[], ['target' => ''], ['target' => []], ['target' => null]] as $input) {
            $this->assertTrue($with->execute($this->context($with, $input)));
            $this->assertFalse($without->execute($this->context($without, $input)));
        }
        foreach ([0, '0', false, ' ', 'yes'] as $value) {
            $this->assertFalse($with->execute($this->context($with, ['other' => $value])));
            $this->assertFalse($without->execute($this->context($without, ['other' => $value])));
            $this->assertTrue($without->execute($this->context($without, ['target' => $value, 'other' => $value])));
        }
    }

    public function testDefaultsDoNotSatisfyRequirement(): void
    {
        $rule = new RequiredWith(['target']);
        $context = $this->context($rule, ['target' => 'yes']);
        $context->validateParam = new Param('value', value: 'default');
        $this->assertFalse($rule->execute($context));
        $context = $this->context($rule, []);
        $context->allDefineParams['target'] = new Param('target', value: 'default');
        $this->assertTrue($rule->execute($context));
    }

    public function testOptionalAndRequiredRulesAreMutuallyExclusive(): void
    {
        $optionalRules = [new Optional(), new IgnoreValidatorWhenEmpty(),
            new OptionalIfParamMiss('target'), new OptionalIfParamSet('target'),
            new OptionalIfParamValInArray('target', ['yes']), new OptionalIfParamValNoInArray('target', ['yes'])];
        foreach ($optionalRules as $optional) {
            foreach ([new NotEmpty(), new Required(), new RequiredIf('target', 'yes'), new RequiredWith(['target']), new RequiredWithout(['other'])] as $rule) {
                foreach ([[$optional, $rule], [$rule, $optional]] as $rules) {
                    try {
                        new Param('value', validate: $rules);
                        $this->fail('Expected incompatible rules to be rejected');
                    } catch (Annotation $error) {
                        foreach (['value', $rule::ruleName(), $optional::ruleName()] as $text) {
                            $this->assertStringContainsString($text, $error->getMessage());
                        }
                    }
                }
            }
        }
    }

    public function testNonEmptyAndRequiredCanBeCombined(): void
    {
        $param = new Param('value', validate: [new Required(), new NotEmpty()]);
        $this->assertSame(['Required', 'NotEmpty'], array_keys($param->validate));
        $param = new Param('value', validate: [new NotEmpty()]);
        $this->assertArrayHasKey('NotEmpty', $param->validate);
    }

    public function testInactiveConditionContinuesFollowingRules(): void
    {
        $rule = new RequiredIf('target', 'yes');
        $context = $this->context($rule, ['target' => 'no', 'value' => 'invalid'], [new IsNumeric()]);
        $this->expectException(ParamValidateFail::class);
        Utility::validateParam($context);
    }

    public function testAllReferencesAreCheckedBeforeEvaluatingCondition(): void
    {
        foreach ([new RequiredIf('missing', 'yes'), new RequiredWith(['target', 'missing']), new RequiredWithout(['other', 'missing'])] as $rule) {
            try {
                $rule->execute($this->context($rule, ['target' => 'yes', 'value' => 'ok']));
                $this->fail('Expected undefined parameter error');
            } catch (Annotation $error) {
                foreach ([$rule::ruleName(), 'value', 'missing', 'Example::save'] as $text) {
                    $this->assertStringContainsString($text, $error->getMessage());
                }
            }
        }
    }

    public function testInvalidNames(): void
    {
        foreach ([fn() => new RequiredIf('', 1), fn() => new RequiredWith([]),
            fn() => new RequiredWithout([]), fn() => new RequiredWith(['']),
            fn() => new RequiredWithout([1])] as $create) {
            try {
                $create();
                $this->fail('Expected invalid configuration');
            } catch (Annotation $error) {
                $this->assertNotEmpty($error->getMessage());
            }
        }
    }

    public function testMessages(): void
    {
        foreach ([new RequiredIf('target', 'yes'), new RequiredWith(['target']), new RequiredWithout(['target'])] as $rule) {
            $this->assertStringNotContainsString('{#', $rule->errorMsg('value'));
            $this->assertStringNotContainsString('{#', $rule->errorMsg('value', ChineseMap::class));
        }
        foreach ([new RequiredIf('target', 1, true, 'custom'), new RequiredWith(['target'], 'custom'), new RequiredWithout(['target'], 'custom')] as $rule) {
            $this->assertSame('custom', $rule->errorMsg('value', ChineseMap::class));
        }
    }
}
