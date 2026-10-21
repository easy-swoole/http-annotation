<?php

namespace EasySwoole\HttpAnnotation\Tests\Document;

use EasySwoole\HttpAnnotation\Attributes\Api;
use EasySwoole\HttpAnnotation\Attributes\ApiGroup;
use EasySwoole\HttpAnnotation\Bean\ClassAttribute;
use EasySwoole\HttpAnnotation\Bean\Example\Raw;
use EasySwoole\HttpAnnotation\Document\Document;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ExampleFormattingTest extends TestCase
{
    private function examples(string $source): array
    {
        $raw = new Raw($source);
        $document = new class(__FILE__, $raw) extends Document {
            public function __construct(string $path, private Raw $raw) { parent::__construct($path, __NAMESPACE__); }
            public function scanAllApiGroup(): array
            {
                $info = new ClassAttribute();
                $api = new Api(requestExamples: [$this->raw], responseExamples: [$this->raw, new Raw($this->raw->toString(), isSuccessResponse: false)]);
                $api->apiName = 'create';
                $api->relateClass = __NAMESPACE__ . '\\Example';
                $info->apis['create'] = $api;
                return ['Example' => ['apiGroup' => new ApiGroup('Example'), 'classAttribute' => $info]];
            }
        };
        $api = $document->scan2ArrayMap()['Example']['apiList']['create'];
        self::assertSame($source, $raw->toString());
        self::assertSame($api['requestExamples'][0], $api['responseExamples']['success'][0]);
        self::assertSame($api['requestExamples'][0], $api['responseExamples']['fail'][0]);
        return $api;
    }

    public function testJsonIsIndentedWithoutChangingItsContents(): void
    {
        // 构造包含引号和反斜杠的合法 JSON 字符串。
        $source = '{"id":123456789012345678901234,"weight":1.2300,"name":' . json_encode('中文, [box]: "test" \\') . ',"data":[{"ok":true}],"empty":{},"list":[],"id":2}';
        $result = $this->examples($source)['requestExamples'][0];
        self::assertStringContainsString("\n    \"id\": 123456789012345678901234,", $result);
        self::assertStringContainsString('"weight": 1.2300', $result);
        self::assertStringContainsString('"empty": {}', $result);
        self::assertStringContainsString('"list": []', $result);
        self::assertSame(2, substr_count($result, '"id":'));
        self::assertSame(json_decode($source, true), json_decode($result, true, flags: JSON_THROW_ON_ERROR));
    }

    public function testXmlIsIndentedAndRemainsValid(): void
    {
        $source = '<?xml version="1.0" encoding="UTF-8"?><request><boxes><box id="1"><name>中文 &amp; 示例</name></box></boxes><empty/></request>';
        $result = $this->examples($source)['requestExamples'][0];
        self::assertStringContainsString("\n  <boxes>\n    <box", $result);
        self::assertStringContainsString('中文 &amp; 示例', $result);
        self::assertSame('中文 & 示例', (string)simplexml_load_string($result)->boxes->box->name);
    }

    #[DataProvider('unchangedExamples')]
    public function testPlainInvalidAndWhitespaceSensitiveContentIsUnchanged(string $source): void
    {
        self::assertSame($source, $this->examples($source)['requestExamples'][0]);
    }

    public static function unchangedExamples(): array
    {
        return [
            ['plain text'], ['a=1&b=2'], ['{"id":'], ['<request><invalid></request>'], [''],
            ['<p>Hello <b>world</b> !</p>'], ['<root xml:space="preserve">  <item>value</item>  </root>'],
            ['<!DOCTYPE root SYSTEM "file:///missing.dtd"><root/>'],
        ];
    }
}
