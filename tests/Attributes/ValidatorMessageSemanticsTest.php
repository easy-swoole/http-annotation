<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Validator\Bean\ValidateRequest;
use EasySwoole\HttpAnnotation\Validator\DateFormat;
use EasySwoole\HttpAnnotation\Validator\Decimal;
use EasySwoole\HttpAnnotation\Validator\Money;
use EasySwoole\HttpAnnotation\Validator\IsFile;
use EasySwoole\HttpAnnotation\Validator\Equal;
use EasySwoole\HttpAnnotation\Validator\MsgMap\ChineseMap;
use EasySwoole\HttpAnnotation\Validator\MsgMap\DefaultMap;
use PHPUnit\Framework\TestCase;

class ValidatorMessageSemanticsTest extends TestCase
{
    public function testDateFormatRejectsInvalidDatesAndRendersFormat(): void
    {
        $rule = new DateFormat('Y-m-d');
        foreach (['2024-02-29' => true, '2026-02-31' => false, '2026-02-29' => false,
            '2026-13-01' => false, "2026-01-01\0" => false, 'invalid' => false] as $value => $expected) {
            $this->assertSame($expected, $rule->execute(new ValidateRequest(new Param('date', type: null, value: $value))));
        }
        foreach ([DefaultMap::class, ChineseMap::class] as $map) {
            $message = $rule->errorMsg('date', $map);
            $this->assertStringContainsString('Y-m-d', $message);
            $this->assertStringNotContainsString('{#', $message);
        }
    }

    public function testConstraintPlaceholdersRenderInBothLanguages(): void
    {
        foreach ([new Decimal(2), new Money(2), new IsFile(1024, ['png']), new Equal('value', true)] as $rule) {
            foreach ([DefaultMap::class, ChineseMap::class] as $map) {
                $message = $rule->errorMsg('input', $map);
                $this->assertStringNotContainsString('{#', $message);
                if ($rule instanceof IsFile) {
                    $this->assertStringContainsString('1024', $message);
                    $this->assertStringContainsString('png', $message);
                } elseif ($rule instanceof Equal) {
                    $this->assertStringContainsString('strict=1', $message);
                } else {
                    $this->assertStringContainsString('2', $message);
                }
            }
        }
    }
}
