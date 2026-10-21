<?php

namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\Component\Context\ContextManager;
use EasySwoole\Component\Di;
use EasySwoole\Http\Message\Stream;
use EasySwoole\Http\Request;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Tests\Support\CoroutineRunner;
use PHPUnit\Framework\TestCase;

class ParamSourceSemanticsTest extends TestCase
{
    public function testFieldSourcesKeepNullAndEmptyValueSemantics(): void
    {
        foreach ([ParamFrom::GET, ParamFrom::POST, ParamFrom::COOKIE, ParamFrom::JSON] as $source) {
            foreach ([null, '', false, 0, '0', [], 'text'] as $value) {
                CoroutineRunner::run(function () use ($source, $value): void {
                    $request = new Request();
                    $data = ['value' => $value];
                    switch ($source) {
                        case ParamFrom::GET:
                            $request->withQueryParams($data);
                            break;
                        case ParamFrom::POST:
                            $request->withParsedBody($data);
                            break;
                        case ParamFrom::COOKIE:
                            $request->withCookieParams($data);
                            break;
                        case ParamFrom::JSON:
                            $request->withBody(new Stream(json_encode($data)));
                            break;
                    }
                    $param = new Param('value', from: $source, type: null, value: 'default');
                    $this->assertSame($value === null ? 'default' : $value, $param->parsedValue($request));
                    $this->assertSame($value !== null, $param->hasSet());
                });
            }
        }
    }

    public function testEmptyInjectedValuesStillFallBackToNextSource(): void
    {
        $name = 'source-refactor-test';
        foreach ([ParamFrom::DI, ParamFrom::CONTEXT] as $source) {
            foreach ([null, '', false, 0, '0', [], 'injected'] as $value) {
                CoroutineRunner::run(function () use ($name, $source, $value): void {
                    $request = new Request();
                    $request->withQueryParams([$name => 'query']);
                    if ($source === ParamFrom::DI) {
                        Di::getInstance()->set($name, $value);
                    } else {
                        ContextManager::getInstance()->set($name, $value);
                    }
                    try {
                        $param = new Param($name, from: [$source, ParamFrom::GET], type: null);
                        $this->assertSame(empty($value) ? 'query' : $value, $param->parsedValue($request));
                        $this->assertTrue($param->hasSet());
                    } finally {
                        if ($source === ParamFrom::DI) {
                            Di::getInstance()->delete($name);
                        }
                    }
                });
            }
        }
    }

    public function testHeaderAndRawBodyEmptyValuesAreStillSet(): void
    {
        $request = new Request();
        $request->withHeader('x-value', '');
        $header = new Param('X-Value', from: ParamFrom::HEADER, type: null, value: 'default');
        $this->assertSame('', $header->parsedValue($request));
        $this->assertTrue($header->hasSet());
        $raw = new Param('body', from: ParamFrom::RAW_POST, type: null, value: 'default');
        $this->assertSame('', $raw->parsedValue($request));
        $this->assertTrue($raw->hasSet());
    }
}
