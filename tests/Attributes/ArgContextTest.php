<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Http\Message\Stream;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Bean\ArgContext;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Tests\Support\CoroutineRunner;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;
use Swoole\Coroutine;
use WeakReference;

class ArgContextTest extends TestCase
{
    #[DataProvider('bodies')]
    public function testParamsShareDecodedBody(ParamFrom $source, string $body, array $expected): void
    {
        CoroutineRunner::run(function () use ($source, $body, $expected): void {
            $stream = new ArgContextStream($body);
            $request = new Request();
            $request->withBody($stream);
            foreach ($expected as $name => $value) {
                $param = new Param($name, from: $source, type: null);
                $this->assertSame($value, $param->parsedValue($request));
            }
            $this->assertSame(1, $stream->reads);
        });
        $this->assertCachesReleased();
    }

    public static function bodies(): array
    {
        return [
            [ParamFrom::JSON, '{"name":"alice","age":20}', ['name' => 'alice', 'age' => 20, 'missing' => null]],
            [ParamFrom::XML, '<root><name>alice</name><age>20</age></root>', ['name' => 'alice', 'age' => '20', 'missing' => null]],
            [ParamFrom::JSON, '', ['first' => null, 'second' => null]],
            [ParamFrom::JSON, '{invalid', ['first' => null, 'second' => null]],
        ];
    }

    public function testConcurrentCoroutinesDoNotShareCachedValues(): void
    {
        $results = [];
        CoroutineRunner::run(function () use (&$results): void {
            foreach (['first', 'second'] as $value) {
                Coroutine::create(function () use ($value, &$results): void {
                    $stream = new ArgContextStream(json_encode(['value' => $value]));
                    $request = new Request();
                    $request->withBody($stream);
                    $first = (new Param('value', from: ParamFrom::JSON, type: null))->parsedValue($request);
                    Coroutine::sleep(0.001);
                    $second = (new Param('value', from: ParamFrom::JSON, type: null))->parsedValue($request);
                    $results[$value] = [$first, $second, $stream->reads];
                });
            }
        });
        $this->assertSame(['first', 'first', 1], $results['first']);
        $this->assertSame(['second', 'second', 1], $results['second']);
        $this->assertCachesReleased();
    }

    public function testFormatsAreSeparateAndOnlyOneCleanupIsRegistered(): void
    {
        CoroutineRunner::run(function (): void {
            $request = new Request();
            $stream = new ArgContextStream('<root><value>xml</value></root>');
            $request->withBody($stream);
            $cache = ArgContext::getInstance();
            $this->assertSame([], $cache->getJson($request));
            $this->assertSame([], $cache->getJson($request));
            $this->assertSame(['value' => 'xml'], $cache->getXml($request));
            $this->assertSame(['value' => 'xml'], $cache->getXml($request));
            $this->assertSame(2, $stream->reads);
            $registered = (new ReflectionProperty(ArgContext::class, 'deferRegistered'))->getValue($cache);
            $this->assertSame([Coroutine::getCid() => true], $registered);
            $weak = WeakReference::create($request);
            unset($request);
            $this->assertNull($weak->get());
        });
        $this->assertCachesReleased();
    }

    public function testCleanupRunsWhenRequestProcessingThrows(): void
    {
        try {
            CoroutineRunner::run(function (): void {
                $request = new Request();
                $request->withBody(new Stream('{"value":1}'));
                ArgContext::getInstance()->getJson($request);
                throw new RuntimeException('request failed');
            });
            $this->fail('Expected request failure');
        } catch (RuntimeException $error) {
            $this->assertSame('request failed', $error->getMessage());
        }
        $this->assertCachesReleased();
    }

    private function assertCachesReleased(): void
    {
        foreach (['cacheJson', 'cacheXml', 'deferRegistered'] as $property) {
            $this->assertSame([], (new ReflectionProperty(ArgContext::class, $property))->getValue(ArgContext::getInstance()), $property);
        }
    }
}

class ArgContextStream extends Stream
{
    public int $reads = 0;

    public function __toString(): string
    {
        $this->reads++;
        return parent::__toString();
    }
}
