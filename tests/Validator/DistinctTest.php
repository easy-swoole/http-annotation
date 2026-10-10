<?php

namespace EasySwoole\HttpAnnotation\Tests\Validator;

use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\DistinctInArray;
use EasySwoole\HttpAnnotation\Validator\DistinctInString;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use PHPUnit\Framework\TestCase;

class DistinctTest extends TestCase
{
    public function testStringItems(): void
    {
        $rule = new DistinctInString();
        foreach ([['1,2,3', true], ['1,2,1', false], ['', true], ['1,01', true],
            ['1, 1', true], ['A,a', true], ['1,,', false], [',', false], [null, false],
            [123, false], [[1, 2], false]] as [$value, $expected]) {
            $param = new Param('ids', type: null, value: $value);
            $this->assertSame($expected, $rule->execute(new ValidateRequest($param)));
            $this->assertSame($value, $param->parsedValue());
        }
        $rule = new DistinctInString('||');
        $this->assertTrue($rule->execute(new ValidateRequest(new Param('ids', value: '1||2'))));
        $this->assertFalse($rule->execute(new ValidateRequest(new Param('ids', value: '1||2||1'))));
    }

    public function testArrayItemsAndStrictComparison(): void
    {
        foreach ([[[], true, true], [[1, 2], true, true], [[1, 1], false, false],
            [[1, '1'], false, true], [[null, null], false, false], [['', ''], false, false],
            [['a' => 1, 'b' => 1], false, false], [[[1], [1]], false, false],
            ['1,2', false, false], [null, false, false]] as [$value, $loose, $strict]) {
            $param = new Param('ids', type: null, value: $value);
            $context = new ValidateRequest($param);
            $this->assertSame($loose, (new DistinctInArray())->execute($context));
            $this->assertSame($strict, (new DistinctInArray(true))->execute($context));
            $this->assertSame($value, $param->parsedValue());
        }
    }

    public function testIntegerAndStringHashComparison(): void
    {
        $cases = [[1, '1'], [0, '0'], [-1, '-1'], [1, '+1'], [1, ' 1 '],
            [1, '01'], [1, '1.0'], [100, '1e2'], ['1.0', '1.00'],
            [PHP_INT_MAX, (string)PHP_INT_MAX], ['9007199254740993', '9007199254740992'],
            ['a', 'b'], ['int:1', 1], ['', '0'], [1, '1abc']];
        foreach ([false, true] as $strict) {
            foreach ($cases as $values) {
                $expected = !in_array($values[1], [$values[0]], $strict);
                $context = new ValidateRequest(new Param('ids', type: null, value: $values));
                $this->assertSame($expected, (new DistinctInArray($strict))->execute($context));
            }
            foreach ([[null], [true], [1.0], [[1]]] as $values) {
                $context = new ValidateRequest(new Param('ids', type: null, value: $values));
                $this->assertFalse((new DistinctInArray($strict))->execute($context));
            }
            $values = range(1, 10000);
            $context = new ValidateRequest(new Param('ids', type: null, value: $values));
            $this->assertTrue((new DistinctInArray($strict))->execute($context));
            $values[] = 9999;
            $context = new ValidateRequest(new Param('ids', type: null, value: $values));
            $this->assertFalse((new DistinctInArray($strict))->execute($context));
        }
    }

    public function testJsonAndPostArrays(): void
    {
        foreach ([ParamFrom::JSON, ParamFrom::POST] as $source) {
            foreach ([[1, 2], [1, 2, 1]] as $ids) {
                $request = new Request();
                if ($source === ParamFrom::JSON) {
                    $request->getBody()->write(json_encode(['ids' => $ids]));
                } else {
                    $request->withParsedBody(['ids' => $ids]);
                }
                $param = new Param('ids', from: $source, type: null);
                $this->assertSame($ids, $param->parsedValue($request));
                $this->assertSame(count($ids) === 2, (new DistinctInArray())->execute(new ValidateRequest($param)));
            }
        }
    }

    public function testMessages(): void
    {
        foreach ([new DistinctInString('|'), new DistinctInArray(true)] as $rule) {
            $this->assertStringNotContainsString('{#', $rule->errorMsg('ids'));
            $this->assertStringNotContainsString('{#', $rule->errorMsg('ids', ChineseMap::class));
        }
        $this->assertSame('custom', (new DistinctInString('|', 'custom'))->errorMsg('ids'));
        $this->assertSame('custom', (new DistinctInArray(true, 'custom'))->errorMsg('ids'));
    }

    public function testEmptySeparatorIsRejected(): void
    {
        $this->expectException(Annotation::class);
        new DistinctInString('');
    }
}
