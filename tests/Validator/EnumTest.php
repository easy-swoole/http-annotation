<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\Enum;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;

class EnumTest extends TestCase
{
    #[DataProvider('values')]
    public function testValues(string $enumClass, bool $strict, mixed $value, bool $expected): void
    {
        $param = new Param('status', type: null, value: $value);
        $this->assertSame($expected, (new Enum($enumClass, $strict))->execute(new ValidateRequest($param)));
        $this->assertSame($value, $param->parsedValue());
    }

    public static function values(): array
    {
        return [
            [IntegerStatus::class, false, 1, true],
            [IntegerStatus::class, false, '1', true],
            [IntegerStatus::class, true, '1', false],
            [IntegerStatus::class, true, 1, true],
            [IntegerStatus::class, false, 0, true],
            [IntegerStatus::class, false, 2, false],
            [IntegerStatus::class, false, 'Active', false],
            [IntegerStatus::class, false, true, false],
            [IntegerStatus::class, false, 1.0, false],
            [IntegerStatus::class, false, null, false],
            [IntegerStatus::class, false, [], false],
            [IntegerStatus::class, true, IntegerStatus::Active, true],
            [IntegerStatus::class, false, StringStatus::Active, false],
            [StringStatus::class, true, 'active', true],
            [StringStatus::class, false, 'Active', false],
            [StringStatus::class, false, 'missing', false],
            [StringStatus::class, false, StringStatus::Active, true],
            [PlainStatus::class, false, 'Active', true],
            [PlainStatus::class, true, 'active', false],
            [PlainStatus::class, false, PlainStatus::Active, true],
            [PlainStatus::class, false, 0, false],
            [EmptyStatus::class, false, 'Active', false],
        ];
    }

    public function testInvalidEnumClass(): void
    {
        foreach ([stdClass::class, 'MissingEnum', ''] as $class) {
            try {
                new Enum($class);
                $this->fail('Expected invalid enum class');
            } catch (Annotation $error) {
                $this->assertStringContainsString('PHP enum', $error->getMessage());
            }
        }
    }

    public function testRequestValues(): void
    {
        foreach ([ParamFrom::JSON, ParamFrom::POST] as $source) {
            $request = new Request();
            if ($source === ParamFrom::JSON) {
                $request->getBody()->write('{"status":1}');
            } else {
                $request->withParsedBody(['status' => '1']);
            }
            $param = new Param('status', from: $source, type: null);
            $param->parsedValue($request);
            $context = new ValidateRequest($param);
            $this->assertTrue((new Enum(IntegerStatus::class))->execute($context));
            $this->assertSame($source === ParamFrom::JSON, (new Enum(IntegerStatus::class, true))->execute($context));
        }
    }

    public function testMessages(): void
    {
        $rule = new Enum(IntegerStatus::class);
        $this->assertStringContainsString(IntegerStatus::class, $rule->errorMsg('status'));
        $this->assertStringNotContainsString('{#', $rule->errorMsg('status', ChineseMap::class));
        $this->assertSame('custom', (new Enum(IntegerStatus::class, true, 'custom'))->errorMsg('status'));
    }
}

enum IntegerStatus: int
{
    case Inactive = 0;
    case Active = 1;
}

enum StringStatus: string
{
    case Active = 'active';
}

enum PlainStatus
{
    case Active;
}

enum EmptyStatus {}
