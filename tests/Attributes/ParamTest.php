<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use PHPUnit\Framework\TestCase;
use EasySwoole\HttpAnnotation\Enum\ParamType;
use PHPUnit\Framework\Attributes\DataProvider;

class ParamTest extends TestCase
{
    #[DataProvider('emptyValueCases')]
    public function testNullWhileEmpty(mixed $input, mixed $expected): void
    {
        foreach ([ParamFrom::GET, ParamFrom::POST] as $source) {
            $request = new Request();
            if ($source === ParamFrom::GET) {
                $request->withQueryParams(['value' => $input]);
            } else {
                $request->withParsedBody(['value' => $input]);
            }
            $param = new Param('value', from: $source, type: ParamType::NULL_WHILE_EMPTY);
            $this->assertSame($expected, $param->parsedValue($request));
            $this->assertSame($input !== null, $param->hasSet());
            $this->assertSame($expected, $param->parsedValue());
        }
    }

    public static function emptyValueCases(): array
    {
        return [
            'null' => [null, null],
            'empty string' => ['', null],
            'false' => [false, null],
            'empty array' => [[], null],
            'float zero' => [0.0, null],
            'integer zero' => [0, 0],
            'string zero' => ['0', '0'],
            'whitespace' => [' ', ' '],
            'text' => ['hello', 'hello'],
            'integer' => [1, 1],
            'true' => [true, true],
            'array' => [[1], [1]],
        ];
    }

    public function testMissingNullWhileEmptyParameter(): void
    {
        $param = new Param('value', type: ParamType::NULL_WHILE_EMPTY);
        $this->assertNull($param->parsedValue(new Request()));
        $this->assertFalse($param->hasSet());
    }

    public function testFileMustBeTheOnlySource(): void
    {
        $this->assertSame([ParamFrom::FILE], (new Param('upload', from: ParamFrom::FILE))->from);
        $this->assertSame([ParamFrom::FILE], (new Param('upload', from: [ParamFrom::FILE]))->from);
        foreach (ParamFrom::cases() as $source) {
            foreach ([[ParamFrom::FILE, $source], [$source, ParamFrom::FILE]] as $sources) {
                try {
                    new Param('upload', from: $sources);
                    $this->fail('Expected multiple FILE sources to be rejected');
                } catch (\EasySwoole\HttpAnnotation\Exception\Annotation $error) {
                    $this->assertStringContainsString('upload', $error->getMessage());
                    $this->assertStringContainsString('FILE must be the only source', $error->getMessage());
                }
            }
        }
    }

    public function testSourcesFollowDeclaredOrder(): void
    {
        $request = new Request();
        $request->withQueryParams(['id' => 'query']);
        $request->withParsedBody(['id' => 'form']);
        $this->assertSame('query', (new Param('id', from: [ParamFrom::GET, ParamFrom::POST]))->parsedValue($request));
        $this->assertSame('form', (new Param('id', from: [ParamFrom::POST, ParamFrom::GET]))->parsedValue($request));
        $request->withQueryParams([]);
        $this->assertSame('form', (new Param('id', from: [ParamFrom::GET, ParamFrom::POST]))->parsedValue($request));
        $this->assertSame('form', (new Param('id', from: [ParamFrom::HEADER, ParamFrom::POST]))->parsedValue($request));
        $this->assertSame([ParamFrom::GET], (new Param('id', from: ParamFrom::GET))->from);
    }
}
