<?php
namespace EasySwoole\HttpAnnotation\Tests\Attributes;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\Param;
use EasySwoole\HttpAnnotation\Enum\ContentType;
use EasySwoole\HttpAnnotation\Enum\HttpMethod;
use EasySwoole\HttpAnnotation\Enum\ParamFrom;
use EasySwoole\HttpAnnotation\Exception\Annotation;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiTest extends TestCase
{
    public function testDefaultsFollowMethod(): void
    {
        $this->assertNull((new Api())->acceptContentType);
        $this->assertNull((new Api(allowMethod: HttpMethod::HEAD))->acceptContentType);
        $this->assertSame(ContentType::FORM_DATA, (new Api(allowMethod: HttpMethod::POST))->acceptContentType);
    }

    #[DataProvider('definitions')]
    public function testDefinition(HttpMethod $method, ?ContentType $contentType, ParamFrom|array $from, bool $valid): void
    {
        if (!$valid) $this->expectException(Annotation::class);
        $api = new Api(allowMethod: $method, acceptContentType: $contentType,
            requestParam: [new Param(name: 'input', from: $from)]);
        $this->assertArrayHasKey('input', $api->requestParam);
    }

    public static function definitions(): array
    {
        $cases = [];
        foreach ([HttpMethod::GET, HttpMethod::HEAD] as $method) {
            foreach (ParamFrom::cases() as $from) {
                $cases[] = [$method, null, $from, !in_array($from, [ParamFrom::POST, ParamFrom::JSON, ParamFrom::XML, ParamFrom::FILE, ParamFrom::RAW_POST], true)];
            }
            foreach (ContentType::cases() as $type) $cases[] = [$method, $type, ParamFrom::GET, false];
        }
        foreach ([HttpMethod::POST, HttpMethod::PUT, HttpMethod::PATCH, HttpMethod::DELETE, HttpMethod::OPTIONS] as $method) {
            foreach (ContentType::cases() as $type) {
                $allowed = match ($type) {
                    ContentType::FORM_DATA => [ParamFrom::POST, ParamFrom::FILE],
                    ContentType::FORM_URLENCODED => [ParamFrom::POST],
                    ContentType::JSON => [ParamFrom::JSON],
                    ContentType::XML => [ParamFrom::XML],
                    ContentType::RAW => [ParamFrom::RAW_POST],
                };
                foreach ([ParamFrom::POST, ParamFrom::FILE, ParamFrom::JSON, ParamFrom::XML, ParamFrom::RAW_POST] as $from) {
                    $cases[] = [$method, $type, $from, in_array($from, $allowed, true)];
                }
                $cases[] = [$method, $type, ParamFrom::GET, true];
                $cases[] = [$method, $type, ParamFrom::HEADER, true];
            }
        }
        $cases[] = [HttpMethod::POST, ContentType::FORM_DATA, [ParamFrom::GET, ParamFrom::POST], true];
        foreach ([ParamFrom::POST, ParamFrom::FILE, ParamFrom::JSON, ParamFrom::XML, ParamFrom::RAW_POST] as $source) {
            $cases[] = [HttpMethod::GET, null, [ParamFrom::GET, $source], false];
        }
        $cases[] = [HttpMethod::GET, null, [], false];
        $cases[] = [HttpMethod::GET, null, ['GET'], false];
        return $cases;
    }
}
