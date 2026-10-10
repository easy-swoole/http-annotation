<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\Validator\AbstractInterface\AbstractValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ValidatorRuleNameTest extends TestCase
{
    #[DataProvider('validators')]
    public function testEveryBuiltinRuleNameIsPublicStaticAndStable(string $class, string $name): void
    {
        $this->assertTrue(is_subclass_of($class, AbstractValidator::class));
        $method = new \ReflectionMethod($class, 'ruleName');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
        $this->assertSame($name, $class::ruleName());
        $this->assertSame($name, $class::ruleName());
    }

    public static function validators(): array
    {
        $cases = [];
        foreach (glob(dirname(__DIR__, 2) . '/src/Validator/*.php') as $file) {
            $name = basename($file, '.php');
            $cases[$name] = ['EasySwoole\\HttpAnnotation\\Validator\\' . $name, $name];
        }
        return $cases;
    }
}
