<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Exception\ParamValidateFail;
use EasySwoole\HttpAnnotation\Utility;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Each;
use EasySwoole\HttpAnnotation\Validator\ObjectShape;
use EasySwoole\HttpAnnotation\Validator\EqualWithColumn;
use EasySwoole\HttpAnnotation\Validator\Func;
use EasySwoole\HttpAnnotation\Validator\AbstractInterface\ValidateFuncInterface;
use EasySwoole\HttpAnnotation\Validator\Integer;
use EasySwoole\HttpAnnotation\Validator\Min;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use EasySwoole\HttpAnnotation\Validator\Optional;
use EasySwoole\HttpAnnotation\Validator\Required;
use EasySwoole\HttpAnnotation\Validator\RequiredIf;
use EasySwoole\HttpAnnotation\Validator\MultipleOf;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class NestedValidationTest extends TestCase
{
    private function parse(Param $definition, mixed $value, ?string $map = null): Param
    {
        $param = clone $definition;
        $request = new Request();
        $request->withQueryParams([$param->name => $value]);
        $param->parsedValue($request);
        Utility::validateParam(new ValidateRequest($param, $request), $map ?? ChineseMap::class);
        return $param;
    }

    public function testNestedConversionDefaultsAndUnknownFields(): void
    {
        $definition = new Param('boxes', type: null, validate: [new Each([
            new ObjectShape([
                new Param('id', from: [ParamFrom::HEADER], type: ParamType::INT, validate: [new Required(), new Integer()]),
                new Param('enabled', type: ParamType::BOOLEAN, value: true),
                new Param('optional', validate: [new Optional()], ignorePassArgWhenNotSet: true),
                new Param('detail', type: null, validate: [new ObjectShape([
                    new Param('count', type: ParamType::INT),
                ])]),
            ]),
        ])]);
        $value = [['id' => '0', 'detail' => ['count' => '2'], 'extra' => 'kept']];
        $parsed = $this->parse($definition, $value);
        self::assertSame([['id' => 0, 'detail' => ['count' => 2], 'extra' => 'kept', 'enabled' => true]], $parsed->parsedValue());
        self::assertNull($definition->parsedValue());
        self::assertFalse($definition->hasSet());
    }

    #[DataProvider('invalidStructures')]
    public function testStructuralTypesAreChecked(string $kind, mixed $value): void
    {
        $rule = $kind === 'object' ? new ObjectShape([]) : new Each([]);
        $this->expectException(ParamValidateFail::class);
        $this->parse(new Param('data', type: null, validate: [$rule]), $value);
    }

    public static function invalidStructures(): array
    {
        return [
            ['object', 'text'], ['object', 1], ['object', true], ['object', new stdClass()],
            ['object', [1, 2]], ['list', 'text'], ['list', 1], ['list', false],
            ['list', ['id' => 1]], ['list', [1 => 'a']], ['list', new stdClass()],
        ];
    }

    public function testEmptyContainersAndOptionalMissingObjects(): void
    {
        self::assertSame([], $this->parse(new Param('data', type: null, validate: [new ObjectShape([])]), [])->parsedValue());
        self::assertSame([], $this->parse(new Param('data', type: null, validate: [new Each([])]), [])->parsedValue());
        $definition = new Param('data', type: null, validate: [new ObjectShape([
            new Param('detail', type: null, validate: [new Optional(), new ObjectShape([
                new Param('id', validate: [new Required()]),
            ])], ignorePassArgWhenNotSet: true),
        ])]);
        self::assertSame([], $this->parse($definition, [])->parsedValue());
    }

    public function testNestedFailureReportsFullPathLeafRuleAndMessageMap(): void
    {
        $definition = new Param('boxes', type: null, validate: [new Each([new ObjectShape([
            new Param('detail', type: null, validate: [new ObjectShape([
                new Param('id', type: ParamType::INT, validate: [new Integer('错误字段 {#validateParam}')]),
            ])]),
        ])])]);
        try {
            $this->parse($definition, [['detail' => ['id' => '1']], ['detail' => ['id' => 'bad']]]);
            self::fail('Expected nested validation failure');
        } catch (ParamValidateFail $exception) {
            self::assertSame('boxes[1].detail.id', $exception->getParamName());
            self::assertInstanceOf(Integer::class, $exception->getFailRule());
            self::assertStringStartsWith('错误字段 boxes[1].detail.id', $exception->getMessage());
        }
        try {
            $this->parse(new Param('ids', type: null, validate: [new Each([new Integer()])]), [1, 'bad']);
            self::fail('Expected Chinese error message');
        } catch (ParamValidateFail $exception) {
            self::assertStringStartsWith('ids[1] 必须为整数', $exception->getMessage());
        }
    }

    public function testMissingFieldAndSiblingScope(): void
    {
        $definition = new Param('boxes', type: null, validate: [new Each([new ObjectShape([
            // ref 在后面声明，所有兄弟字段必须先解析，再执行校验。
            new Param('copy', type: ParamType::INT, validate: [new EqualWithColumn('ref', true)]),
            new Param('ref', type: ParamType::INT, validate: [new Required()]),
        ])])]);
        self::assertSame([['copy' => 1, 'ref' => 1], ['copy' => 2, 'ref' => 2]],
            $this->parse($definition, [['copy' => '1', 'ref' => '1'], ['copy' => '2', 'ref' => '2']])->parsedValue());
        try {
            $this->parse(new Param('data', type: null, validate: [new ObjectShape([
                new Param('id', validate: [new Required()]),
            ])]), []);
            self::fail('Expected missing nested field');
        } catch (ParamValidateFail $exception) {
            self::assertSame('data.id', $exception->getParamName());
            self::assertInstanceOf(Required::class, $exception->getFailRule());
        }
    }

    public function testEachClonesStatefulRulesForEveryItemAndRequest(): void
    {
        $callback = new NestedCountingCallback();
        $definition = new Param('items', type: null, validate: [new Each([new Func($callback)])]);
        for ($attempt = 0; $attempt < 3; $attempt++) {
            self::assertSame([1, 2, 3], $this->parse($definition, [1, 2, 3])->parsedValue());
        }
        self::assertSame(0, $callback->calls);
    }

    public function testFailedRequestDoesNotPolluteNextParse(): void
    {
        $definition = new Param('data', type: null, validate: [new ObjectShape([
            new Param('id', type: ParamType::INT, validate: [new Integer(), new Min(1)]),
        ])]);
        try {
            $this->parse($definition, ['id' => 'bad']);
            self::fail('Expected failure');
        } catch (ParamValidateFail) {
            self::assertSame(['id' => 2], $this->parse($definition, ['id' => '2'])->parsedValue());
        }
    }

    public function testDefaultObjectsAreNormalizedWithoutBecomingProvided(): void
    {
        $param = new Param('data', type: null, value: ['id' => '2'], validate: [new ObjectShape([
            new Param('id', type: ParamType::INT, validate: [new Integer()]),
            new Param('label', value: 'default'),
        ])]);
        $param->parsedValue(new Request());
        Utility::validateParam(new ValidateRequest($param));
        self::assertSame(['id' => 2, 'label' => 'default'], $param->parsedValue());
        self::assertFalse($param->hasSet());
        self::assertSame(['id' => '2'], $param->value);
    }

    public function testLocalScopeDoesNotFallBackToTopLevelParameters(): void
    {
        $param = new Param('data', type: null, value: ['copy' => 1], validate: [new ObjectShape([
            new Param('copy', type: null, validate: [new EqualWithColumn('ref')]),
        ])]);
        $context = new ValidateRequest($param, allDefineParams: ['ref' => new Param('ref', value: 1)]);
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage('not define');
        Utility::validateParam($context);
    }

    public function testDefaultMapAndStructuralCustomMessages(): void
    {
        $param = new Param('data', type: null, value: [], validate: [new ObjectShape([
            new Param('id', validate: [new Required()]),
        ])]);
        try {
            Utility::validateParam(new ValidateRequest($param));
            self::fail('Expected default map failure');
        } catch (ParamValidateFail $exception) {
            self::assertStringStartsWith('data.id is required', $exception->getMessage());
        }
        try {
            $this->parse(new Param('data', type: null, validate: [new ObjectShape([], '对象格式错误：{#validateParam}')]), 'bad');
            self::fail('Expected structural failure');
        } catch (ParamValidateFail $exception) {
            self::assertStringStartsWith('对象格式错误：data', $exception->getMessage());
        }
    }

    public function testObjectAndListCannotBeAppliedToTheSameParameter(): void
    {
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage('mutually exclusive');
        new Param('data', validate: [new ObjectShape([]), new Each([])]);
    }

    public function testNestedListsAndTheMaximumSupportedDepth(): void
    {
        $param = new Param('matrix', type: null, validate: [new Each([new Each([new Integer()])])]);
        self::assertSame([[1, 2], [3]], $this->parse($param, [[1, 2], [3]])->parsedValue());
        $rule = new ObjectShape([]);
        $data = [];
        for ($depth = 1; $depth < 32; $depth++) {
            $rule = new ObjectShape([new Param('child', type: null, validate: [$rule])]);
            $data = ['child' => $data];
        }
        self::assertSame($data, $this->parse(new Param('root', type: null, validate: [$rule]), $data)->parsedValue());
    }

    public function testConditionalRequiredUsesTheCurrentObjectAndAcceptsZero(): void
    {
        $definition = new Param('boxes', type: null, validate: [new Each([new ObjectShape([
            new Param('value', type: ParamType::INT, validate: [new RequiredIf('insured', true, true)]),
            new Param('insured', type: ParamType::BOOLEAN),
        ])])]);
        $data = [['value' => '0', 'insured' => 'true'], ['insured' => 'false']];
        self::assertSame([['value' => 0, 'insured' => true], ['insured' => false, 'value' => null]],
            $this->parse($definition, $data)->parsedValue());
        try {
            $this->parse($definition, [['insured' => 'true']]);
            self::fail('Expected conditional required failure');
        } catch (ParamValidateFail $exception) {
            self::assertSame('boxes[0].value', $exception->getParamName());
        }
    }

    public function testNestedEmptyNormalizationPreservesPresenceAndOmission(): void
    {
        $definition = new Param('data', type: null, validate: [new ObjectShape([
            new Param('value', type: ParamType::NULL_WHILE_EMPTY, validate: [new Optional()], ignorePassArgWhenNotSet: true),
        ])]);
        self::assertSame(['value' => null], $this->parse($definition, ['value' => ''])->parsedValue());
        self::assertSame([], $this->parse($definition, [])->parsedValue());
        self::assertSame(['value' => '0'], $this->parse($definition, ['value' => '0'])->parsedValue());
    }

    public function testDimensionsAcceptIntegersAndThreeDecimalPlaces(): void
    {
        $definition = new Param('box', type: null, validate: [new ObjectShape([
            new Param('length', type: ParamType::FLOAT, validate: [new Min(0.001), new MultipleOf(0.001)]),
        ])]);
        foreach ([11, '11', '1.234'] as $value) {
            self::assertSame(['length' => (float)$value], $this->parse($definition, ['length' => $value])->parsedValue());
        }
        $this->expectException(ParamValidateFail::class);
        $this->parse($definition, ['length' => '1.2345']);
    }

    public function testDeepNestingHasAnExplicitLimit(): void
    {
        $rule = new ObjectShape([]);
        $data = [];
        for ($depth = 0; $depth < 33; $depth++) {
            $rule = new ObjectShape([new Param('child', type: null, validate: [$rule])]);
            $data = ['child' => $data];
        }
        $this->expectException(Annotation::class);
        $this->expectExceptionMessage('32');
        $this->parse(new Param('root', type: null, validate: [$rule]), $data);
    }

    #[DataProvider('invalidDefinitions')]
    public function testInvalidNestedDefinitionsAreRejected(string $kind, array $definitions): void
    {
        $this->expectException(Annotation::class);
        $kind === 'object' ? new ObjectShape($definitions) : new Each($definitions);
    }

    public static function invalidDefinitions(): array
    {
        return [
            ['object', ['invalid']], ['object', [new Param('id'), new Param('id')]],
            ['object', [new Param(' ')]], ['object', [new Param('id', ignoreAction: ['test'])]],
            ['list', ['invalid']], ['list', [new Optional(), new Required()]],
        ];
    }
}

class NestedCountingCallback implements ValidateFuncInterface
{
    public int $calls = 0;
    public function execute(ValidateRequest $validateRequest): bool { return ++$this->calls === 1; }
    public function functionName(): string { return 'nested counter'; }
}
